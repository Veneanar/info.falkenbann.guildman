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
use wcf\data\wow\character\WowCharacterItemSet;
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
 * @property integer		 $wclassID      PRIMARY KEY
 * @property integer		 $mask
 * @property string		 $powerType			Ressourcentyp
 * @property string		 $name			Klassenname
 * @property string		 $color			Klassenfarbe HMLT Code
 *
 */

final class bnetAPI {

    static private function buildURL($type, $game='wow', array $data = null, array $parameters = []) {
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
                $host = 'http://render-api-eu.worldofwarcraft.com/static-render/eu/';
            }
            elseif (GMAN_BNET_REGION == 'us.api.battle.net') {
                $host = 'http://render-api-us.worldofwarcraft.com/static-render/us/';
            }
            elseif (GMAN_BNET_REGION == 'kr.api.battle.net') {
                $host = 'http://render-api-kr.worldofwarcraft.com/static-render/kr/';
            }
            elseif (GMAN_BNET_REGION == 'tw.api.battle.net') {
                $host = 'http://render-api-tw.worldofwarcraft.com/static-render/tw/';
            }
            else {
                $host = 'http://render-api-us.worldofwarcraft.com/static-render/us/';
            }
            return  $host .'/'.$data[0];
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

    static private function  updatePictures($bnetPath) {
        $images = ['avatar','inset','profilemain'];
        foreach( $images as $image) {
            $path = $image=='avatar' ? $bnetPath : StringUtil::replaceIgnoreCase('avatar',$image, $bnetPath);
            $url = static::buildURL('image', 'wow', [$path]);
            //echo "URL: " . $url; die;
            $request = new HTTPRequest($url);
            try {
                $request->execute();
            }
            catch (HTTPNotFoundException $e) {
                file_put_contents(WCF_DIR . 'log/bnet.log', '*** ERROR *** '. $url . PHP_EOL, FILE_APPEND);
                if (php_sapi_name() === 'cli') echo $image . ':failed ';
                continue;
            }
            $savePath = WCF_DIR . 'images/wow/' . $path;
            if(!file_exists(dirname($savePath))) mkdir(dirname($savePath), 0777, true);
            file_put_contents($savePath, $request->getReply()['body']);
            if (php_sapi_name() === 'cli') echo $image . ':OK ';
            if (ENABLE_DEBUG_MODE) file_put_contents(WCF_DIR . 'log/bnet.log', 'Bild gespeichert: '. $savePath - PHP_EOL, FILE_APPEND);
        }
    }


    static public function updateCharacter(array $updateList) {
        $charDataList = [];
        foreach($updateList as $update) {
            $data = explode("-", $update['charID'], 2);
            $url = static::buildURL('character', 'wow', ['char' => $data[0], 'realm' => $data[1]], ['guild', 'items']);
            $request = new HTTPRequest($url);
            try {
                $request->execute();
            }
            catch (HTTPNotFoundException $e) {
                if (php_sapi_name() === 'cli') echo PHP_EOL .  '*** ERROR ***  '. $update['charID'] . PHP_EOL;
                file_put_contents(WCF_DIR .  'log/bnet.log', '*** ERROR *** '. $url . PHP_EOL, FILE_APPEND);
                continue;
            }
            $reply = $request->getReply();
            $charData=JSON::decode($reply['body'], true);
            if (isset($update['forceUpdate']) || $update['bnetUpdate'] < ($charData['lastModified'] / 1000)) {
                    $charData['charID'] = $update['charID'];
                    $charDataList[] = $charData;
                    if (php_sapi_name() === 'cli') echo PHP_EOL . 'UPDATE: '. $update['charID'] .' ';
                    if (ENABLE_DEBUG_MODE) file_put_contents(WCF_DIR . 'log/bnet.log', 'UPDATE: '. $update['charID'] . PHP_EOL, FILE_APPEND);
                    static::updatePictures($charData['thumbnail']);
             }
            else {
                if (php_sapi_name() === 'cli') echo 'HINT: No update requiered for '. $update['charID'] . PHP_EOL;
                if (ENABLE_DEBUG_MODE) file_put_contents(WCF_DIR . 'log/bnet.log', 'HINT: No update requiered for '. $update['charID'] . PHP_EOL, FILE_APPEND);
            }
        }
       // echo "Charlist: <pre>"; var_dump($charDataList); echo "</pre>"; die;
        $sql = "UPDATE  wcf".WCF_N."_gman_wow_character
            SET     inGuild = ?,
                    bnetData = ?,
                    bnetUpdate = ?,
                    averageItemLevel = ?,
                    averageItemLevelEquipped = ?,
                    head = ?,
                    neck = ?,
                    shoulder = ?,
                    back = ?,
                    chest = ?,
                    shirt = ?,
                    wrist = ?,
                    hands = ?,
                    waist = ?,
                    legs = ?,
                    feet = ?,
                    finger1 = ?,
                    finger2 = ?,
                    trinket1 = ?,
                    trinket2 = ?,
                    mainHand = ?,
                    offHand = ?
            WHERE   charID = ?";

    $statement = WCF::getDB()->prepareStatement($sql);
    WCF::getDB()->beginTransaction();
    foreach ($charDataList as $charData) {
        $plaindata = $charData;
        $plaindata['items'] = null;
        $plaindata['guild'] = null;
        $plaindata['feed'] = null;
        $plaindata['lastModified'] = $plaindata['lastModified'] / 1000;
        // echo "Einzel Gildenliste <pre>"; var_dump($charData); echo "</pre>"; die;
        $statement->execute([
            isset($charData['guild']['name']) ? $charData['guild']['name'] == GMAN_MAIN_GUILDNAME ? 1 : 0 : 0,
            JSON::encode($plaindata),
            TIME_NOW,
            $charData['items']['averageItemLevel'],
            $charData['items']['averageItemLevelEquipped'],
            @static::normalizeItem($charData['items']['head']),
            @static::normalizeItem($charData['items']['neck']),
            @static::normalizeItem($charData['items']['shoulder']),
            @static::normalizeItem($charData['items']['back']),
            @static::normalizeItem($charData['items']['chest']),
            @static::normalizeItem($charData['items']['shirt']),
            @static::normalizeItem($charData['items']['wrist']),
            @static::normalizeItem($charData['items']['hands']),
            @static::normalizeItem($charData['items']['waist']),
            @static::normalizeItem($charData['items']['legs']),
            @static::normalizeItem($charData['items']['feet']),
            @static::normalizeItem($charData['items']['finger1']),
            @static::normalizeItem($charData['items']['finger2']),
            @static::normalizeItem($charData['items']['trinket1']),
            @static::normalizeItem($charData['items']['trinket2']),
            @static::normalizeItem($charData['items']['mainHand']),
            @static::normalizeItem($charData['items']['offHand']),
            $charData['charID']
        ]);
    }
    WCF::getDB()->commitTransaction();

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
                            (charID, isMain, inGuild, realmID, bnetData, groups, bnetUpdate, firstSeen, guildRank)
                VALUES      (?,0,1,?,?,0,?,?,?)
                ON DUPLICATE KEY UPDATE
                            inGuild = VALUES(inGuild),
                            bnetData = VALUES(bnetData),
                            bnetUpdate = VALUES(bnetUpdate)
            ";
        $statement = WCF::getDB()->prepareStatement($sql);

        WCF::getDB()->beginTransaction();

        foreach ($guildmember as $member) {
            $member["character"]['lastModified'] = $member["character"]['lastModified'] / 1000;
            $member["character"]["guild"] = null;

            $statement->execute([
                $member["character"]["name"] . "-". $member["character"]["realm"],
                $member["character"]["realm"],
                JSON::encode($member["character"]),
                TIME_NOW,
                TIME_NOW,
                $member["rank"]
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


