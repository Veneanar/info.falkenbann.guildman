<?php
namespace wcf\form;
use wcf\form\AbstractForm;
use wcf\system\cache\runtime\GuildRuntimeChache;
use wcf\system\exception\UserInputException;
use wcf\system\exception\IllegalLinkException;
use wcf\system\WCF;
use wcf\util\StringUtil;
use wcf\util\ArrayUtil;
use wcf\system\wow\bnetAPI;
use wcf\data\guild\Guild;
use wcf\data\user\User;
use wcf\data\wow\realm\WowRealmList;
use wcf\data\wow\character\WowCharacter;
use wcf\data\wow\character\WowCharacterList;
use wcf\data\wow\character\WowCharacterAction;

/**
 * Gruppen hinzufügen
 * @author	    Veneanar Falkenbann
 * @copyright	2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	    GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	    info.falkenbann.guildman
 *
 */

class UserCharacterManageForm extends AbstractForm {
	/**
     * @inheritDoc
     */
	public $activeMenuItem = 'wcf.user.menu.gman.mychars';

	/**
     * @inheritDoc
     */
	public $neededPermissions = ['user.gman.canViewChar'];

	/**
     * @inheritDoc
     */
	public $loginRequired = true;
	/**
     * @inheritDoc
     */
	public $neededModules = [];
	/**
     * name of the template for the called page
     * @var	string
     */
	public $templateName = 'userCharacterManage';

    /**
     * Guild
     * @var	Guild
     */
	public $guild = null;

    /**
     * Main Character
     * @var \wcf\data\wow\character\WowCharacter
     */
    public $mainChar = null;

    /**
     * Character name to add
     * @var string
     */
    public $charName = '';

    /**
     * RealmSlug
     * @var string
     */
    public $realmSlug = '';

    /**
     * user obejct
     * @var User
     */
    private $user = null;


	/**
     * @inheritDoc
     */
	public function assignVariables() {
        parent::assignVariables();
        $twinks = null;
        $twinksUnconfirmed = null;
//        if (!empty($this->mainChar->accountID) || $this->mainChar->userID>0) {
            $twinkList = new WowCharacterList();
            $twinkListunconfirmed = new WowCharacterList();
            // SELECT	gman_character.* FROM	wcf1_gman_character gman_character WHERE accountID LIKE '95716478a8f09d895bff2f8f0272e579a0fc36f0bb68564234ca0367099bb794' AND userID = 0 AND tempUserID = 0 AND characterID != 329
            if (!empty($this->mainChar->accountID)) {
                $twinkListunconfirmed->getConditionBuilder()->add("accountID LIKE ? AND (userID = 0 OR userID is null) AND (tempUserID = 0 OR tempUserID is null) AND characterID != ?", [$this->mainChar->accountID, $this->mainChar->characterID]);
                $twinkListunconfirmed->readObjects();
                $twinksUnconfirmed = $twinkListunconfirmed->getObjects();
            }
            $twinkList->getConditionBuilder()->add("(userID = ? OR tempUserID = ?) AND characterID != ?", [$this->user->userID, $this->user->userID, $this->mainChar->characterID]);
            $twinkList->sqlOrderBy ="tempUserID ASC";
            $twinkList->readObjects();
            $twinks = $twinkList->getObjects();
//        }
        $realmList = new WowRealmList();
        $realmList->readObjects();
		WCF::getTPL()->assign([
            'guild'             => $this->guild,
            'twinks'            => $twinks,
            'twinksUN'          => $twinksUnconfirmed,
            'mainChar'          => $this->mainChar,
            'realms'            => $realmList->getObjects(),
            'userID'            => $this->user->userID,
            'realmID'           => $this->realmID,
            'charName'          => $this->charName,
		]);
	}

	/**
     * @inheritDoc
     */
	public function readData() {
		parent::readData();
        $this->guild = GuildRuntimeChache::getInstance()->getCachedObject();
        $this->user = WCF::getUser();
        $this->mainChar = WowCharacter::getMainCharacterFromUser($this->user->userID);
        if (empty($_POST)) {
            $this->realmID = $this->guild->getRealm()->slug;
            $this->charName = '';
        }

	}

	/**
     * @inheritDoc
     */
	public function readFormParameters() {
		parent::readFormParameters();
        if (isset($_POST['charName']))  $this->password = trim($_POST['charName']);
		if (isset($_POST['realmID']))   $this->realmID  = intval($_POST['realmID']);
	}

	/**
     * @inheritDoc
     */
	public function validate() {
		parent::validate();

    }

	/**
     * @inheritDoc
     */
	public function save() {
		$this->saved();
		parent::save();
		WCF::getTPL()->assign('success', true);
	}
}
