<?php
namespace wcf\acp\form;
use wcf\form\AbstractForm;
use wcf\system\exception\UserInputException;
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
use wcf\data\wow\realm\WowRealm;
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

class CharacterAddForm extends AbstractForm {
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
     * query for calendar
     * @var	string
     */
	public $realmID = '';



	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();

        $realmList = new WowRealmList;
        $realmList->readObjects();
        $realms = $realmList->getObjects();
		WCF::getTPL()->assign([
			'action'            =>'add',
			'charName'          => $this->charName,
            'charObj'           => $this->charObject,
            'realmID'           => $this->realmID,
            'realms'            => $realms,
		]);
	}

	/**
	 * @inheritDoc
	 */
	public function readData() {
		parent::readData();
	}

	/**
	 * @inheritDoc
	 */
	public function readFormParameters() {
		parent::readFormParameters();
        if (isset($_POST['realmID']))    $this->realmID       = StringUtil::trim($_POST['realmID']);
        if (isset($_POST['charName']))   $this->charName      = StringUtil::trim($_POST['charName']);
	}


	/**
     * @inheritDoc
     */
	public function validate() {
		parent::validate();
		if (empty($this->charName)) {
			throw new UserInputException('charName');
		}
		if (empty($this->realmID)) {
			throw new UserInputException('realmID');
		}
        if (new WowRealm($this->realmID)===null) throw new UserInputException('realmID', 'notexist');
	}

	/**
	 * @inheritDoc
	 */
	public function save() {
		parent::save();
        $objectAction = new WowCharacterAction([], 'create', [
               'data' => [
                    'realmSlug' => $this->realmID,
                    'characterName'  => $this->charName,
                    'isSlug'=> true,
               ]
        ]);
        $resultValues = $objectAction->executeAction();
		$this->saved();
        if ($resultValues['returnValues']['status']) {
		    WCF::getTPL()->assign('success', true);
            $this->charObject = new WowCharacter($resultValues['returnValues']['charID']);
            $this->charName = $this->charObject->name;
            $this->realmID = $this->charObject->realmID;
        }
        else {
        WCF::getTPL()->assign('failed', $resultValues['returnValues']['msg']);
        }
	}
}
