<?php
namespace wcf\system\wow;
use wcf\system\exception\HTTPNotFoundException;
use wcf\system\exception\Exception;
use wcf\util\HTTPRequest;
use wcf\util\StringUtil;
use wcf\util\JSON;
use wcf\system\WCF;
use wcf\data\wow\item\WowItem;
use wcf\data\wow\item\ViewableWowItem;
use wcf\data\wow\character\WowCharacter;
use wcf\data\wow\character\WowCharacterEditor;
use wcf\data\wow\character\WowCharacterAction;

/**
 * Updates a given WoW Character
 * @author	Veneanar Falkenbann
 * @copyright	2017  Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */

class CharacterUpdate {
    /**
     * forces an update
     * @var boolean
     */
    public $forceUpdate = false;

    /**
     *  ID of the character
     * @var WowCharacterEditor
     */
    public $char = null;

	/**
     * Initialize the update process
     *
     * @param $char         WowCharacter  ID of the character
     * @param $forceUpdate  boolean force an update (optinal)
     */
    public function __construct($char, $forceUpdate = false) {
        $this->forceUpdate = $forceUpdate;
        if (get_class($char) == 'wcf\data\wow\character\WowCharacterEditor') {
            $this->char = $char;
        }
        else {
            $this->char = new WowCharacterEditor($char);
        }
    }

    /**
     * checks if an update is needed
     * @return bool
     */
    private function checkUpdate() {
        if ($this->forceUpdate) return true;
        $url = bnetAPI::buildURL('character', 'wow', ['char' => $this->char->charname, 'realm' => $this->char->realmSlug]);
        $data = $this->getData($url);
        if (!isset($data['lastModified'])) return true;
        if ($this->char->bnetUpdate < ($data['lastModified'] / 1000)) return true;
        return false;
    }

    /**
     * trys to get data
     * @param mixed $url
     * @return array|null
     */
    private function getData($url) {
        $reply = '';
        $request = new HTTPRequest($url, ['timeout' => 25]);
        try {
            $request->execute();
        }
        catch (HTTPNotFoundException $e) {
            $this->char->updateCounters(['bnetError' => 1]);
            if (ENABLE_DEBUG_MODE) file_put_contents(WCF_DIR .  'log/bnet.log', '*** ERROR *** '. $url . PHP_EOL, FILE_APPEND);
            return null;
        }
        catch (HTTPServerErrorException $e) {
            return null;
        }
        catch (SystemException $e) {
            return null;
        }
        $reply = $request->getReply();
        return JSON::decode($reply['body'], true);
    }
    /**
     * executes Update
     * @return void
     */
    public function run() {
        if (!$this->checkUpdate()) {
            if (ENABLE_DEBUG_MODE) file_put_contents(WCF_DIR . 'log/bnet.log', 'HINT: No update requiered for '. $this->char->charname . '('.$this->char->characterID.')'. PHP_EOL, FILE_APPEND);
            return;
        }
         $charData = [];
         $url = bnetAPI::buildURL('character', 'wow', ['char' => $this->char->charname, 'realm' => $this->char->realmSlug], ['guild', 'items', 'feed', 'statistics', 'stats', 'petSlots', 'pets', 'mounts' ]);
        // Rufe Daten ab.
        // sollte der request fehlschalgen, warte 100ms bis 300ms und Versuche erneut.
        // notwendig um nicht gegen das key LIMIT zu stoßen.
        $data=$this->getData($url);
        if (!is_null($data)) {
            $charData=$data;
        }
        else {
            usleep(rand(100000,300000));
            $charData=$this->getData($url);
            if (is_null($charData)) {
                return;
            }
        }
        if (!isset($charData['lastModified'])) {
            $this->char->updateCounters(['bnetError' => 1]);
            if (ENABLE_DEBUG_MODE) file_put_contents(WCF_DIR .  'log/bnet.log', '*** ERROR *** '. $url . PHP_EOL, FILE_APPEND);
            return;
        }
            // download images
            $images = ['avatar','inset','profilemain'];
            foreach($images as $image) {
                $path = $image=='avatar' ? $charData['thumbnail'] : StringUtil::replaceIgnoreCase('avatar',$image, $charData['thumbnail']);
                $imageRequest = new ImageDownload($path, $this->char->charname);
                $imageRequest->run();
            }
            // update char
            $idlist = bnetUpdate::runCharacterUpdate($this->char, $charData);
            // get equiped items
            foreach ($idlist as $item) {
                $nullvar = new WowItem($item['id']);
                if($nullvar->itemID > 0 && (!empty($item['context']) || !empty($item['bonusList']))) {
                    $nullvar = new ViewableWowItem($nullvar, $item['context'], $item['bonusList']);
                    $nullvar = null;
                }
                // if (!bnetAPI::getItem($item['id'], $item['context'], $item['bonusList'])) bnetAPI::getItem($item['id'], '', $item['bonusList']);
            }
            if (ENABLE_DEBUG_MODE) file_put_contents(WCF_DIR . 'log/bnet.log', 'UPDATE: DONE '. $this->char->charname . '('.$this->char->characterID.')'. PHP_EOL, FILE_APPEND);
    }
}

