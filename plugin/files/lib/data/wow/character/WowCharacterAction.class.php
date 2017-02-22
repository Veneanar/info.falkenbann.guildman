<?php
namespace wcf\data\wow\character;
use wcf\data\ISearchAction;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\exception\UserInputException;
use wcf\system\background\BackgroundQueueHandler;
use wcf\system\background\job\WowCharacterUpdateJob;
use wcf\system\clipboard\ClipboardHandler;
use wcf\data\IClipboardAction;
use wcf\system\WCF;
use wcf\data\guild\Guild;
use wcf\system\wow\bnetAPI;
use wcf\data\user\User;
use wcf\data\user\UserAction;
use wcf\data\user\group\ModeratedUserGroup;
use wcf\data\user\group\UserGroup;
use wcf\data\guild\group\GuildGroup;
use wcf\data\guild\group\GuildGroupList;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\UserException;

/**
 *
 * Executes WoW Character-related actions.
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */

class WowCharacterAction extends AbstractDatabaseObjectAction implements ISearchAction, IClipboardAction{
	/**
	 * {@inheritDoc}
	 */
	public static $baseClass = WowCharacterEditor::class;
	/**
	 * {@inheritDoc}
	 */
	protected $permissionsUpdate = array('mod.gman.canUpdateChar', 'user.gman.canUpdateOwnChar');
	/**
	 * {@inheritDoc}
	 */
	protected $permissionsGroupUpdate = array('admin.user.canManageGroupAssignment');
	/**
     * {@inheritDoc}
     */
	protected $permissionsCreate = array();
	/**
	 * {@inheritDoc}
	 */
	protected $permissionsDelete = array();
	/**
	 * {@inheritDoc}
	 */
	protected $requireACP = array();

	/**
     * @inheritDoc
     */
	protected $allowGuestAccess = ['getSearchResultList'];


    /**
     * @inheritDoc
     */
	public function validateGetSearchResultList() {
		$this->readString('searchString', false, 'data');
	}

	/**
     * @inheritDoc
     */
	public function getSearchResultList() {
		$searchString = $this->parameters['data']['searchString'];
		$list = [];
		$wowCharList = new WowCharacterList();
		$wowCharList->getConditionBuilder()->add("charID LIKE ?", [$searchString.'%']);
		$wowCharList->sqlLimit = 10;
		$wowCharList->readObjects();
        /**
         * @var WowCharacter $wowChar
         */
		foreach ($wowCharList as $wowChar) {
			$list[] = [
				'icon'      => $wowChar->getAvatar("avatar")->getImageTag(16),
				'label'     => $wowChar->getNice(),
				'objectID'  => $wowChar->charID,
				'type'      => 'user'
			];
		}
		return $list;
	}

    public function validateUpdateData() {
        parent::validateUpdate();
        /**
         * @var WowCharacter $wowChar
         */
        foreach($this->objects as $wowChar) {
            if ($wowChar->userID != WCF::getUser()->userID)  WCF::getSession()->checkPermissions(['mod.gman.canUpdateChar']);
        }
    }

    public function updateData() {
        foreach($this->objects as $wowChar) {
            bnetAPI::updateCharacter([
                'char'        => $wowChar,
                'forceUpdate'   => true,
                ]);
        }
    }

    public function validateUpdate() {
        parent::validateUpdate();
        if (isset($this->parameters['removeGroups'])) $groupIDs = $this->parameters['removeGroups'];
        if (isset($this->parameters['groups'])) $groupIDs = $this->parameters['groups'];
        if (!empty($groupIDs)) {
            $checkModeratedGroup = false;
            $throwDenied = false;
		    try {
			    WCF::getSession()->checkPermissions($this->permissionsUpdate);
		    }
		    catch (PermissionDeniedException $e) {
                $checkModeratedGroup = true;
                $throwDenied = true;
            }
            $guildGroups = new GuildGroupList();
            $guildGroups->getConditionBuilder()->add('groupID IN (?)', [$groupIDs]);
            $guildGroups->getConditionBuilder()->add('wcfGroupID > 0');
            $guildGroups->readObjects();
            /**
             * @var $guildGroup GuildGroup
             */
             foreach ($guildGroups->getObjects() as $guildGroup) {
                 $userGroup = new UserGroup($guildGroup->wcfGroupID);
                 if ($checkModeratedGroup) {
                     $moderatedUserGroup = new ModeratedUserGroup($userGroup);
                     if (!$moderatedUserGroup->isLeader()) throw new PermissionDeniedException;
                 }
                 else {
                     if ($throwDenied) throw new PermissionDeniedException;
                     if (!$userGroup->isAccessible()) throw new PermissionDeniedException;
                 }
            }
        }
   }



    public function validateCreate() {
        parent::validateCreate();
        if (!isset($this->parameters['name'])) throw new UserInputException('charname', 'empty');
        if (!isset($this->parameters['realm'])) throw new UserInputException('realmname', 'empty');
    }
    public function create() {
        $name = $this->parameters['name'];
        $realm = $this->parameters['realm'];
        $isSlug = isset($this->parameters['isSlug']) ? $this->parameters['isSlug'] : false;
        $this->parameters['groups'];
        $charCheck = bnetAPI::checkCharacter($name, $realm, $isSlug);
        if($charCheck['status']) {
            $charID = bnetAPI::createCharacter($name, $realm, $isSlug);
            bnetAPI::updateCharacter([['charID'=>$charID,'bnetUpdate'=>10]]);
            return $charCheck;
        }
        return $charCheck;
    }

    public function update() {
        parent::update();
		$groupIDs = isset($this->parameters['groups']) ? $this->parameters['groups'] : [];
		$removeGroups = isset($this->parameters['removeGroups']) ? $this->parameters['removeGroups'] : [];
		if (!empty($groupIDs)) {
			$action = new WowCharacterAction($this->objects, 'addToGroups', [
				'groups' => $groupIDs,
				'addDefaultGroups' => false,
                'addWCFGroups' => true
			]);
			$action->executeAction();
		}
		if (!empty($removeGroups)) {
			$action = new WowCharacterAction($this->objects, 'removeFromGroups', [
				'groups' => $removeGroups,
                'deleteWCFGroups' => true
			]);
			$action->executeAction();
		}
    }

    public function setMain() {
        /**
         * @var WowCharacter $wowChar
         */
        foreach($this->objects as $wowChar) {
            $charList = new WowCharacterList();
            $charList->getConditionBuilder->add('WHERE userID = ?', $wowChar->userID);
            $charList->readObjects();
            $action = new WowCharacterAction($charList->getObjects, 'update', [
                'data' => [
                    'isMain' => 0,
                ]]);
            $action->executeAction();
            $action = new WowCharacterAction($this->objects, 'update', [
                'data' => [
                    'isMain' => 1,
                ]]);
            $action->executeAction();
        }
    }

    public function validateSetUser() {
        /**
         * @var WowCharacter $wowChar
         */
        $groupIDs = [];
        foreach($this->objects as $wowChar) {
            $groupIDs[] = $wowChar->getGroupIDs();
        }
        $this->parameters['groups'] = array_unique($groupIDs);
        try {
            $this->validateUpdate();
        }
        catch (PermissionDeniedException $e)  {
            // set in moderated queue
        }
    }



    public function setUser() {
        foreach($this->objects as $wowChar) {
            $action = new WowCharacterAction([$wowChar], 'update', [
                'data' => [
                    'userID'    => $wowChar->tempUserID,
                    'disabled'  => 0,
                ]]);
            $action->executeAction();
            $groupIDs[] = $wowChar->getGroupIDs();
            if (!empty($groupIDs)) {
                $this->addWCFUserGroups($wowChar, $this->parameters['addWCFGroups']);
            }
        }
    }


	/**
	 * Validates the enable action.
	 */
	public function validateEnable() {
		WCF::getSession()->checkPermissions(['admin.user.canEnableUser']);
	}

	/**
	 * Enables wowchar.
	 */
	public function enable() {
        foreach($this->objects as $wowChar) {
            $action = new WowCharacterAction([$wowChar], 'update', [
                'data' => [
                    'userID'    => $this->parameters['userID'],
                    'disabled'  => 0,
                ]]);
            $action->executeAction();
            $groupIDs[] = $wowChar->getGroupIDs();
            if (!empty($groupIDs)) {
                $this->addWCFUserGroups($wowChar, $this->parameters['addWCFGroups']);
            }
        }
    }

	/**
     * Enables wowchar ownerusers.
     */
	public function disable() {
        foreach($this->objects as $wowChar) {
            $groupIDs[] = $wowChar->getGroupIDs();
            if (!empty($groupIDs)) {
                $this->removeWCFUserGroups($wowChar, $this->parameters['deleteWCFGroups']);
            }
            $action = new WowCharacterAction([$wowChar], 'update', [
                'data' => [
                    'userID'         => 0,
                    'tempUuserID'    => $wowChar->userID,
                    'disabled'       => 1,
                ]]);
            $action->executeAction();
        }
    }


    public function removeFromGuild() {
        foreach($this->objects as $wowChar) {
            $this->removeWCFUserGroups($wowChar, ['deleteWCFGroups'=> true]);
            $action = new WowCharacterAction([$wowChar], 'update', [
                'data' => [
                    'inGuild'         => 0,
                    'primaryGroup'    => 0,
                ]]);
            $action->executeAction();
        }
        $action = new WowCharacterAction($this->objects, 'removeFromAllGroups');
        $action->executeAction();
    }
	/**
     * function for rank changes.
     */
    public function changeRank() {
        $guild = new Guild();
        $newGroup = $guild->getGroupfromRank($this->parameters['rank']);
        $action = new WowCharacterAction($this->objects, 'removeFromGroups', [
            'groups' => $guild->getGuildGroupIds(true),
            'deleteWCFGroups' => true
        ]);
        $action->executeAction();

        $action = new WowCharacterAction($this->objects, 'addToGroups', [
            'groups' => $newGroup,
            'addDefaultGroups' => false,
            'addWCFGroups' => true
        ]);
        $action->executeAction();

        $action = new WowCharacterAction($this->objects, 'update', [
            'data' => [
                'primaryGroup' => $newGroup,
            ]]);
        $action->executeAction();
    }

	/**
     * Remove chars from given groups.
     */
	public function removeFromGroups() {
		if (empty($this->objects)) {
			$this->readObjects();
		}
		$groupIDs = $this->parameters['groups'];
		foreach ($this->getObjects() as $charEditor) {
			$charEditor->removeFromGroups($groupIDs);
            if (isset($this->parameters['deleteWCFGroups']) && $this->parameters['deleteWCFGroups']==true){
                $this->removeWCFUserGroups($charEditor);
            }
        }

    }

	/**
     * Remove chars from given groups.
     */
	public function addToGroups() {
		if (empty($this->objects)) {
			$this->readObjects();
		}
		$groupIDs = $this->parameters['groups'];
		$deleteOldGroups = $addDefaultGroups = true;
		if (isset($this->parameters['deleteOldGroups'])) $deleteOldGroups = $this->parameters['deleteOldGroups'];

		foreach ($this->getObjects() as $charEditor) {
			$charEditor->addToGroups($groupIDs, $deleteOldGroups, $addDefaultGroups);
            if (isset($this->parameters['addWCFGroups']) && $this->parameters['addWCFGroups']==true){
                $this->addWCFUserGroups($charEditor, $this->parameters['addWCFGroups']);
            }
		}
	}

	/**
     * Internal function for removing WCF Groups for the account.
     *
     * @param $charEditor
     */
    private function removeWCFUserGroups($charEditor) {
        $user = new User($charEditor->userID);
        if ($user->getObjectID>0) {
            $guild = new Guild();
            $diffGroups = array_diff($guild->getGuildGroupIds(), $charEditor->getAccountGroups());
            $diffGroups = $guild->convertToWCFGroup($diffGroups);
            if (count($diffGroups)) {
                $objectAction = new UserAction([$user], 'removeFromGroups', [
                 'groups' => $diffGroups
             ]);
                $objectAction->executeAction();
            }
        }
    }

	/**
     * Internal function for adding WCF Groups for the account.
     *
     * @param $userObject
     */
    private function addWCFUserGroups($charEditor, $groupIDs) {
        $user = new User($charEditor->userID);
        if ($user->getObjectID>0) {
            $guild = new Guild();
            $groupIDs = $guild->convertToWCFGroup($groupIDs);
            if (count($groupIDs)) {
                $objectAction = new UserAction([$user], 'addToGroups', [
                    'groups' => $groupIDs,
                    'addDefaultGroups' => false
                ]);
                $objectAction->executeAction();
            }
        }
    }

    public static function bulkUpdate($isCron = false, $forceALL = false) {
        $GMAN_UPDATE_GUILDOONLY = false;
        $charList = new WowCharacterList();
        if ($GMAN_UPDATE_GUILDOONLY && !$forceALL)  $charList->getConditionBuilder()->add('inGuild = ?', [1]);
        $charList->readObjects();
        $updateList = [];
        $jobs = [];
        $counter = 0;
        foreach ($charList as $char) {
            $updateList[] = ['charID' => $char->charID, 'bnetUpdate' => $char->bnetUpdate];
            $counter++;
            if (!$isCron && $counter == GMAN_BNET_JOBSIZE) {
                $counter = 0;
                $jobs[] = new WowCharacterUpdateJob($updateList);
                $updateList = [];
            }
        }
        return ($isCron) ? $updateList : $jobs;
        // BackgroundQueueHandler::getInstance()->enqueueIn
    }

	/**
     * @inheritDoc
     */
	public function validateUnmarkAll() {
		// does nothing
	}

	/**
     * @inheritDoc
     */
	public function unmarkAll() {
		ClipboardHandler::getInstance()->removeItems(ClipboardHandler::getInstance()->getObjectTypeID('info.falkenbann.gman.character'));
	}

	/**
     * Unmarks chars.
     *
     * @param	integer[]	$charIDs
     */
	protected function unmarkItems(array $charIDs = []) {
		if (empty($charIDs)) {
			$charIDs = $this->objectIDs;
		}

		if (!empty($charIDs)) {
			ClipboardHandler::getInstance()->unmark($charIDs, ClipboardHandler::getInstance()->getObjectTypeID('info.falkenbann.gman.character'));
		}
	}

}