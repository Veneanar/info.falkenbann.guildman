<?php
namespace wcf\acp\form;
use wcf\form\AbstractForm;
use wcf\system\cache\runtime\GuildRuntimeChache;
use wcf\system\exception\UserInputException;
use wcf\system\exception\IllegalLinkException;
use wcf\system\WCF;
use wcf\util\StringUtil;
use wcf\util\ArrayUtil;
use wcf\data\guild\group\GuildGroup;
use wcf\data\guild\group\GuildGroupList;
use wcf\data\guild\group\GuildGroupAction;
use wcf\data\user\group\UserGroup;
use wcf\data\user\group\UserGroupList;
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

class CharacterEditForm extends AbstractForm {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.gman.grouplist';

	/**
	 * @inheritDoc
	 */
	public $neededPermissions = ['admin.gman.canAddGroups'];

	/**
	 * @inheritDoc
	 */
	public $neededModules = [];
	/**
     * name of the template for the called page
     * @var	string
     */
	public $templateName = 'characterEdit';
	/**
	 * IDs of the GuildGroup a Characteer bvelongs
	 * @var	integer[]
	 */
	public $guildGroupIDs = [];

	/**
	 * GuildGroup Objects
     * @var	GuildGroup[]
	 */
	public $guildGroupObjects = [];

	/**
     * Character's owner ID
     * @var	string
     */
	public $ownerName = '';

	/**
	 * Character's owner
	 * @var	User
	 */
	public $ownerObject = null;

	/**
	 * Characters' state
	 * @var	integer
	 */
	public $state = '';

	/**
     * Char is disabled
     * @var	string
     */
	public $isDisabled = false;

	/**
	 * Char's ID to edit
	 * @var	integer
	 */
	public $characterID = 0;

	/**
     * query for calendar
     * @var	WoWCharacter
     */
	public $charObject = null;

	/**
     * query for calendar
     * @var	string
     */
	public $charName = '';

	/**
     * Guild
     * @var	Guild
     */
	public $guild = null;



	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
        parent::assignVariables();
        $twinks = null;
        if (!empty($this->charObject->accountID) || $this->charObject->userID>0) {
            $twinkList = new WowCharacterList();
            if (!empty($this->charObject->accountID)) {
                $twinkList->getConditionBuilder()->add("(accountID LIKE ? OR userID = ?) AND characterID != ?", [$this->charObject->accountID, $this->charObject->userID, $this->charObject->characterID]);
            }
            else {
                $twinkList->getConditionBuilder()->add("userID = ? AND characterID != ?", [$this->charObject->userID, $this->charObject->characterID]);
            }
            $twinkList->readObjects();
            $twinks = $twinkList->getObjects();
        }
        $guildGroupList = new GuildGroupList();
        $guildGroupList->readObjects();
		WCF::getTPL()->assign([
			'action'            => 'add',
			'charObject'        => $this->charObject,
            'ownerName'         => $this->ownerName,
            'guild'             => $this->guild,
            'twinks'            => $twinks,
            'mainChar'          => $this->charObject,
            'guildGroups'       => $guildGroupList->getObjects(),
		]);
	}

	/**
	 * @inheritDoc
	 */
	public function readData() {
		parent::readData();
        if (isset($_REQUEST['id'])) $this->characterID = intval($_REQUEST['id']);
		$this->charObject = new WowCharacter($this->characterID);
        if ($this->charObject===null) {
			throw new IllegalLinkException();
		}

        $this->guild = GuildRuntimeChache::getInstance()->getCachedObject();
        if (empty($_POST)) {
			$this->ownerObject      = $this->charObject->getOwner();
            $this->ownerName        = $this->ownerObject->username;
            $this->guildGroupIDs    = $this->charObject->getGroupIDs();
        }

	}

	/**
	 * @inheritDoc
	 */
	public function readFormParameters() {
		parent::readFormParameters();
        if (isset($_REQUEST['id'])) $this->characterID = intval($_REQUEST['id']);
		$this->charObject = new WowCharacter($this->characterID);
        if ($this->charObject===null) {
			throw new IllegalLinkException();
		}
        if (isset($_POST['ownerName']))         $this->ownerName        = StringUtil::trim($_POST['ownerName']);
        if (isset($_POST['groupField']))        $this->guildGroupIDs    = ArrayUtil::toIntegerArray($_POST['groupField']);
	}


	/**
     * @inheritDoc
     */
	public function validate() {
		parent::validate();
		if (!empty($this->ownerName)) {
            $this->ownerObject = User::getUserByUsername($this->ownerName);
            if ($this->ownerObject->userID==0) {
                throw new UserInputException('ownerName', 'notfound');
            }
		}
        foreach($this->guildGroupIDs as $groupID) {
            $testgroup = new GuildGroup($groupID);
            if ($testgroup->groupID == 0) throw new UserInputException('groupField', 'notfound');
            if ($testgroup->wcfGroupID > 0) {
                if (!$testgroup->isAccesible()) throw new UserInputException('groupField', 'nopermission', ['groupName' => $testgroup->groupName]);
            }
        }

	}

	/**
	 * @inheritDoc
	 */
	public function save() {
        //echo "<pre>"; var_dump($this->charObject->getGroupIDs(), $this->guildGroupIDs); echo"</pre>"; die();
        if ( !empty(array_diff($this->charObject->getGroupIDs(), $this->guildGroupIDs)) || count($this->charObject->getGroupIDs()) != count($this->guildGroupIDs)) {
            $objectAction = new WowCharacterAction([$this->charObject], 'addToGroups', [
                'deleteOldGroups' => true,
                'groups'    => $this->guildGroupIDs,
                ]);
            $objectAction->executeAction();

            $objectAction = new WowCharacterAction([$this->charObject], 'setWCFGroups');
            $objectAction->executeAction();
        }
        if (!empty($this->ownerName)) {
            if ($this->charObject->userID != $this->ownerObject->userID) {
                $objectAction = new WowCharacterAction([$this->charObject],'setUser', ['userID' =>$this->ownerObject->userID]);
                $objectAction->executeAction();
            }
        }
		$this->saved();
		parent::save();
		WCF::getTPL()->assign('success', true);
	}
}
