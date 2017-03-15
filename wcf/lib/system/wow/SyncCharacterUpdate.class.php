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
     * @var Array
     */
    public $charInfo = [];

	/**
     * Initialize the update process
     *
     * @param $charData       string  ID of the character
     * @param $bnetUpdate   integer timestamp of the local data
     * @param $forceUpdate  boolean force an update (optinal)
     */
    public function __construct(array $charInfo, $bnetUpdate = 10, $forceUpdate = false) {
        $this->forceUpdate = $forceUpdate;
        $this->bnetUpdate = $bnetUpdate;
        $this->charInfo = $charInfo;
    }
    public function run() {
        //echo "CharID: " . $this->charID . " data: "; var_dump($data); die();
        $url = bnetAPI::buildURL('character', 'wow', ['char' => $this->charInfo['name'], 'realm' => $this->charInfo['realm']], ['guild', 'items', 'feed', 'statistics', 'stats', 'petSlots', 'pets', 'mounts' ]);
            $request = new HTTPRequest($url);
            try {
                $request->execute();
            }
            catch (HTTPNotFoundException $e) {
                if (ENABLE_DEBUG_MODE) file_put_contents(WCF_DIR .  'log/bnet.log', '*** ERROR *** '. $url . PHP_EOL, FILE_APPEND);
                $sql = "UPDATE  wcf".WCF_N."_gman_character
                    SET     bnetError = bnetError + 1
                    WHERE   characterID = ? ";
                $statement = WCF::getDB()->prepareStatement($sql);
                $statement->execute([$this->charInfo['id']]);
                return;
            }
            $reply = $request->getReply();
            $charData=JSON::decode($reply['body'], true);
            if (isset($this->forceUpdate) || $this->bnetUpdate < ($charData['lastModified'] / 1000)) {
                $charData['bnetError'] = 0;
                if (isset($charData['guild']['name']) && $charData['guild']['name'] == GMAN_MAIN_GUILDNAME) {
                    $charData['inGuild'] = 1;
                }
                else {
                    $charData['inGuild'] = 2;
                }
                if (ENABLE_DEBUG_MODE) file_put_contents(WCF_DIR . 'log/bnet.log', 'UPDATE: '. $this->charInfo['name'] .'(' .$this->charInfo['id'] .')' . PHP_EOL, FILE_APPEND);
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
                bnetAPI::updateFeed($this->charInfo, $charData['feed'], $charData['inGuild']);
                bnetAPI::updateCharData($this->charInfo, $plaindata, $accID);
                bnetAPI::updateEquip($this->charInfo, $plaindata['lastModified'], $charData['items']);
                bnetAPI::updateMounts($this->charInfo, $plaindata['lastModified'], $charData['mounts']);
                bnetAPI::updateStatistics($this->charInfo, $plaindata['lastModified'], $charData['statistics']);
                bnetAPI::updatePets($this->charInfo, $plaindata['lastModified'], $petdata);
                WCF::getDB()->commitTransaction();
            }
            else {
                if (ENABLE_DEBUG_MODE) file_put_contents(WCF_DIR . 'log/bnet.log', 'HINT: No update requiered for '. $this->charInfo['name'] .'(' .$this->charInfo['id'] .')'. PHP_EOL, FILE_APPEND);
            }
    }



}