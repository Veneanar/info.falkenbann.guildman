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
    private $forceUpdate = false;

    /**
     * timestamp of the local data
     * @var integer
     */
    private $bnetUpdate = 10;

    /**
     *  Name of the character
     * @var String
     */
    private $name = '';
    /**
     *  Realm of the character
     * @var String
     */
    private $realm = '';
    /**
     *  ID of the character
     * @var integer
     */
    private $id = 0;

	/**
     * Initialize the update process
     *
     * @param $name         string  Name of the character
     * @param $realm        string  Realm of the character
     * @param $id           integer 
     * @param $bnetUpdate   integer timestamp of the local data
     * @param $forceUpdate  boolean force an update (optinal)
     */
    public function __construct($name, $realm, $id, $bnetUpdate = 10, $forceUpdate = false, $wcfdir) {
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
        $this->name = $name;
        $this->realm = $realm;
        $this->id = $id;
    }

	/**
     * runs the update process asnc
     *
     */
    public function run() {
        new \wcf\system\WCF();
        $url = bnetAPI::buildURL('character', 'wow', ['char' => $this->name, 'realm' => $this->realm], ['guild', 'items', 'feed', 'statistics', 'stats', 'petSlots', 'pets', 'mounts' ]);
        $reply = null;
        $charData['inGuild'] = 0;
        try {
            $reply = @file_get_contents($url);
            if ($reply === false) {
                echo  "UPDATE ". $this->name . "(".$this->id."):\033[31m ERROR Url not found \033[0m (". $url .")". PHP_EOL;
                $sql = "UPDATE  wcf".WCF_N."_gman_character
                    SET     bnetError = bnetError + 1
                    WHERE   characterID = ? ";
                $statement = WCF::getDB()->prepareStatement($sql);
                $statement->execute([$this->id]);
                return;
            }
        }
        catch (Exception $e) {
            echo "UPDATE ".$this->name . "(".$this->id."): \033[31m ERROR Url not found \033[0m (". $url .")". PHP_EOL;
            $sql = "UPDATE  wcf".WCF_N."_gman_character
                    SET     bnetError = bnetError + 1
                    WHERE   characterID = ? ";
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute([$this->id]);
            return;
        }
        $charData=JSON::decode($reply, true);
        if (isset($this->forceUpdate) || $this->update['bnetUpdate'] < ($charData['lastModified'] / 1000)) {
            $charData['bnetError'] = 0;
            if (isset($charData['guild']['name']) && $charData['guild']['name'] == GMAN_MAIN_GUILDNAME) {
                $charData['inGuild'] = 1;
            }
            else {
                //$wowObj = new WowCharacter($this->charID);
                //@$action = new WowCharacterAction([$wowObj], 'removeFromAllGroups');
                //@$action->executeAction();
                $charData['inGuild'] = 2;
                echo $this->name . "(".$this->id."): \033[31m removed from guild\033[0m (". $url .")". PHP_EOL;
            }

            $images = ['avatar','inset','profilemain'];
            foreach($images as $image) {
                $path = $image=='avatar' ? $charData['thumbnail'] : StringUtil::replaceIgnoreCase('avatar',$image, $charData['thumbnail']);
                $imageRequest = new AsyncImageDownload($path, $this->name);
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
            bnetAPI::updateFeed(['name' => $this->name, 'realm' => $this->realm, 'id' => $this->id], $charData['feed'], $charData['inGuild']);
            bnetAPI::updateCharData(['name' => $this->name, 'realm' => $this->realm, 'id' => $this->id], $plaindata, $accID);
            bnetAPI::updateEquip(['name' => $this->name, 'realm' => $this->realm, 'id' => $this->id], $plaindata['lastModified'], $charData['items']);
            bnetAPI::updateMounts(['name' => $this->name, 'realm' => $this->realm, 'id' => $this->id], $plaindata['lastModified'], $charData['mounts']);
            bnetAPI::updateStatistics(['name' => $this->name, 'realm' => $this->realm, 'id' => $this->id], $plaindata['lastModified'], $charData['statistics']);
            bnetAPI::updatePets(['name' => $this->name, 'realm' => $this->realm, 'id' => $this->id], $plaindata['lastModified'], $petdata);
            WCF::getDB()->commitTransaction();
            echo "UPDATE ". $this->name . "(".$this->id."): \033[32m update done. \033[0m" . PHP_EOL;
        }
        else {
            echo "UPDATE ". $this->name . "(".$this->id."): no update requiered" . PHP_EOL;
        }
    }
}