<?php
namespace wcf\page;
use wcf\data\wow\character\WowCharacter;
use wcf\data\guild\Guild;
use wcf\data\wow\character\WowCharacterList;
use wcf\data\wow\character\slot\CharacterSlotList;
use wcf\system\exception\IllegalLinkException;
use wcf\page\AbstractPage;
use wcf\system\WCF;
use wcf\data\user\User;
use wcf\data\user\UserProfile;
use wcf\system\cache\runtime\GuildRuntimeChache;
use wcf\util\StringUtil;
use wcf\system\menu\user\profile\UserProfileMenu;

/**
 * Shows an armory
 *
 * @author	    Veneanar Falkenbann
 * @copyright	2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	    GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	    info.falkenbann.guildman
 *
 */
class ArmorySpellPage extends AbstractPage {
	/**
     * @inheritDoc
     */
	public $activeMenuItem = 'wcf.menu.link.gman.armoryview';

	/**
     * name of the template for the called page
     * @var	string
     */
	public $templateName = 'amoryView';

    /**
     * wow char ID
     * @var integer
     */
    public $characterID = 0;
	/**
     * guild Object
     * @var	WoWCharacter
     */
    public $char = null;

	/**
     * owner Object
     * @var	UserProfile
     */
    public $user = null;

    /**
     * list of confirmed twinks
     * @var WoWCharacter[]
     */
    public $twinks = [];

	/**
     * needed modules to view this page
     * @var	string[]
     */
	public $neededModules = [];

	/**
     * needed permissions to view this page
     * @var	string[]
     */
	public $neededPermissions = [];

    /**
     * main guild
     * @var Guild
     */
    public $guild = null;




	/**
     * @inheritDoc
     */
	public function readParameters() {
		parent::readParameters();
        if (isset($_REQUEST['id'])) $this->characterID = intval($_REQUEST['id']);
        $this->char = new WowCharacter($this->characterID);
        if ($this->char->characterID == 0) {
            throw new IllegalLinkException();
        }
        $twinkList = new WowCharacterList();
        if ($this->char->userID > 0) {
            $this->user = new UserProfile($this->char->getOwner());
            $twinkList->getConditionBuilder()->add("userID = ? AND characterID <> ?", [$this->char->userID, $this->char->characterID]);
            $twinkList->sqlOrderBy ="c_level DESC";
        }
        else {
            //userID = 0 AND
            $twinkList->getConditionBuilder()->add("characterID <> ? AND accountID LIKE ? AND accountID <> ''", [$this->char->characterID, $this->char->accountID]);
            $twinkList->sqlOrderBy ="c_level DESC";
        }
        $twinkList->readObjects();
        $this->twinks = $twinkList->getObjects();



    }

	/**
     * @inheritDoc
     */
	public function readData() {
		parent::readData();
        $this->guild = GuildRuntimeChache::getInstance()->getCachedObject();
    }


	/**
     * @inheritDoc
     */
	public function assignVariables() {
		parent::assignVariables();
        $slotlist = new CharacterSlotList();
        $slotlist->readObjects();


        // echo "<pre> Count: " . $this->char->getCharacterStatistics()->getMaincategories()[0]['name']; echo "</pre>"; die();
        //echo "outside: <pre>";var_dump($this->char->getEquip()->getItem('offHand')); echo "</pre>"; die();
        //echo $this->char->getEquip()->getItem('head')->getIcon()->getImageTag(36); die();
		WCF::getTPL()->assign([
            'guild' => $this->guild,
			'viewChar' => $this->char,
            'twinks' => $this->twinks,
            'user' => $this->user,
            'slotList' => $slotlist->getObjects(),
		]);
	}
}
