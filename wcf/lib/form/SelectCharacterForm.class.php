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
use wcf\data\guild\group\application\GuildGroupApplication;

/**
 * Gruppen hinzufügen
 * @author	    Veneanar Falkenbann
 * @copyright	2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	    GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	    info.falkenbann.guildman
 *
 */

class SelectCharacterForm extends AbstractForm {
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
	public $loginRequired = false;
	/**
     * @inheritDoc
     */
	public $neededModules = [];
	/**
     * name of the template for the called page
     * @var	string
     */
	public $templateName = 'selectCharacter';

    /**
     * Guild
     * @var	Guild
     */
	public $guild = null;

    /**
     * Application ID
     * @var integer
     */
    public $appID = 0;

    /**
     * Application object
     * @var GuildGroupApplication
     */
    public $applicationObject = null;

    /**
     * user obejct
     * @var User
     */
    public $user = null;

    /**
     * RealmSlug
     * @var string
     */
    public $realmSlug = '';

    public $step = 1;

	/**
     * @inheritDoc
     */
	public function assignVariables() {
        parent::assignVariables();
        $chars = [];
        if ($this->user->userID > 0) {
            $charList = new WowCharacterList();
            $charList->getConditionBuilder()->add("(userID = ? OR tempUserID = ?)", [$this->user->userID, $this->user->userID]);
            $charList->sqlOrderBy ="tempUserID ASC";
            $charList->readObjects();
            $chars = $charList->getObjects();
            $this->step = 2;
        }
        if ($this->applicationObject->requireUser==1 and $this->user->userID==0) {
            $this->step = 1;
        }

        $realmList = new WowRealmList();
        $realmList->readObjects();
		WCF::getTPL()->assign([
            'guild'             => $this->guild,
            'chars'             => $chars,
            'realms'            => $realmList->getObjects(),
            'userID'            => $this->user->userID,
            'application'       => $this->applicationObject,
            'realmID'           => $this->realmID,
            'step'              => $this->step,
		]);
	}

	/**
     * @inheritDoc
     */
	public function readData() {
		parent::readData();
        $this->guild = GuildRuntimeChache::getInstance()->getCachedObject();
        $this->user = WCF::getUser();
        $this->realmID = $this->guild->getRealm()->slug;
        if (isset($_REQUEST['id'])) $this->appID = intval($_REQUEST['id']);
        if (isset($_REQUEST['step'])) $this->step = intval($_REQUEST['step']);
        $this->applicationObject = new GuildGroupApplication($this->appID);
        // echo "<pre>"; var_dump($this->applicationObject); "</pre>"; die();
		if ($this->applicationObject->appID  == 0) {
			throw new IllegalLinkException();
		}
	}

	/**
     * @inheritDoc
     */
	public function readFormParameters() {
		parent::readFormParameters();


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
