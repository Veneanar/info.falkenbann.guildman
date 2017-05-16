<?php
namespace wcf\form;
use wcf\form\MessageForm;
use wcf\system\cache\runtime\GuildRuntimeChache;
use wcf\system\exception\UserInputException;
use wcf\system\exception\IllegalLinkException;
use wcf\system\WCF;
use wcf\util\StringUtil;
use wcf\util\ArrayUtil;
use wcf\system\wow\bnetAPI;
use wcf\data\guild\Guild;
use wcf\data\user\User;
use wcf\data\wow\character\WowCharacter;
use wcf\data\wow\character\WowCharacterList;
use wcf\data\wow\character\WowCharacterAction;
use wcf\data\guild\group\GuildGroup;
use wcf\data\guild\group\GuildGroupAction;
use wcf\data\guild\group\GuildGroupList;
use wcf\data\guild\group\application\GuildGroupApplication;
use wcf\data\guild\group\application\GuildGroupApplicationList;
use wcf\data\guild\group\application\GuildGroupApplicationAction;





/**
 * Gruppen hinzufügen
 * @author	    Veneanar Falkenbann
 * @copyright	2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	    GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	    info.falkenbann.guildman
 *
 */

class UserGuildGroupManageForm extends MessageForm {
    /**
     * @inheritDoc
     */
	public $disallowedBBCodesPermission = 'user.signature.disallowedBBCodes';

	/**
     * @inheritDoc
     */
	public $loginRequired = true;

	/**
     * @inheritDoc
     */
	public $messageObjectType = 'com.woltlab.wcf.box.content';

	/**
     * @inheritDoc
     */
	public $showSignatureSetting = false;

	/**
     * @inheritDoc
     */
	public $activeMenuItem = 'wcf.user.menu.gman.mygroups';

	/**
     * @inheritDoc
     */
	public $neededPermissions = ['user.gman.canViewChar'];

	/**
     * name of the template for the called page
     * @var	string
     */
	public $templateName = 'userGroupManage';

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
     * Character text
     * @var string
     */
    public $charText = '';

    /**
     * GroupID
     * @var integer
     */
    public $groupID = 0;

    /**
     * GroupID
     * @var GuildGroup
     */
    public $groupObject = null;

    /**
     * All Guild Groups
     * @var GuildGroup[]
     */
    public $allGroups = [];

    /**
     * user obejct
     * @var User
     */
    public $user = null;

    /**
     * if group is preselected
     * @var boolean
     */
    public $preselect = false;

    /**
     * list of group applications
     * @var GuildGroupApplication[]
     */
    public $applications = [];

	/**
     * @inheritDoc
     */
	public function assignVariables() {
        parent::assignVariables();

		WCF::getTPL()->assign([
            'guild'             => $this->guild,
            'allGroups'         => $this->allGroups,
            'preselect'         => $this->preselect,
            'groupID'           => $this->groupID,
            'mainChar'          => $this->mainChar,
            'user'              => $this->user,
            'applications'      => $this->applications
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
        if ($this->mainChar->characterID > 0) {
            $twinkList = new WowCharacterList();
            $twinkList->getConditionBuilder()->add("userID = ?", [$this->user->userID]);
            $twinkList->readObjectIDs();
            $applicationList = new GuildGroupApplicationList();
            $applicationList->getConditionBuilder()->add('characterID IN (?)', [$twinkList->getObjectIDs()]);
            $applicationList->readObjects();
            //echo "<pre>"; var_dump($twinkList->getObjectIDs()); echo "</pre>", die();
            $this->applications = $applicationList->getObjects();
        }
        if (empty($_POST)) {
            $this->realmID = $this->guild->getRealm()->slug;
            $this->charName = '';
        }
        $allGroupsList = new GuildGroupList();
        $allGroupsList->readObjects();
        $this->allGroups = $allGroupsList->getObjects();



	}

	/**
     * @inheritDoc
     */
	public function readFormParameters() {
		parent::readFormParameters();
        if (isset($_POST['preselect']))     $this->preselect = boolval($_POST['preselect']);
        if (isset($_POST['groupID']))       $this->groupID   = intval($_POST['groupID']);
		if (isset($_POST['charText']))      $this->charText  = trim($_POST['charText']);
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
