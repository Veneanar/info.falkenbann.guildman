<?php
namespace wcf\acp\form;
use wcf\form\AbstractForm;
use wcf\system\exception\UserInputException;
use wcf\system\exception\IllegalLinkException;
use wcf\system\WCF;
use wcf\util\StringUtil;
use wcf\util\ArrayUtil;
use wcf\data\guild\group\GuildGroup;
use wcf\data\guild\group\GuildGroupAction;
use wcf\data\user\group\UserGroup;
use wcf\data\user\group\UserGroupList;
use wcf\data\guild\Guild;
use wcf\data\user\User;
use wcf\data\wow\realm\WowRealmList;
use wcf\data\wow\character\WowCharacter;
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
	 * @var	string
	 */
	public $charID = '';

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
		WCF::getTPL()->assign([
			'action'            => 'add',
			'charObject'        => $this->charObject,
            'ownerName'         => $this->ownerName,
            'guild'             => $this->guild,
		]);
	}

	/**
	 * @inheritDoc
	 */
	public function readData() {
		parent::readData();
        if (isset($_REQUEST['charID'])) $this->charID = StringUtil::trim($_REQUEST['charID']);
		$this->charObject = new WowCharacter($this->charID);
        if ($this->charObject===null) {
			throw new IllegalLinkException();
		}

        $this->guild = new Guild();
        if (empty($_POST)) {
			$this->ownerObject  = $this->charObject->getOwner();
            $this->ownerName     = $this->ownerObject->username;
        }

	}

	/**
	 * @inheritDoc
	 */
	public function readFormParameters() {
		parent::readFormParameters();
        if (isset($_REQUEST['charID'])) $this->charID = StringUtil::trim($_REQUEST['charID']);
		$this->charObject = new WowCharacter($this->charID);
        if ($this->charObject===null) {
			throw new IllegalLinkException();
		}
        if (isset($_POST['ownerName']))     $this->ownerName        = StringUtil::trim($_POST['ownerName']);

	}


	/**
     * @inheritDoc
     */
	public function validate() {
		parent::validate();
		if (empty($this->ownerName)) {
			throw new UserInputException('ownerName');
		}
        $this->ownerObject = User::getUserByUsername($this->ownerName);
        if ($this->ownerObject->userID==0) {
            throw new UserInputException('ownerName', 'notfound');
        }
	}

	/**
	 * @inheritDoc
	 */
	public function save() {
		parent::save();
        $objectAction = new WowCharacterAction([$this->charObject],'setUser', ['userID' =>$this->ownerObject->userID]);
        $objectAction->executeAction();

		$this->saved();

		// reset values

		WCF::getTPL()->assign('success', true);
	}
}
