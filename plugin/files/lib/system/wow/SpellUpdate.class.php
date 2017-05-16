<?php
namespace wcf\system\wow;
use wcf\system\exception\HTTPNotFoundException;
use wcf\system\exception\Exception;
use wcf\util\HTTPRequest;
use wcf\util\StringUtil;
use wcf\util\JSON;
use wcf\system\WCF;
use wcf\data\wow\spell\WowSpell;


/**
 * Updates a given WoW Spell
 * @author	Veneanar Falkenbann
 * @copyright	2017  Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */

class SpellUpdate {
    /**
     *  ID of the character
     * @var integer
     */
    private $spellID = 0;

	/**
     * Initialize the update process
     *
     * @param $id           integer Spell ID
     */
    public function __construct($id) {
        $this->spellID = $id;
    }

    private function getData($url) {
        $reply = '';
        $request = new HTTPRequest($url);
        try {
        	$request->execute();
        }
        catch (HTTPNotFoundException $e) {
            if (ENABLE_DEBUG_MODE) file_put_contents(WCF_DIR . 'log/bnet.log', '*** ERROR *** '. $url . PHP_EOL, FILE_APPEND);
            return false;
        }
        catch (HTTPException $e) {
            if (ENABLE_DEBUG_MODE) file_put_contents(WCF_DIR . 'log/bnet.log', '*** ERROR *** '. $url . PHP_EOL, FILE_APPEND);
            return false;
        }
        catch (SystemException $e) {
            if (ENABLE_DEBUG_MODE) @file_put_contents(WCF_DIR . 'log/bnet.log', '*** ERROR *** Cannot reach media.blizzard.com for '. $url . PHP_EOL, FILE_APPEND);
            return false;
        }
        $reply = $request->getReply();
        return JSON::decode($reply['body'], true);

    }

	/**
     * runs the update process asnc
     *
     */
    public function run() {
        if (!$this->spellID) return;
        $spellinfo = [];
        $url = bnetAPI::buildURL('spell', 'wow', ['id' => $this->spellID]);
        // Rufe Daten ab.
        // sollte der request fehlschalgen, warte 200 bis 500ms und Verscuhe erneut.
        // notwendig um nicht gegen das key LIMIT zu stoßen.
        $data=$this->getData($url);
        if (!is_null($data)) {
            $spellinfo=$data;
        }
        else {
            usleep(rand(200000,500000));
            $spellinfo=$this->getData($url);
            if (is_null($spellinfo)) {
                return;
            }
        }
        $spellinfo = $this->getData($url);
        if (!isset($spellinfo['id'])) {
            echo "Error connecting bnet.";
            if (ENABLE_DEBUG_MODE) file_put_contents(WCF_DIR .  'log/spell.log', '*** ERROR *** '. $url . PHP_EOL, FILE_APPEND);
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

       } else {
            if (ENABLE_DEBUG_MODE) @file_put_contents(WCF_DIR . 'log/spell.log', "*** ERROR *** spell ".$this->spellID. ": Item not found". PHP_EOL, FILE_APPEND);
        }
    }
}