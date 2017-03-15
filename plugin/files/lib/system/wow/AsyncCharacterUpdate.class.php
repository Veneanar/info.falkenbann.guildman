<?php
namespace wcf\system\wow;
use wcf\system\exception\HTTPNotFoundException;
// use wcf\system\exception\Exception;
use wcf\util\HTTPRequest;
use wcf\util\StringUtil;
use wcf\util\JSON;
use wcf\system\WCF;
use wcf\data\wow\character\WowCharacter;
use wcf\data\wow\character\WowCharacterAction;
use Zend\Loader\StandardAutoloader;

/**
 * Updates a given WoW Character
 * @author	Veneanar Falkenbann
 * @copyright	2017  Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */

class AsyncCharacterUpdate extends \Thread{
    /**
     * forces an update
     * @var boolean
     */
    public $forceUpdate = false;

    /**
     * timestamp of the local data
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
    public function __construct($charID, $bnetUpdate = 10, $forceUpdate = false, $wcfdir) {
        //require_once($wcfdir . "lib/system/WCF.class.php");
        //new \wcf\system\WCF();
        if (!defined('WCF_DIR')) define('WCF_DIR', $wcfdir);
        require_once($wcfdir.'lib/system/WCF.class.php');
		require_once(WCF_DIR.'lib/system/api/zend/Loader/StandardAutoloader.php');
		$zendLoader = new StandardAutoloader([StandardAutoloader::AUTOREGISTER_ZF => true]);
		$zendLoader->register();
		require_once(WCF_DIR.'lib/util/HTTPRequest.class.php');
		require_once(WCF_DIR.'lib/util/StringUtil.class.php');
		require_once(WCF_DIR.'lib/util/JSON.class.php');
		require_once(WCF_DIR.'lib/data/wow/character/WowCharacter.class.php');
		require_once(WCF_DIR.'lib/data/wow/character/WowCharacterEditor.class.php');
        require_once(WCF_DIR.'lib/data/wow/character/WowCharacterAction.class.php');
        require_once(WCF_DIR.'lib/system/wow/AsyncImageDownload.class.php');
        $this->forceUpdate = $forceUpdate;
        $this->bnetUpdate = $bnetUpdate;
        $this->charID = $charID;
    }

	/**
     * runs the update process asnc
     *
     */
    public function run() {
        new \wcf\system\WCF();
        $data = explode("@", $this->charID, 2);
        $url = bnetAPI::buildURL('character', 'wow', ['char' => $data[0], 'realm' => $data[1]], ['guild', 'items', 'feed', 'statistics', 'stats', 'petSlots', 'pets', 'mounts' ]);
        $reply = null;
        $charData['inGuild'] = 0;
        try {
            $reply = @file_get_contents($url);
            if ($reply === false) {
                echo  "UPDATE ". $this->charID .":\033[31m ERROR Url not found \033[0m (". $url .")". PHP_EOL;
                $sql = "UPDATE  wcf".WCF_N."_gman_character
                    SET     bnetError = bnetError + 1
                    WHERE   charID = ? ";
                $statement = WCF::getDB()->prepareStatement($sql);
                $statement->execute([$this->charID]);
                return;
            }
        }
        catch (Exception $e) {
            echo "UPDATE ". $this->charID .": \033[31m ERROR Url not found \033[0m (". $url .")". PHP_EOL;
            $sql = "UPDATE  wcf".WCF_N."_gman_character
                    SET     bnetError = bnetError + 1
                    WHERE   charID = ? ";
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute([$this->charID]);
            return;
        }
        $charData=JSON::decode($reply, true);
        if (isset($this->forceUpdate) || $this->update['bnetUpdate'] < ($charData['lastModified'] / 1000)) {
            $charData['charID'] = $this->charID;
            $charData['bnetError'] = 0;
            if (isset($charData['guild']['name']) && $charData['guild']['name'] == GMAN_MAIN_GUILDNAME) {
                $charData['inGuild'] = 1;
            }
            else {
                //$wowObj = new WowCharacter($this->charID);
                //@$action = new WowCharacterAction([$wowObj], 'removeFromAllGroups');
                //@$action->executeAction();
                $charData['inGuild'] = 2;
                echo $this->charID .": \033[31m removed from guild\033[0m (". $url .")". PHP_EOL;
            }

            $images = ['avatar','inset','profilemain'];
            foreach($images as $image) {
                $path = $image=='avatar' ? $charData['thumbnail'] : StringUtil::replaceIgnoreCase('avatar',$image, $charData['thumbnail']);
                $imageRequest = new AsyncImageDownload($path, $this->charID);
                $imageRequest->run();
            }
            $plaindata = $charData;
            $plaindata['items'] = null;
            $plaindata['guild'] = null;
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
            $this->updateFeed($this->charID, $charData['feed'], $charData['inGuild']);
            $this->updateCharData($this->charID, $plaindata, $accID);
            $this->updateEquip($this->charID, $plaindata['lastModified'], $charData['items']);
            $this->updateMounts($this->charID, $plaindata['lastModified'], $charData['mounts']);
            $this->updateStatistics($this->charID, $plaindata['lastModified'], $charData['statistics']);
            $this->updatePets($this->charID, $plaindata['lastModified'], $petdata);
            WCF::getDB()->commitTransaction();
            echo "UPDATE ". $this->charID .": \033[32m update done. \033[0m" . PHP_EOL;
        }
        else {
            echo "UPDATE ". $this->charID .": no update requiered" . PHP_EOL;
        }
    }


    private function updateCharData($charID, $charData, $accID) {
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
            WHERE   charID = ?";
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
    private function updateMounts($charID, $updateTime, $mountData) {
        $sql = "INSERT INTO  wcf".WCF_N."_gman_character_mounts
                            (charID, bnetData, bnetUpdate)
                VALUES      (?,?,?)
                ON DUPLICATE KEY UPDATE
                            charID = VALUES(charID)
            ";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([
            $charID,
            JSON::encode($mountData['collected']),
            $updateTime,
        ]);
    }
    private function updateStatistics($charID, $updateTime, $statistictData) {
        $sql = "INSERT INTO  wcf".WCF_N."_gman_character_statistics
                            (charID, bnetData, bnetUpdate)
                VALUES      (?,?,?)
                ON DUPLICATE KEY UPDATE
                            charID = VALUES(charID)
            ";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([
            $charID,
            JSON::encode($statistictData['subCategories']),
            $updateTime,
        ]);
    }
    private function updatePets($charID, $updateTime, $pettData) {
        $sql = "INSERT INTO  wcf".WCF_N."_gman_character_pets
                            (charID, bnetData, bnetUpdate)
                VALUES      (?,?,?)
                ON DUPLICATE KEY UPDATE
                            charID = VALUES(charID)
            ";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([
            $charID,
            JSON::encode($pettData),
            $updateTime,
        ]);
    }
}