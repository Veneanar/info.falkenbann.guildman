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
use wcf\util\StringUtil;

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
	protected $allowGuestAccess = ['getSearchResultList', 'check'];


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
		$searchString = StringUtil::firstCharToUpperCase($this->parameters['data']['searchString']);
		$list = [];
		$wowCharList = new WowCharacterList();
		$wowCharList->getConditionBuilder()->add("charname LIKE ?", [$searchString.'%']);
		$wowCharList->sqlLimit = 10;
		$wowCharList->readObjects();
        $wowChars = $wowCharList->getObjects();
        /**
         * @var WowCharacter $wowChar
         */
		foreach ($wowChars as $wowChar) {
			$list[] = [
				'icon'      => $wowChar->getAvatar("avatar")->getImageTag(16),
				'label'     => $wowChar->name,
				'objectID'  => $wowChar->characterID,
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
                'charInfo' => [
                        'name'  => $wowChar->charname,
                        'realm' => $wowChar->realmSlug,
                        'id'    => $wowChar->characterID,
                    ],
                'forceUpdate'   => true,
                ]);
        }
    }

    public function validateUpdate() {
        parent::validateUpdate();
        if (isset($this->parameters['removeGroups'])) $groupIDs = $this->parameters['removeGroups'];
        if (isset($this->parameters['addGroups'])) $groupIDs = $this->parameters['addGroups'];
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


    public function validateCheck() {
        $this->readString('name');
		$this->readString('realm');
        $this->readBoolean('isSlug', true);
    }
    public function validateCreate() {
        parent::validateCreate();
        $this->validateCheck();
    }

    public function check() {
        return bnetAPI::checkCharacter($this->parameters['name'], $this->parameters['realm'], $this->parameters['isSlug']);
    }

    public function create() {
        $result = bnetAPI::checkCharacter($this->parameters['name'], $this->parameters['realm'], $this->parameters['isSlug']);
        if($result['status']) {
            $charID = $result['charID'];
            if (empty($charID)) $charID = bnetAPI::createCharacter($this->parameters['name'], $this->parameters['realm'], $this->parameters['isSlug']);
            bnetAPI::updateCharacter([['name'=> $this->parameters['name'],'realm' => $this->parameters['realm'],  'id'=>$charID,'bnetUpdate'=>10]]);
            return ['status' => true, 'charID'=> $charID];
        }
        else {
            return $result;
       }
    }

    public function update() {
        parent::update();


        //$groupIDs = isset($this->parameters['groups']) ? $this->parameters['groups'] : [];
        //$removeGroups = isset($this->parameters['removeGroups']) ? $this->parameters['removeGroups'] : [];
        //if (!empty($groupIDs)) {
        //    $action = new WowCharacterAction($this->objects, 'addToGroups', [
        //        'groups' => $groupIDs,
        //        'addDefaultGroups' => false,
        //        'addWCFGroups' => true
        //    ]);
        //    $action->executeAction();
        //}
        //if (!empty($removeGroups)) {
        //    $action = new WowCharacterAction($this->objects, 'removeFromGroups', [
        //        'groups' => $removeGroups,
        //        'deleteWCFGroups' => true
        //    ]);
        //    $action->executeAction();
        //}
        $objectAction = new WowCharacterAction($this->objects, 'setWCFGroups');
        $objectAction->executeAction();

    }

	/**
     * validation for SetMain()
     */
    public function validateSetMain() {
		if (empty($this->objects)) {
			$this->readObjects();

			if (empty($this->objects)) {
				throw new UserInputException('objectIDs');
			}
		}
        foreach($this->objects as $wowChar) {
            if ($wowChar->userID==0) {
                // noch gegen korrekte Fehlermeldung tauschen.
                throw new UserInputException('userID');
            }
            if (!$wowChar->getOwner()->canEdit()) {
                throw new PermissionDeniedException();
            }
        }
    }

	/**
     * Set current character as main character.
     */
    public function setMain() {
        foreach($this->objects as $wowChar) {
            $charList = new WowCharacterList();
            $charList->getConditionBuilder()->add('userID = ?', [$wowChar->userID]);
            $charList->readObjects();
            $action = new WowCharacterAction($charList->getObjects(), 'update', [
                'data' => [
                    'isMain' => 0,
                ]]);
            $action->executeAction();
        }
        $action = new WowCharacterAction($this->objects, 'update', [
            'data' => [
                'isMain' => 1,
            ]]);
        $action->executeAction();
        $objectAction = new WowCharacterAction($this->objects, 'setWCFGroups');
        $objectAction->executeAction();
    }

    public function validateSetUser() {
		if (empty($this->objects)) {
			$this->readObjects();

			if (empty($this->objects)) {
				throw new UserInputException('objectIDs');
			}
		}
        if (empty($this->parameters['userID'])) throw new UserInputException('userid');
        WCF::getSession()->checkPermissions(['user.gman.canAddCharOwner']);
        WCF::getSession()->checkPermissions(['user.gman.canAddCharOwnerWithoutModeration']);
    }

    public function validateSetUserModerated() {
		if (empty($this->objects)) {
			$this->readObjects();

			if (empty($this->objects)) {
				throw new UserInputException('objectIDs');
			}
		}
        if (empty($this->parameters['userID'])) throw new UserInputException('userid');
        WCF::getSession()->checkPermissions(['user.gman.canAddCharOwner']);
    }

    public function setUserModerated() {
        foreach($this->objects as $wowChar) {
            $action = new WowCharacterAction([$wowChar], 'update', [
                'data' => [
                    'tempUserID'    => $this->parameters['userID'],
                    'isDisabled'      => 1,
                ]]);
            $action->executeAction();
        }
    }

    public function setUser() {
        foreach($this->objects as $wowChar) {
            $action = new WowCharacterAction([$wowChar], 'update', [
                'data' => [
                    'userID'    => $this->parameters['userID'],
                    'isDisabled'  => 0,
                ]]);
            $action->executeAction();
        }
        $objectAction = new WowCharacterAction($this->objects, 'setWCFGroups');
        $objectAction->executeAction();
    }

	/**
	 * Validates the enable action.
	 */
	public function validateConfirmUser() {
		WCF::getSession()->checkPermissions(['admin.user.canEnableUser']);
	}

	/**
	 * Enables wowchar.
	 */
	public function confirmUser() {
        foreach($this->objects as $wowChar) {
            $action = new WowCharacterAction([$wowChar], 'update', [
                'data' => [
                    'userID'    => $wowChar->tempUserID,
                    'isDisabled'  => 0,
                ]]);
            $action->executeAction();
        }
        $objectAction = new WowCharacterAction($this->objects, 'setWCFGroups');
        $objectAction->executeAction();
    }

	/**
     * Set the WCF Groups for the Account
     */
    public function setWCFGroups() {
       $charList = [];
       $userFilter  = [];
       // filter non-assigned and alt chars.
       foreach($this->objects as $wowChar) {
            if ($wowChar->userID > 0 && !in_array($wowChar->userID, $userFilter)) {
                $charList[] = $wowChar;
                $userFilter[] = $wowChar->userID;
            }
        }
        $guild = new Guild();
        // Convert all Guild Group IDs to WCF Group IDs
        $oldgroups = array_unique(isset($this->parameters['oldWCFGroups']) ?  $this->parameters['oldWCFGroups'] : $guild->convertToWCFGroup($guild->getGuildGroupIds()));
        //if (ENABLE_DEBUG_MODE) file_put_contents(WCF_DIR . 'log/action.log', 'Alte Gruppen die geloescht werden sollen sdsd: '. print_r($oldgroups) . PHP_EOL, FILE_APPEND);
        foreach($charList as $wowChar) {
            // Get the guild groups from each character of an account and convert it to a WCF Group ID
                $userObject = new User($wowChar->userID);
                if (!empty($oldgroups)) {
                    $action = new UserAction([$userObject], 'removeFromGroups', [
                        'groups' => $oldgroups
                    ]);
                    $action->executeAction();
                }
                $newgroups = $guild->convertToWCFGroup($wowChar->getAccountGroupIDs());
                if (!empty($newgroups)) {
                    $action = new UserAction([$userObject], 'addToGroups', [
                        'groups' => $newgroups,
                        'deleteOldGroups' => false
                    ]);
                    $action->executeAction();
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
                    'isDisabled'     => 1,
                ]]);
            $action->executeAction();
        }
        $objectAction = new WowCharacterAction($this->objects, 'setWCFGroups');
        $objectAction->executeAction();
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
        $objectAction = new WowCharacterAction($this->objects, 'setWCFGroups');
        $objectAction->executeAction();
    }

	/**
     * function for rank changes.
     */
    public function setRank() {
            $guild = new Guild();
            $oldgroups = $guild->getGuildGroupIds(true);
            $newGroup = $guild->getGroupfromRank($this->parameters['rank']);
            //echo "Alte Gruppen (G): <pre>"; var_dump($oldgroups); echo "</pre> Neue Gruppe: <pre>"; var_dump($newGroup); echo "</pre>";

            if (!empty($oldgroups)) {
                $action = new WowCharacterAction($this->objects, 'removeFromGroups', [
                    'groups' => $oldgroups ,
                ]);
                $action->executeAction();
            }

            if ($newGroup) {
                $action = new WowCharacterAction($this->objects, 'addToGroups', [
                    'groups' => [$newGroup->groupID],
                    'deleteOldGroups' => false,
                    ]);
                $action->executeAction();
                $action = new WowCharacterAction($this->objects, 'update', [
                    'data' => [
                        'primaryGroup' => $newGroup->groupID,
                    ]]);
                $action->executeAction();
            }

            $objectAction = new WowCharacterAction($this->objects, 'setWCFGroups', [
                'oldWCFGroups' => $guild->convertToWCFGroup($oldgroups)
                ]);
            $objectAction->executeAction();
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
		}
	}


    public static function bulkUpdate($isCron = false, $forceALL = false) {
        $charList = new WowCharacterList();
        if (!$forceALL)  $charList->getConditionBuilder()->add('inGuild = ?', [1]);
        $charList->readObjectIDs();
        $charList->readObjects();
        $chars = $charList->getObjects();
        $updateList = [];
        $jobs = [];
        $counter = 0;
        foreach ($chars as $char) {
            $updateList[] = [
                'charInfo' => [
                        'name'  => $char->charname,
                        'realm' => $char->realmSlug,
                        'id'    => $char->characterID,
                    ],
                'bnetUpdate' => $char->bnetUpdate
            ];
            $counter++;
            if (!$isCron && $counter == GMAN_BNET_JOBSIZE) {
                $counter = 0;
                $jobs[] = new WowCharacterUpdateJob($updateList);
                $updateList = [];
            }
        }
        return ($isCron) ? $updateList : $jobs;
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