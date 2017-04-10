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
use wcf\system\io\RemoteFile;
use wcf\system\Regex;
use Zend\Loader\StandardAutoloader;
use wcf\data\wow\spell\WowSpell;
use wcf\data\wow\spell\WowSpellItem;

/**
 * Updates a given WoW Spell
 * @author	Veneanar Falkenbann
 * @copyright	2017  Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */

class AsyncSpellUpdate extends \Thread{
    /**
     *  ID of the character
     * @var integer
     */
    private $spellID = 0;

	/**
     * Initialize the update process
     *
     * @param $id           integer
     */
    public function __construct($id, $wcfdir) {
        //require_once($wcfdir . "lib/system/WCF.class.php");
        //new \wcf\system\WCF();
        if (!defined('WCF_DIR')) define('WCF_DIR', $wcfdir);
        require_once($wcfdir.'lib/system/WCF.class.php');
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
        require_once(WCF_DIR.'lib/util/HTTPRequest.class.php');
        require_once(WCF_DIR.'lib/util/exception/HTTPException.class.php');
		require_once(WCF_DIR.'lib/util/StringUtil.class.php');
		require_once(WCF_DIR.'lib/util/JSON.class.php');
        require_once(WCF_DIR.'lib/system/wow/AsyncImageDownload.class.php');
        require_once(WCF_DIR.'lib/system/wow/exception/AuthenticationFailure.class.php');
        require_once(WCF_DIR.'lib/data/wow/spell/WowSpell.class.php');
        require_once(WCF_DIR.'lib/data/wow/spell/WowSpellIcon.class.php');

        $this->spellID = $id;

    }


    private function getData($url) {
        $reply = '';
        $request = new HTTPRequest($url);
        try {
        	$request->execute();
        }
        catch (HTTPNotFoundException $e) {
            echo  "UPDATE ".$this->id.":\033[31m ERROR Url not found \033[0m (". $url .")". PHP_EOL;
            return null;
        }
        catch (HTTPServerErrorException $e) {
            echo  "UPDATE ".$this->id.":\033[31m battle.net returns a 500er!\033[0m  (". $url .")". PHP_EOL;
            return null;
        }
        catch (SystemException $e) {
            echo  "UPDATE ".$this->id.":\033[31m battle.net not reachable!\033[0m  (". $url .")". PHP_EOL;
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
        $spellinfo = [];
        $url = bnetAPI::buildURL('spell', 'wow', ['id' => $this->spellID]);
        // Rufe Daten ab.
        // sollte der request fehlschalgen, warte 500ms bis 1,5 und Verscuhe erneut.
        // notwendig um nicht gegen das key LIMIT zu stoßen.
        $data=$this->getData($url);
        if (!is_null($data)) {
            $spellinfo=$data;
        }
        else {
            usleep(rand(500000,1500000));
            $spellinfo=$this->getData($url);
            if (is_null($spellinfo)) {
                return;
            }
        }
        $spellinfo = $this->getData($url);
        if (!isset($spellinfo['id'])) {
            echo  "UPDATE ".$this->id.":\033[31m ERROR!\033[0m  (". $url .")". PHP_EOL;
            return;
        }

        $sql = "INSERT INTO  wcf".WCF_N."_gman_wow_spells
                            (spellID, enchantID, spellName, bnetData, bnetUpdate)
                VALUES      (?,0,?,?,?)
                ON DUPLICATE KEY UPDATE
                            spellName = VALUES(spellName),
                            bnetData = VALUES(bnetData),
                            bnetUpdate = VALUES(bnetUpdate)
            ";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([
                $this->spellID,
                $spellinfo['name'],
                JSON::encode($spellinfo),
                TIME_NOW,
        ]);
        $t = new WowSpell($this->spellID);
        if ($t->spellID > 0) {
            $t->getIcon()->getURL(18);
            $t->getIcon()->getURL(36);
            $t->getIcon()->getURL(56);
            echo "UPDATE ". $spellinfo['name']. "(".$this->spellID."): \033[32m update done. \033[0m" . PHP_EOL;
        } else {
            echo "UPDATE ". $spellinfo['name']. "(".$this->spellID."): \033[31m ERROR Item not found \033[0m". PHP_EOL;
            if (ENABLE_DEBUG_MODE) @file_put_contents(WCF_DIR . 'log/spell.log', "*** ERROR *** ". $spellinfo['name']. "(".$this->spellID."): Spell not found". PHP_EOL, FILE_APPEND);
        }

    }
}

