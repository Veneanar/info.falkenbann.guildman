<?php
namespace wcf\system\wow;
use wcf\system\exception\HTTPNotFoundException;
use wcf\system\exception\Exception;
use wcf\util\HTTPRequest;
use wcf\util\StringUtil;
use wcf\util\JSON;
use wcf\system\WCF;
use wcf\data\wow\character\WowCharacter;
use wcf\data\wow\character\WowCharacterAction;

/**
 * bnetImage short summary.
 *
 * bnetImage description.
 *
 * @version 1.0
 * @author jarau
 */

class SyncCharacterUpdate {
    /**
     * forces an update
     * @var boolean
     */
    public $forceUpdate = false;

    /**
     * battle.net timestamp (local)
     * @var integer
     */
    public $bnetUpdate = 10;

    /**
     *  ID of the character
     * @var string
     */
    public $charID = '';

	/**
     * Initialize the update process
     *
     * @param $charID       string  ID of the character
     * @param $bnetUpdate   integer timestamp of the local data
     * @param $forceUpdate  boolean force an update (optinal)
     */
    public function __construct($charID, $bnetUpdate = 10, $forceUpdate = false) {
        $this->forceUpdate = $forceUpdate;
        $this->bnetUpdate = $bnetUpdate;
        $this->charID = $charID;
    }
    public function run() {
        $data = explode("@", $this->charID, 2);
        //echo "CharID: " . $this->charID . " data: "; var_dump($data); die();
            $url = bnetAPI::buildURL('character', 'wow', ['char' => $data[0], 'realm' => $data[1]], ['guild', 'items', 'feed']);
            $request = new HTTPRequest($url);
            try {
                $request->execute();
            }
            catch (HTTPNotFoundException $e) {
                if (ENABLE_DEBUG_MODE) file_put_contents(WCF_DIR .  'log/bnet.log', '*** ERROR *** '. $url . PHP_EOL, FILE_APPEND);
                $sql = "UPDATE  wcf".WCF_N."_gman_wow_character
                    SET     bnetError = bnetError + 1
                    WHERE   charID = ? ";
                $statement = WCF::getDB()->prepareStatement($sql);
                $statement->execute([$this->charID]);
                return;
            }
            $reply = $request->getReply();
            $charData=JSON::decode($reply['body'], true);
            if (isset($this->forceUpdate) || $this->bnetUpdate < ($charData['lastModified'] / 1000)) {
                $charData['charID'] = $this->charID;
                $charData['bnetError'] = 0;
                if (isset($charData['guild']['name']) && $charData['guild']['name'] == GMAN_MAIN_GUILDNAME) {
                    $charData['inGuild'] = 1;
                }
                else {
                    $action = new WowCharacterAction([new WowCharacter($this->charID)], 'removeFromAllGroups');
                    $action->executeAction();
                    $charData['inGuild'] = 0;
                }
                if (ENABLE_DEBUG_MODE) file_put_contents(WCF_DIR . 'log/bnet.log', 'UPDATE: '. $this->charID . PHP_EOL, FILE_APPEND);
                $images = ['avatar','inset','profilemain'];
                foreach($images as $image) {
                    $path = $image=='avatar' ? $charData['thumbnail'] : StringUtil::replaceIgnoreCase('avatar',$image, $charData['thumbnail']);
                    $imageRequest = new SyncImageDownload($path);
                    $imageRequest->run();
                }
                $plaindata = $charData;
                $plaindata['items'] = null;
                $plaindata['guild'] = null;
                $plaindata['feed'] = null;
                $plaindata['lastModified'] = $plaindata['lastModified'] / 1000;

                WCF::getDB()->beginTransaction();
                $this->updateFeed($this->charID, $charData['feed'], $charData['inGuild']);
                $this->updateCharData($this->charID, $plaindata);
                $this->updateEquip($this->charID, $plaindata['lastModified'], $charData['items']);
                WCF::getDB()->commitTransaction();
            }
            else {
                if (ENABLE_DEBUG_MODE) file_put_contents(WCF_DIR . 'log/bnet.log', 'HINT: No update requiered for '. $this->charID . PHP_EOL, FILE_APPEND);
            }
    }

    private function updateCharData($charID, $charData) {
        $sql = "UPDATE  wcf".WCF_N."_gman_wow_character
            SET     inGuild = ?,
                    bnetData = ?,
                    bnetUpdate = ?,
                    bnetError = 0,
                    c_class = ?,
                    c_race = ?,
                    c_level = ?
            WHERE   charID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);

        $statement->execute([
            $charData['inGuild'],
            JSON::encode($charData),
            TIME_NOW,
            $charData['class'],
            $charData['race'],
            $charData['level'],
            $charID
            ]);
    }
    private function updateEquip($charID, $updateTime, $itemData) {
        $sql = "INSERT INTO  wcf".WCF_N."_gman_character_equip
                (
                    charID,
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
           charID = VALUES(charID)
           ";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([
            $charID,
            $itemData['averageItemLevel'],
            $itemData['averageItemLevelEquipped'],
            @bnetAPI::normalizeItem($itemData['head']),
            @bnetAPI::normalizeItem($itemData['neck']),
            @bnetAPI::normalizeItem($itemData['shoulder']),
            @bnetAPI::normalizeItem($itemData['back']),
            @bnetAPI::normalizeItem($itemData['chest']),
            @bnetAPI::normalizeItem($itemData['shirt']),
            @bnetAPI::normalizeItem($itemData['wrist']),
            @bnetAPI::normalizeItem($itemData['hands']),
            @bnetAPI::normalizeItem($itemData['waist']),
            @bnetAPI::normalizeItem($itemData['legs']),
            @bnetAPI::normalizeItem($itemData['feet']),
            @bnetAPI::normalizeItem($itemData['finger1']),
            @bnetAPI::normalizeItem($itemData['finger2']),
            @bnetAPI::normalizeItem($itemData['trinket1']),
            @bnetAPI::normalizeItem($itemData['trinket2']),
            @bnetAPI::normalizeItem($itemData['mainHand']),
            @bnetAPI::normalizeItem($itemData['offHand']),
            $updateTime,
        ]);
    }
    private function updateFeed($charID, $feedList, $inGuild) {
        $sql = "INSERT INTO  wcf".WCF_N."_gman_feedlist
                            (charID, type, itemID, acmID, quantity, bonusLists, context, criteria, feedTime, inGuild)
                VALUES      (?,?,?,?,?,?,?,?,?,?)
                ON DUPLICATE KEY UPDATE
                            charID = VALUES(charID)
            ";
        $statement = WCF::getDB()->prepareStatement($sql);
        foreach ($feedList as $feed) {
            $data = bnetAPI::normalizeFeed($feed, $charID, $inGuild);
            $statement->execute($data);
        }
    }


}