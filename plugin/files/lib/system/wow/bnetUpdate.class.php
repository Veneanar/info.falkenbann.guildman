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
use wcf\data\user\User;
use wcf\data\user\UserEditor;
use wcf\data\user\avatar\UserAvatarAction;
/**
 * Access to the bnetAPI
 * @author	Veneanar Falkenbann
 * @copyright	2017  Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */

final class bnetUpdate {
    const ACHIEVEMENT = 1;
    const CRITERIA = 2;
    const LOOT = 3;
    const BOSSKILL = 4;
    const UNDEFINED = 0;

    static public function updateRaidBosses() {
        $url = bnetAPI::buildURL('zone');
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
        $zoneinfo = JSON::decode($reply['body'], true);
        foreach($zoneinfo['zones'] as $zone) {
            if (isset($zone['id'])) {
                if (isset($zone['bosses'])) {
                    $sql = "INSERT INTO  wcf".WCF_N."_gman_wow_boss
                            (bossID, bossName, zoneID, zoneName, bnetData, bnetUpdate)
                VALUES      (?,?,?,?,?,?)
                ON DUPLICATE KEY UPDATE
                            bnetData = VALUES(bnetData),
                            bnetUpdate = VALUES(bnetUpdate)
                ";
                    $statement = WCF::getDB()->prepareStatement($sql);
                    WCF::getDB()->beginTransaction();
                    foreach($zone['bosses'] as $boss) {
                        $statement->execute([
                            $boss["id"],
                            $boss["name"],
                            $zone["id"],
                            $zone["name"],
                            JSON::encode($boss),
                            TIME_NOW,
                        ]);
                    }
                    WCF::getDB()->commitTransaction();
                    $zone['bosses'] = null;
                }
                $sql = "INSERT INTO  wcf".WCF_N."_gman_wow_zone
                            (zoneID, expansionId, isDungeon, isRaid, zoneName, bnetData, bnetUpdate)
                VALUES      (?,?,?,?,?,?,?)
                ON DUPLICATE KEY UPDATE
                            bnetData = VALUES(bnetData),
                            bnetUpdate = VALUES(bnetUpdate)
            ";
                $statement = WCF::getDB()->prepareStatement($sql);
                $statement->execute([
                    $zone["id"],
                    $zone["expansionId"],
                    $zone["isDungeon"] ? 1 : 0,
                    $zone["isRaid"] ? 1 : 0,
                    $zone["name"],
                    JSON::encode($zone),
                    TIME_NOW,
                ]);
            }
        }
        return true;
    }

    static public function updateSpell(array $spells, $wcfdir = '') {
        if (class_exists('Pool')) {
            $p = new \Pool(8);
            foreach($spells as $spell) {
                if ($spell > 0) $p->submit(new AsyncSpellUpdate($spell, $wcfdir));
            }
            $p->shutdown();
        } else {
            foreach($spells as $spell) {
                if ($spell > 0) {
                    $sync = new SpellUpdate($spell);
                    $sync->run();
                }
            }
        }

    }

    static public function updateGuild() {
        $url = bnetAPI::buildURL('guild');
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
    /**
     * Summary of updateCharacter
     * @param array $updateList
     * @param mixed $wcfdir
     */
    static public function updateCharacter(array $characterList, $forceUpdate, $wcfdir = '') {
        if (class_exists('Pool')) {
            $p = new \Pool(8);
            foreach($characterList as $charUpdate) {
                $p->submit(new AsyncCharacterUpdate($charUpdate, $forceUpdate, $wcfdir));
            }
            $p->shutdown();

        } else {
            foreach($characterList as $charUpdate) {
                $sync = new CharacterUpdate($charUpdate, $forceUpdate);
                $sync->run();
            }
        }
    }

    static public function runCharacterUpdate(WowCharacterEditor $wowChar, array $charData) {
        /**
         * Guild Check ---------------------------------------------------------------------------------------------
         */
        if (isset($charData['guild']['name']) && $charData['guild']['name'] == GMAN_MAIN_GUILDNAME) {
            $charData['inGuild'] = 1;
        }
        else {
            if ($wowChar->inGuild > 0) {
                if ($wowChar->inGuild > 5) {
                    @$action = new WowCharacterAction([$wowChar], 'removeFromGuild');
                    @$action->executeAction();
                    $charData['inGuild'] = 0;
                }
                else {
                    $wowChar->updateCounters(['inGuild' => 1]);
                    $charData['inGuild'] = $wowChar->inGuild +1;
                }
            }
        }
        /**
         * Guild Check ---------------------------------------------------------------------------------------------
         */
        if ($wowChar->isMain > 0 && $wowChar->getOwner()->getUserOption('OverrideAvatar')) {
            $userEditor = new UserEditor($wowChar->getOwner());
            $userAvatarAction = new UserAvatarAction(array(), 'fetchRemoteAvatar', array(
                    'url'           => $wowChar->getAvatar()->getURL(),
                    'userEditor'    => $userEditor
                ));
            $userAvatarAction->executeAction();
        }
        $plaindata = $charData;
        $plaindata['items'] = null;
        $plaindata['feed'] = null;
        $plaindata['pets'] = null;
        $plaindata['statistics'] = null;
        $plaindata['petSlots'] = null;
        $plaindata['mounts'] = null;
        $plaindata['lastModified'] = $plaindata['lastModified'] / 1000;
        $petdata = array_merge($charData['pets'], $charData['petSlots']);
        $petstring = JSON::encode($petdata);
        $accID = '';
        if (strlen($petstring) > 10000)  $accID = hash('ripemd256', JSON::encode($petstring));
        $petstring = '';
        WCF::getDB()->beginTransaction();
        $idlist= [];
        static::updateFeed($wowChar, $charData['feed'], $charData['inGuild']);
        static::updateCharData($wowChar, $plaindata, $accID);
        $idlist = static::updateEquip($wowChar, $plaindata['lastModified'], $charData['items']);
        static::updateMounts($wowChar, $plaindata['lastModified'], $charData['mounts']);
        static::updateStatistics($wowChar, $plaindata['lastModified'], $charData['statistics']);
        static::updatePets($wowChar, $plaindata['lastModified'], $petdata);
        WCF::getDB()->commitTransaction();
        return $idlist;
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
            'appearance' =>             isset($item['appearance'])              ? $item['appearance']       : null,
        ]);
    }
    static public function updateGuildMemberList() {
        $url = bnetAPI::buildURL('guild', 'wow', [], ['members']);
        $request = new HTTPRequest($url);
        @$request->execute();
        @$reply = $request->getReply();
        if (!isset($reply['statusCode']) || $reply['statusCode'] != 200) {
            throw new LoggedException('Cannot connect to battle.net: '.$url.' returns: HTTP: ' . $reply['statusCode']);
        }
        $guildmember = JSON::decode($reply['body'], true)['members'];

        $sql = "UPDATE wcf".WCF_N."_gman_character SET inGuild = inGuild +1 WHERE inGuild >= 1;";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute();

        $sql = "INSERT INTO  wcf".WCF_N."_gman_character
                            (charname, isMain, inGuild, realmSlug, bnetData, bnetUpdate, firstSeen, guildRank, c_class, c_race, c_level, c_acms)
                VALUES      (?,0,1,?,?,?,?,?,?,?,?,?)
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
            $member["character"]["realm"] = isset($member["character"]["realm"]) ? $member["character"]["realm"] : GMAN_MAIN_HOMEREALM;
            $realmObject = WowRealm::getByName($member["character"]["realm"]);
            $realm = $realmObject->slug;
            $statement->execute([
                $member["character"]["name"],
                $realm,
                JSON::encode($member["character"]),
                TIME_NOW,
                TIME_NOW,
                $member["rank"],
            $member["character"]['class'],
            $member["character"]['race'],
            $member["character"]['level'],
            $member["character"]['achievementPoints']
           ]);
        }
        WCF::getDB()->commitTransaction();

        // Prüfe alle Gildenmitglieder auf Rangänderungen und führe die Änderungen durch.
        $guild = GuildRuntimeChache::getInstance()->getCachedObject();
        $rankList = $guild->getRanks();
        foreach ($rankList as $rank) {
            $guildMemberList = new WowCharacterList();
            $guildMemberList->getConditionBuilder()->Add("userID > 0 AND guildRank = ?", [$rank['rankID']]);
            $guildMemberList->readObjects();
            $characterAction = new  WowCharacterAction($guildMemberList->getObjects(), 'setRank', ['rank' => $rank['rankID']]);
            $characterAction->executeAction();
        }

        // Wenn das battle.net mehr als 3 mal den Spieler nciht mehr auflistst, soll der Char aus der Gilde entfernt werden.
        $removeMemberList = new WowCharacterList();
        $removeMemberList->getConditionBuilder()->Add("inGuild > 4");
        $removeMemberList->readObjects();
        $characterAction = new  WowCharacterAction($removeMemberList->getObjects(), 'removeFromGuild');
    }
    static public function updateRealms() {
        $url = bnetAPI::buildURL('realm');
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
    public static function updateCharData(WowCharacterEditor $wowChar, $charData, $accID) {
        $sql = "UPDATE  wcf".WCF_N."_gman_character
            SET     inGuild = ?,
                    bnetData = ?,
                    bnetUpdate = ?,
                    bnetError = 0,
                    c_class = ?,
                    c_race = ?,
                    c_level = ?,
                    c_acms = ?,
                    accountID = ?
            WHERE   characterID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);

        $statement->execute([
            $charData['inGuild'],
            JSON::encode($charData),
            TIME_NOW,
            $charData['class'],
            $charData['race'],
            $charData['level'],
            $charData['achievementPoints'],
            $accID,
            $wowChar->characterID
            ]);
    }
    public static function updateEquip(WowCharacterEditor $wowChar, $updateTime, $itemData) {
        $idlist = [];
        foreach($itemData as $item) {
            if (isset($item['id'])) {
                $idlist[] = [
                    'id'        => $item['id'],
                    'context'   => isset($item['context']) ? $item['context'] : '',
                    'bonusList' => isset($item['bonusLists']) ? $item['bonusLists'] : [],
                ];
            }
        }
        $sql = "INSERT INTO  wcf".WCF_N."_gman_character_equip
                (
                    characterID,
                    averageItemLevel,
                    averageItemLevelEquipped,
                    head,
                    neck,
                    shoulder,
                    back,
                    chest,
                    shirt,
                    wrist,
                    hands,
                    waist,
                    legs,
                    feet,
                    finger1,
                    finger2,
                    trinket1,
                    trinket2,
                    mainHand,
                    offHand,
                    equipTime
                )
           VALUES      (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
           ON DUPLICATE KEY UPDATE
           characterID = VALUES(characterID)
           ";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([
            $wowChar->characterID,
            $itemData['averageItemLevel'],
            $itemData['averageItemLevelEquipped'],
            @static::normalizeItem($itemData['head']),
            @static::normalizeItem($itemData['neck']),
            @static::normalizeItem($itemData['shoulder']),
            @static::normalizeItem($itemData['back']),
            @static::normalizeItem($itemData['chest']),
            @static::normalizeItem($itemData['shirt']),
            @static::normalizeItem($itemData['wrist']),
            @static::normalizeItem($itemData['hands']),
            @static::normalizeItem($itemData['waist']),
            @static::normalizeItem($itemData['legs']),
            @static::normalizeItem($itemData['feet']),
            @static::normalizeItem($itemData['finger1']),
            @static::normalizeItem($itemData['finger2']),
            @static::normalizeItem($itemData['trinket1']),
            @static::normalizeItem($itemData['trinket2']),
            @static::normalizeItem($itemData['mainHand']),
            @static::normalizeItem($itemData['offHand']),
            $updateTime,
        ]);
        return $idlist;
    }
    public static function updateFeed(WowCharacterEditor $wowChar, $feedList, $inGuild) {
        $sql = "INSERT INTO  wcf".WCF_N."_gman_feedlist
                            (characterID, type, itemID, acmID, quantity, bonusLists, context, criteria, feedTime, inGuild)
                VALUES      (?,?,?,?,?,?,?,?,?,?)
                ON DUPLICATE KEY UPDATE
                            characterID = VALUES(characterID)
            ";
        $statement = WCF::getDB()->prepareStatement($sql);
        foreach ($feedList as $feed) {
            $data = static::normalizeFeed($feed, $wowChar->characterID, $inGuild);
            $statement->execute($data);
        }
    }
    public static function updateMounts(WowCharacterEditor $wowChar, $updateTime, $mountData) {
        $sql = "INSERT INTO  wcf".WCF_N."_gman_character_mounts
                            (characterID, bnetData, bnetUpdate)
                VALUES      (?,?,?)
                ON DUPLICATE KEY UPDATE
                            characterID = VALUES(characterID)
            ";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([
            $wowChar->characterID,
            JSON::encode($mountData['collected']),
            $updateTime,
        ]);
    }
    public static function updateStatistics(WowCharacterEditor $wowChar, $updateTime, $statistictData) {
        $guild = GuildRuntimeChache::getInstance()->getCachedObject();
        foreach ($statistictData['subCategories'] as $mainCategory) {
              if (isset($mainCategory['subCategories']))  {
                foreach ($mainCategory['subCategories'] as $subCategory) {
                    if (isset($subCategory['id']) && in_array($subCategory['id'], $guild->getStatisticCategorys())) {
                        $sql = "INSERT INTO  wcf".WCF_N."_gman_char_bosskills
                                            (statID, characterID, killDate, quantity, lastupdate)
                                VALUES      (?,?,?,?,?)
                                ON DUPLICATE KEY UPDATE
                                            quantity = VALUES(quantity),
                                            lastupdate = VALUES(lastupdate)";
                        $statement = WCF::getDB()->prepareStatement($sql);
                        WCF::getDB()->beginTransaction();
                        foreach($subCategory['statistics'] as $statistic) {
                            if (in_array($statistic["id"], $guild->getStatisticIDs())) {
                                $statistic["lastUpdated"] = $statistic["lastUpdated"] / 1000;
                                 $statement->execute([
                                    $statistic["id"],
                                    $wowChar->characterID,
                                    $statistic["lastUpdated"],
                                    $statistic["quantity"],
                                    $statistic["lastUpdated"],
                                ]);
                            }
                        }
                        WCF::getDB()->commitTransaction();
                    }
                }
            }
        }
        $sql = "INSERT INTO  wcf".WCF_N."_gman_character_statistics
                            (characterID, bnetData, bnetUpdate)
                VALUES      (?,?,?)
                ON DUPLICATE KEY UPDATE
                            characterID = VALUES(characterID)
            ";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([
            $wowChar->characterID,
            JSON::encode($statistictData['subCategories']),
            $updateTime,
        ]);
    }
    public static function updatePets(WowCharacterEditor $wowChar, $updateTime, $pettData) {
        $sql = "INSERT INTO  wcf".WCF_N."_gman_character_pets
                            (characterID, bnetData, bnetUpdate)
                VALUES      (?,?,?)
                ON DUPLICATE KEY UPDATE
                            characterID = VALUES(characterID)
            ";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([
            $wowChar->characterID,
            JSON::encode($pettData),
            $updateTime,
        ]);
    }
}


