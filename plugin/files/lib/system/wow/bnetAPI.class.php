<?php
namespace wcf\system\wow;
use wcf\data\guild\Guild;
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
use wcf\data\wow\character\WowCharacterList;
use wcf\system\wow\exception\AuthenticationFailure;
use wcf\system\WCF;
use wcf\util\JSON;
use wcf\util\StringUtil;
use wcf\util\ArrayUtil;
use wcf\system\exception\LoggedException;
use wcf\system\exception\HTTPNotFoundException;
use wcf\system\exception\Exception;
use wcf\system\exception\SystemException;
use wcf\system\cache\runtime\GuildRuntimeChache;
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


    static public function buildURL($type, $game='wow', array $data = [], array $parameters = []) {
        if (GMAN_BNET_KEY == '') throw new AuthenticationFailure('Missing battle.net API Key! Settings -> Gman -> battle.net');
        if ($type=='guild') {
            $querystring = (empty($parameters)) ? '' : '&fields=' . implode(',', $parameters);
            return  'https://'.GMAN_BNET_REGION .'/'.$game.'/'.$type.'/'.rawurlencode(GMAN_MAIN_HOMEREALM).'/'.rawurlencode(GMAN_MAIN_GUILDNAME).'?locale='. GMAN_BNET_LANGUAGE .$querystring.'&apikey='. GMAN_BNET_KEY;
        } elseif ($type=='character') {
            $querystring = (empty($parameters)) ? '' : '&fields=' . implode(',', $parameters);
            return  'https://'.GMAN_BNET_REGION .'/'.$game.'/'.$type.'/'.$data['realm'].'/'.$data['char'].'?locale='. GMAN_BNET_LANGUAGE .$querystring.'&apikey='. GMAN_BNET_KEY;
        } elseif ($type == 'spell') {
            return  'https://'.GMAN_BNET_REGION .'/'.$game.'/'.$type.'/'. $data['id']. '?locale='. GMAN_BNET_LANGUAGE .'&apikey='. GMAN_BNET_KEY;
        } elseif ($type == 'realm') {
            return  'https://'.GMAN_BNET_REGION .'/'.$game.'/'.$type.'/status?locale='. GMAN_BNET_LANGUAGE .'&apikey='. GMAN_BNET_KEY;
        } elseif ($type == 'zone') {
            return  'https://'.GMAN_BNET_REGION .'/'.$game.'/'.$type.'/?locale='. GMAN_BNET_LANGUAGE .'&apikey='. GMAN_BNET_KEY;
        } elseif ($type == 'item') {
            $querystring = empty($parameters) ? '' : '&bl=' . implode(',', $parameters);
            $context = empty($data['context']) ? '' : '/'.$data['context'];
            return  'https://'.GMAN_BNET_REGION .'/'.$game.'/'.$type.'/'. $data['id']. $context .'?locale='. GMAN_BNET_LANGUAGE .$querystring.'&apikey='. GMAN_BNET_KEY;
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

    static public function getItem($id, $con = '', $bl = []) {
        $url = static::buildURL('item', 'wow', ['id' => $id, 'context' => $con], $bl);
        $request = new HTTPRequest($url);
        try {
        	$request->execute();
        }
        catch (HTTPNotFoundException $e) {
                if (ENABLE_DEBUG_MODE) file_put_contents(WCF_DIR . 'log/item.log', '*** ERROR *** '. $url . PHP_EOL, FILE_APPEND);
                return false;
            }
       catch (HTTPException $e) {
           if (ENABLE_DEBUG_MODE) file_put_contents(WCF_DIR . 'log/item.log', '*** ERROR *** '. $url . PHP_EOL, FILE_APPEND);
                return false;
            }
       catch (SystemException $e) {
           if (ENABLE_DEBUG_MODE) @file_put_contents(WCF_DIR . 'log/item.log', '*** ERROR *** Cannot reach media.blizzard.com for '. $url . PHP_EOL, FILE_APPEND);
           return false;
       }
        $reply = $request->getReply();
        if ($reply['statusCode'] != 200) {
            throw new LoggedException('Cannot connect to battle.net: '.$url.' returns: HTTP: ' . $reply['statusCode']);
        }
        $iteminfo = JSON::decode($reply['body'], true);
        if (empty($con) && empty($bonusList)) {
            $itemName = $iteminfo['name'];
            $sql = "INSERT INTO  wcf".WCF_N."_gman_wow_items
                                (itemID, bnetData, bnetUpdate, itemName)
                    VALUES      (?,?,?,?)
                    ON DUPLICATE KEY UPDATE
                                itemID = VALUES(itemID)
                ";
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute([
                    $id,
                    JSON::encode($iteminfo),
                    TIME_NOW,
                    $itemName
            ]);
            $SAVE_ICONS_LOCAL = true;
            if ($SAVE_ICONS_LOCAL) {
                bnetIcon::download($iteminfo['icon'], [18,36,56]);
            }
        }
        else {
            $sql = "INSERT INTO  wcf".WCF_N."_gman_wow_itembonus
                                (itemID, bnetData, bnetUpdate, context, bonus)
                    VALUES      (?,?,?,?,?)
                    ON DUPLICATE KEY UPDATE
                                itemID = VALUES(itemID)
                ";
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute([
                    $id,
                    JSON::encode($iteminfo),
                    TIME_NOW,
                    $con,
                    implode('', $bl),
            ]);
        }
       return true;
    }

    // ALTER TABLE wcf1_gman_feedlist ADD PRIMARY KEY (`charID`, `type`, `feedTime`);

    static public function checkRealm($realm, $isSlug = false) {
       $realmObj = null;
       if ($isSlug) {
           $realmObj = new WowRealm($realm);
           if ($realmObj === null) return ['status'=>0,'msg'=> 'Realm not found'];
       }
       else {
           $realmObj = WowRealm::getByName($realm);
           if ($realmObj === null) return ['status'=>0,'msg'=> 'Realm not found'];
       }
       return ['status'=>1,'slug'=> $realmObj->slug];
    }

    static public function checkCharacter($name, $realm, $isSlug = false) {
        $realmCheck = static::checkRealm($realm, $isSlug);
        if($realmCheck['status']) {
            $objectCheck = new WowCharacter($name .'@' . $realmCheck['slug']);
            if ($objectCheck->name==$name)  return ['status'=>1,'charID'=> $name .'@' . $realmCheck['slug']];
            $url = static::buildURL('character', 'wow', ['realm'=> $realmCheck['slug'], 'char'=>$name]);
            $request = new HTTPRequest($url);
            try {
                $request->execute();
            }
            catch (HTTPNotFoundException $e) {
                return ['status'=>0,'msg'=> 'Character not found'];
            }
            return ['status'=>1,'charID'=> ''];
        }
        else {
            return $realmCheck;
        }
    }

    static public function createCharacter($name, $realm, $isSlug = false) {
        $realm = $isSlug ? $realm : WowRealm::getByName($realm)->slug;
        $sql = "INSERT INTO  wcf".WCF_N."_gman_character
                            (charname, isMain, inGuild, realmSlug, bnetData, bnetUpdate, firstSeen, guildRank)
                VALUES      (?,0,0,?,?,?,?,?)
                ON DUPLICATE KEY UPDATE
                            characterID=LAST_INSERT_ID(characterID)
            ";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([
                $name,
                $realm,
                null,
                0,
                TIME_NOW,
                11,
        ]);
        return WCF::getDB()->getInsertID("wcf".WCF_N."_gman_character", "characterID");
    }


}


