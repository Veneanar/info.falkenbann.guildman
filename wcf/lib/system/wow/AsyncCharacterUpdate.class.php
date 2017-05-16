<?php
namespace wcf\system\wow;
use wcf\system\exception\HTTPNotFoundException;
use wcf\system\exception\HTTPServerErrorException;
use wcf\system\exception\Exception;
use wcf\system\exception\SystemException;
use wcf\system\database\exception\DatabaseQueryException;
use wcf\util\HTTPRequest;
use wcf\util\StringUtil;
use wcf\util\JSON;
use wcf\system\WCF;
use wcf\data\guild\Guild;
use wcf\data\wow\character\WowCharacter;
use wcf\data\wow\character\WowCharacterEditor;
use wcf\data\wow\character\WowCharacterAction;
use wcf\system\cache\runtime\GuildRuntimeChache;
use wcf\data\wow\item\WowItem;
use wcf\data\wow\item\ViewableWowItem;
use wcf\system\io\RemoteFile;
use wcf\system\Regex;
use Zend\Loader\StandardAutoloader;
use wcf\data\user\User;
use wcf\data\user\UserEditor;
use wcf\data\user\avatar\UserAvatarAction;


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
     * @var WowCharacterEditor
     */
    private $char = null;

	/**
     * Initialize the update process
     *
     * @param $char         WowCharacter  ID of the character
     * @param $forceUpdate  boolean force an update (optinal)
     */
    public function __construct($char, $forceUpdate = false, $wcfdir) {
        //require_once($wcfdir . "lib/system/WCF.class.php");
        //new \wcf\system\WCF();
        if (!defined('WCF_DIR')) define('WCF_DIR', $wcfdir);
        require_once(WCF_DIR.'lib/system/WCF.class.php');
		require_once(WCF_DIR.'lib/system/api/zend/Loader/StandardAutoloader.php');
		$zendLoader = new StandardAutoloader([StandardAutoloader::AUTOREGISTER_ZF => true]);
		$zendLoader->register();
        // wcf\system\database\exception\DatabaseQueryExecutionException

        require_once(WCF_DIR.'lib/system/database/exception/DatabaseQueryException.class.php');
        require_once(WCF_DIR.'lib/system/exception/SystemException.class.php');
        require_once(WCF_DIR.'lib/system/exception/HTTPNotFoundException.class.php');
        require_once(WCF_DIR.'lib/system/exception/HTTPServerErrorException.class.php');
        require_once(WCF_DIR.'lib/system/io/RemoteFile.class.php');
        require_once(WCF_DIR.'lib/system/Regex.class.php');
        require_once(WCF_DIR.'lib/system/wow/bnetIcon.class.php');
        require_once(WCF_DIR.'lib/system/wow/bnetUpdate.class.php');
        require_once(WCF_DIR.'lib/system/wow/bnetAPI.class.php');
        require_once(WCF_DIR.'lib/util/HTTPRequest.class.php');
        require_once(WCF_DIR.'lib/util/exception/HTTPException.class.php');
		require_once(WCF_DIR.'lib/util/StringUtil.class.php');
		require_once(WCF_DIR.'lib/util/JSON.class.php');
        require_once(WCF_DIR.'lib/data/guild/Guild.class.php');
		require_once(WCF_DIR.'lib/data/wow/character/WowCharacter.class.php');
		require_once(WCF_DIR.'lib/data/wow/item/WowItem.class.php');
        require_once(WCF_DIR.'lib/data/wow/item/ViewableWowItem.class.php');
        require_once(WCF_DIR.'lib/data/wow/item/WowItemIcon.class.php');
		require_once(WCF_DIR.'lib/data/wow/character/WowCharacterEditor.class.php');
        require_once(WCF_DIR.'lib/data/wow/character/WowCharacterAction.class.php');
        require_once(WCF_DIR.'lib/system/wow/AsyncImageDownload.class.php');
        require_once(WCF_DIR.'lib/system/wow/exception/AuthenticationFailure.class.php');
        require_once(WCF_DIR.'lib/system/cache/runtime/GuildRuntimeChache.class.php');
        require_once(WCF_DIR.'lib/data/user/User.class.php');
        require_once(WCF_DIR.'lib/data/user/UserEditor.class.php');
        require_once(WCF_DIR.'lib/data/user/avatar/UserAvatarAction.class.php');

        $this->forceUpdate = $forceUpdate;
        if (is_a($char, 'WowCharacterEditor')) {
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
            echo  "UPDATE ". $this->char->charname . "(".$this->char->characterID."):\033[31m ERROR Url not found \033[0m (". $url .")". PHP_EOL;
            if (ENABLE_DEBUG_MODE) file_put_contents(WCF_DIR .  'log/bnet.log', '*** ERROR *** '. $url . PHP_EOL, FILE_APPEND);
            return null;
        }
        catch (HTTPServerErrorException $e) {
            echo  "UPDATE ". $this->char->charname . "(".$this->char->characterID."):\033[31m battle.net returns a 500er!\033[0m  (". $url .")". PHP_EOL;
            return null;
        }
        catch (SystemException $e) {
            echo  "UPDATE ". $this->char->charname . "(".$this->char->characterID."):\033[31m battle.net not reachable!\033[0m  (". $url .")". PHP_EOL;
            return null;
        }
        $reply = $request->getReply();
        return JSON::decode($reply['body'], true);
    }

	/**
     * runs the update process asnc
     *
     */
    public function run() {
        new \wcf\system\WCF();
        $charData = [];
        if (!$this->checkUpdate()) {
            echo "UPDATE ". $this->char->charname . "(".$this->char->characterID."): no update requiered" . PHP_EOL;
            return;
        }
        $url = bnetAPI::buildURL('character', 'wow', ['char' => $this->char->charname, 'realm' => $this->char->realmSlug], ['guild', 'items', 'feed', 'statistics', 'stats', 'petSlots', 'pets', 'mounts', 'achievements']);
        // Rufe Daten ab.
        // sollte der request fehlschalgen, warte 500ms bis 1,5 und Verscuhe erneut.
        // notwendig um nicht gegen das key LIMIT zu stoßen.
        $data=$this->getData($url);
        if (!is_null($data)) {
            $charData=$data;
        }
        else {
            usleep(rand(500000,1500000));
            $charData=$this->getData($url);
            if (is_null($charData)) {
                return;
            }
        }
        if (!isset($charData['lastModified'])) {
            $this->char->updateCounters(['bnetError' => 1]);
            echo  "UPDATE ". $this->char->charname . "(".$this->char->characterID."):\033[31m ERROR Url not found \033[0m (". $url .")". PHP_EOL;
            if (ENABLE_DEBUG_MODE) file_put_contents(WCF_DIR .  'log/bnet.log', '*** ERROR *** '. $url . PHP_EOL, FILE_APPEND);
            return;
        }
            // download images
            $images = ['avatar','inset','profilemain'];
            foreach($images as $image) {
                $path = $image=='avatar' ? $charData['thumbnail'] : StringUtil::replaceIgnoreCase('avatar',$image, $charData['thumbnail']);
                $imageRequest = new AsyncImageDownload($path, $this->char->charname);
                $imageRequest->run();
            }
            // update char
            $idlist = bnetUpdate::runCharacterUpdate($this->char, $charData);
            // echo "UPDATE ". $this->name . ": anzhal items: ". count($idlist) . PHP_EOL;
            foreach ($idlist as $item) {
                $nullvar = new WowItem($item['id']);
                if($nullvar->itemID > 0 && (!empty($item['context']) || !empty($item['bonusList']))) {
                    $nullvar = new ViewableWowItem($nullvar, $item['context'], $item['bonusList']);
                    $nullvar = null;
                }
            }
            echo "UPDATE ". $this->char->charname . "(".$this->char->characterID."): \033[32m update done. \033[0m" . PHP_EOL;
    }
}

//try {
//    $reply = @file_get_contents($url);
//    if ($reply === false) {
//        echo  "UPDATE ". $this->name . "(".$this->id."):\033[31m ERROR Url not found \033[0m (". $url .")". PHP_EOL;
//        $sql = "UPDATE  wcf".WCF_N."_gman_character
//            SET     bnetError = bnetError + 1
//            WHERE   characterID = ? ";
//        $statement = WCF::getDB()->prepareStatement($sql);
//        $statement->execute([$this->id]);
//        return;
//    }
//}
//catch (Exception $e) {
//    echo "UPDATE ".$this->name . "(".$this->id."): \033[31m ERROR Url not found \033[0m (". $url .")". PHP_EOL;
//    $sql = "UPDATE  wcf".WCF_N."_gman_character
//            SET     bnetError = bnetError + 1
//            WHERE   characterID = ? ";
//    $statement = WCF::getDB()->prepareStatement($sql);
//    $statement->execute([$this->id]);
//    return;
//}
//$charData=JSON::decode($reply, true);