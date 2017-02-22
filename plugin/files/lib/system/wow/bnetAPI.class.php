<?php
namespace wcf\system\wow;
use wcf\data\wow\WowRace;
use wcf\data\wow\WowClasses;
use wcf\data\wow\item\WowItem;
use wcf\data\wow\item\WowItemClasses;
use wcf\data\wow\acm\WowACM;
use wcf\data\wow\acm\GuildWowACM;
use wcf\data\wow\realm\WowRealm;
use wcf\data\wow\character\WowCharacter;
use wcf\data\wow\character\WowCharacterEditor;
use wcf\data\wow\character\WowCharacterAction;
use wcf\system\wow\exception\AuthenticationFailure;
use wcf\system\WCF;
use wcf\util\JSON;
use wcf\util\StringUtil;
use wcf\system\exception\LoggedException;
use wcf\system\exception\HTTPNotFoundException;
use wcf\system\exception\Exception;
use wcf\util\HTTPRequest;
/**
 * Access to the bnetAPI
 * @author	Veneanar Falkenbann
 * @copyright	2017  Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */

final class bnetAPI {
    const ACHIEVEMENT = 1;
    const CRITERIA = 2;
    const LOOT = 3;
    const BOSSKILL = 4;
    const UNDEFINED = 0;

    static public function buildURL($type, $game='wow', array $data = null, array $parameters = []) {
        if (GMAN_BNET_KEY == '') throw new AuthenticationFailure('Missing battle.net API Key! Settings -> Gman -> battle.net');
        $querystring = (empty($parameters)) ? '' : '&fields=';
        foreach($parameters as $option) {
            $querystring .= $option .',';
        }
        if ($type=='guild') {
            return  'https://'.GMAN_BNET_REGION .'/'.$game.'/'.$type.'/'.rawurlencode(GMAN_MAIN_HOMEREALM).'/'.rawurlencode(GMAN_MAIN_GUILDNAME).'?locale='. GMAN_BNET_LANGUAGE .$querystring.'&apikey='. GMAN_BNET_KEY;
        } elseif ($type=='character') {
            return  'https://'.GMAN_BNET_REGION .'/'.$game.'/'.$type.'/'.rawurlencode($data['realm']).'/'.rawurlencode($data['char']).'?locale='. GMAN_BNET_LANGUAGE .$querystring.'&apikey='. GMAN_BNET_KEY;
        } elseif ($type == 'realm') {
            return  'https://'.GMAN_BNET_REGION .'/'.$game.'/'.$type.'/status?locale='. GMAN_BNET_LANGUAGE .'&apikey='. GMAN_BNET_KEY;
        } elseif ($type == 'image') {
            $host = '';
            if (GMAN_BNET_REGION == 'eu.api.battle.net') {
                $host = 'http://render-eu.worldofwarcraft.com/character/';
            }
            elseif (GMAN_BNET_REGION == 'us.api.battle.net') {
                $host = 'http://render-us.worldofwarcraft.com/character/';
            }
            elseif (GMAN_BNET_REGION == 'kr.api.battle.net') {
                $host = 'http://render-kr.worldofwarcraft.com/character/';
            }
            elseif (GMAN_BNET_REGION == 'tw.api.battle.net') {
                $host = 'http://render-tw.worldofwarcraft.com/character/';
            }
            else {
                $host = 'http://render-us.worldofwarcraft.com/character/';
            }
            return  $host .$data[0];
        } else {
            return '';
        }

    }

    static public function updateGuild() {
        $url = static::buildURL('guild');
        $request = new HTTPRequest($url);
        $request->execute();
        $reply = $request->getReply();
        if ($reply['statusCode'] != 200) {
            throw new LoggedException('Cannot connect to battle.net: '.$url.' returns: HTTP: ' . $reply['statusCode']);
        }
        $guildinfo = JSON::decode($reply['body'], true);
        $guildinfo['lastModified'] = $guildinfo['lastModified'] / 1000;
        $sql = "INSERT INTO  wcf".WCF_N."_gman_guild
                            (guildID, birthday, bnetUpdate, bnetData)
                VALUES      (1,?,?,?)
                ON DUPLICATE KEY UPDATE
                            bnetUpdate = VALUES(bnetUpdate),
                            bnetData = VALUES(bnetData)
            ";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([
            TIME_NOW,
            TIME_NOW,
            JSON::encode($guildinfo)
            ]);
    }
    // ALTER TABLE wcf1_gman_feedlist ADD PRIMARY KEY (`charID`, `type`, `feedTime`);

    static public function checkRealm($realm, $isSlug = false) {
       $realmObj = null;
       if ($isSlug) {
           $realmObj = new WowRealm($realm);
           if ($realmObj->getObjectID == 0) return ['status'=>0,'msg'=> 'Realm not found'];
       }
       else {
           $realmObj = WowRealm::getByName($realm);
           if ($realmObj->getObjectID == 0) return ['status'=>0,'msg'=> 'Realm not found'];
       }
       return ['status'=>1,'slug'=> $realmObj->slug];
    }


    static public function checkCharacter($name, $realm, $isSlug = false) {
        $realmCheck = static::checkRealm($realm, $isSlug);
        if($realmCheck['status']) {
            $url = static::buildURL('character');
            $request = new HTTPRequest($url);
            try {
                $request->execute();
            }
            catch (HTTPNotFoundException $e) {
                return ['status'=>0,'msg'=> 'Character not found'];
            }
            return ['status'=>1,'msg'=> 'Character not found'];
        }
        else {
            return $realmCheck;
        }

    }

    static public function createCharacter($name, $realm, $isSlug = false) {
        $realm = $isSlug ? $realm : WowRealm::getByName($realm)->slug;
        $sql = "INSERT INTO  wcf".WCF_N."_gman_wow_character
                            (charID, isMain, inGuild, realmID, bnetData, bnetUpdate, firstSeen, guildRank)
                VALUES      (?,0,0,?,?,?,?,?,?,?,?)
                ON DUPLICATE KEY UPDATE
                            charID = VALUES(charID)
            ";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([
                $name . '@' . $realm,
                $realm,
                null,
                0,
                TIME_NOW,
                11,
        ]);
        return $name . '@' . $realm;
    }

    static public function updateCharacter(array $updateList, $wcfdir = '') {
        if (php_sapi_name() === 'cli') {
            $p = new \Pool(8);
            $i = 0;
            foreach($updateList as $charUpdate) {
                $p->submit(new AsyncCharacterUpdate($charUpdate['charID'], isset($charUpdate['bnetUpdate']) ? $charUpdate['bnetUpdate'] : 10, isset($charUpdate['forceUpdate']) ? $charUpdate['forceUpdate'] : false, $wcfdir));
                $i = $i + 1;
                //if ($i == 1) break;
            }
            $p->shutdown();

        } else {
            foreach($updateList as $charUpdate) {
                $sync = new SyncCharacterUpdate($charUpdate['charID'], isset($charUpdate['bnetUpdate']) ? $charUpdate['bnetUpdate'] : 10, isset($charUpdate['forceUpdate']) ? $charUpdate['forceUpdate'] : false);
                $sync->run();
            }
        }
    }

    static public function normalizeFeed($feedData, $charID, $inGuild) {
        if ($feedData['type']=='ACHIEVEMENT') {
            return [
                $charID,
                static::ACHIEVEMENT,
                0,
                $feedData['achievement']['id'],
                0,
                0,
                0,
                0,
                $feedData['timestamp'] /1000,
                $inGuild
                ];
        }
        elseif($feedData['type']=='CRITERIA') {
            return [
                $charID,
                static::CRITERIA,
                0,
                $feedData['achievement']['id'],
                0,
                0,
                0,
                $feedData['criteria']['id'],
                $feedData['timestamp'] /1000,
                $inGuild
                ];
        }
        elseif($feedData['type']=='LOOT') {
            return [
                $charID,
                static::LOOT,
                $feedData['itemId'],
                0,
                0,
                JSON::encode($feedData['bonusLists']),
                $feedData['context'],
                0,
                $feedData['timestamp'] /1000,
                $inGuild
                ];

        }
        elseif($feedData['type']=='BOSSKILL') {
            return [
                $charID,
                static::BOSSKILL,
                0,
                $feedData['achievement']['id'],
                $feedData['quantity'],
                0,
                0,
                $feedData['criteria']['id'],
                $feedData['timestamp'] /1000,
                $inGuild
                ];
        }
        else {
            return [
                $charID,
                static::UNDEFINED,
                0,
                0,
                0,
                0,
                0,
                JSON::encode($feedData),
                TIME_NOW,
                $inGuild
                ];

        }
    }
    static public function normalizeItem($item = null)  {
        if ($item===null) return NULL;
        return JSON::encode([
            'id' => $item['id'],
            'bonusLists' =>             isset($item['bonusLists'])              ? $item['bonusLists']           : null,
            'tooltipParams' =>          isset($item['tooltipParams'])           ? $item['tooltipParams']        : null,
            'context' =>                isset($item['context'])                 ? $item['context']              : null,
            'artifactId'=>              isset($item['artifactId'])              ? $item['artifactId']           : null,
            'artifactAppearanceId'=>    isset($item['artifactAppearanceId'])    ? $item['artifactAppearanceId'] : null,
            'artifactTraits'=>          isset($item['artifactTraits'])          ? $item['artifactTraits']       : null,
        ]);
    }
    static public function removeFromGuild($charID) {

    }

    static public function updateGuildMemberList() {
        $url = static::buildURL('guild', 'wow', null, ['members']);
        $request = new HTTPRequest($url);
        @$request->execute();
        @$reply = $request->getReply();
        if (!isset($reply['statusCode']) || $reply['statusCode'] != 200) {
            throw new LoggedException('Cannot connect to battle.net: '.$url.' returns: HTTP: ' . $reply['statusCode']);
        }
        $guildmember = JSON::decode($reply['body'], true)['members'];
        $sql = "INSERT INTO  wcf".WCF_N."_gman_wow_character
                            (charID, isMain, inGuild, realmID, bnetData, bnetUpdate, firstSeen, guildRank, c_class, c_race, c_level)
                VALUES      (?,0,1,?,?,?,?,?,?,?,?)
                ON DUPLICATE KEY UPDATE
                            inGuild = VALUES(inGuild),
                            bnetData = VALUES(bnetData),
                            bnetUpdate = VALUES(bnetUpdate),
                            guildRank = VALUES(guildRank)
            ";
        $statement = WCF::getDB()->prepareStatement($sql);

        WCF::getDB()->beginTransaction();

        foreach ($guildmember as $member) {
            $member["character"]['lastModified'] = $member["character"]['lastModified'] / 1000;
            $member["character"]["guild"] = null;
            $realm = WowRealm::getByName($member["character"]["realm"])->slug;
            $statement->execute([
                $member["character"]["name"] . "@". $realm,
                $realm,
                JSON::encode($member["character"]),
                TIME_NOW,
                TIME_NOW,
                $member["rank"],
            $member["character"]['class'],
            $member["character"]['race'],
            $member["character"]['level'],
           ]);
        }
        WCF::getDB()->commitTransaction();
    }


    static public function updateRealms() {
        $url = static::buildURL('realm');
        $request = new HTTPRequest($url);
        $request->execute();
        $reply = $request->getReply();
        if ($reply['statusCode'] != 200) {
            throw new LoggedException('Cannot connect to battle.net: '.$url.' returns: HTTP: ' . $reply['statusCode']);
        }
        $realmlist = JSON::decode($reply['body'], true)['realms'];
        //echo "Komplett Realmlist <pre>"; var_dump($realmlist); echo "</pre>";
        $sql = "INSERT INTO  wcf".WCF_N."_gman_wow_realm
                            (name, type, population, queue, status, battlegroup, timezone, connected_realms, slug, locale, lastUpdate)
                VALUES      (?,?,?,?,?,?,?,?,?,?,?)
                ON DUPLICATE KEY UPDATE
                            population = VALUES(population),
                            queue = VALUES(queue),
                            status = VALUES(status),
                            battlegroup = VALUES(battlegroup),
                            connected_realms = VALUES(connected_realms),
                            lastUpdate = VALUES(lastUpdate)
            ";
        $statement = WCF::getDB()->prepareStatement($sql);
        WCF::getDB()->beginTransaction();
        foreach ($realmlist as $realm) {
            $statement->execute([
                $realm["name"],
                $realm["type"],
                $realm["population"],
                $realm["queue"] ?: 0,
                $realm["status"],
                $realm["battlegroup"],
                $realm["timezone"],
                JSON::encode($realm["connected_realms"]),
                $realm["slug"],
                $realm["locale"],
                TIME_NOW
           ]);
        }
        WCF::getDB()->commitTransaction();
    }


}


