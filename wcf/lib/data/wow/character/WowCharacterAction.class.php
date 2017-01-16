<?php
namespace wcf\data\wow\character;
use wcf\data\ISearchAction;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\exception\UserInputException;
use wcf\system\background\BackgroundQueueHandler;
use wcf\system\background\job\WowCharacterUpdateJob;
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

/**
 *
 * Executes WoW Character-related actions.
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */

class WowCharacterAction extends AbstractDatabaseObjectAction implements ISearchAction {
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
	 * {@inheritDoc}
	 */
	protected $allowGuestAccess = array();


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

    public function update() {
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

        // ... update fertig schreiben.
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
            if (isset($this->parameters['deleteWCFGroups']) && $this->parameters['deleteWCFGroups']==true && $charEditor->userID > 0){
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
            if (isset($this->parameters['addWCFGroups']) && $this->parameters['addWCFGroups']==true && $charEditor->userID > 0){
                $this->addWCFUserGroups($charEditor);
            }
		}
	}

	/**
     * Internal function for removing WCF Groups for the account.
     *
     * @param $userObject
     */
    private function removeWCFUserGroups($userObject) {
        $user = new User($userObject->userID);
        if ($user->getObjectID>0) {
            $guild = new Guild();
            $diffGroups = array_diff($guild->getGuildGroupIds(), $userObject->getAccountGroups());
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
    private function addWCFUserGroups($userObject, $groupIDs) {
        $user = new User($userObject->userID);
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

}