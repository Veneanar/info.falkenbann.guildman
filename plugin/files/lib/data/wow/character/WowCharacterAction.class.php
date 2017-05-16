<?php
namespace wcf\data\wow\character;
use wcf\data\ISearchAction;
use wcf\data\IValidateAction;
use wcf\data\ITabContentAction;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\exception\UserInputException;
use wcf\system\background\BackgroundQueueHandler;
use wcf\system\background\job\WowCharacterUpdateJob;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\moderation\queue\ModerationQueueActivationManager;
use wcf\data\IClipboardAction;
use wcf\system\WCF;
use wcf\data\guild\Guild;
use wcf\system\wow\bnetAPI;
use wcf\system\wow\bnetUpdate;
use wcf\data\user\User;
use wcf\data\user\UserEditor;
use wcf\data\user\avatar\UserAvatarAction;
use wcf\data\user\UserAction;
use wcf\data\user\group\ModeratedUserGroup;
use wcf\data\user\group\UserGroup;
use wcf\data\guild\group\GuildGroup;
use wcf\data\guild\group\GuildGroupList;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\UserException;
use wcf\util\StringUtil;
use wcf\util\JSON;
use wcf\system\cache\runtime\GuildRuntimeChache;
use wcf\data\wow\character\slot\CharacterSlotList;
use wcf\data\user\option\ViewableUserOption;
use wcf\data\guild\tracking\Tracking;
use wcf\data\guild\tracking\TrackingList;
use wcf\data\guild\group\application\GuildGroupApplication;

/**
 * Executes WoW Character-related actions.
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */

class WowCharacterAction extends AbstractDatabaseObjectAction implements ISearchAction, IClipboardAction, IValidateAction, ITabContentAction{

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
	protected $permissionsCreate = ['user.gman.canAddChar'];

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
	protected $allowGuestAccess = ['create', 'validateCreate', 'validateGetValidateResult', 'getSearchResultList', 'getValidateResult', 'getTabContent'];

    /**
     * @inheritDoc
     */
	public function validateGetValidateResult() {
		$this->readString('characterName', false, 'data');
		$this->readString('realmSlug', false, 'data');
	}

    /**
     * @inheritDoc
     */
	public function getValidateResult() {
        return bnetAPI::checkCharacter($this->parameters['data']['characterName'], $this->parameters['data']['realmSlug'], true);
	}

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

    /**
     * validation for updateData
     * @throws UserInputException
     */
    public function validateUpdateData() {
		// read objects
		if (empty($this->objects)) {
			$this->readObjects();

			if (empty($this->objects)) {
				throw new UserInputException('objectIDs');
			}
		}
        foreach($this->objects as $wowChar) {
            if ($wowChar->userID != WCF::getUser()->userID)  WCF::getSession()->checkPermissions(['mod.gman.canUpdateChar']);
        }
    }

    /**
     * updates wow character data
     */
    public function updateData() {
        foreach($this->objects as $wowChar) {
            bnetUpdate::updateCharacter([$wowChar], true);
        }
    }

    /**
     * validation for Update
     * @throws PermissionDeniedException
     */
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

    /**
     * validation fpr character creation
     */
    public function validateCreate() {
        parent::validateCreate();
        $this->validateGetValidateResult();
        $this->readBoolean('isAjax', false, 'data');
        $this->readInteger('userAdd', true, 'data');
        $this->readInteger('appID', true, 'data');
    }

    /**
     * validation for getTabContent
     */
    public function validateGetTabContent() {
        $this->readString('contentName', false, 'data');
    }

    /**
     * returns tab content for Character View
     * @return array
     */
    public function getTabContent() {
        $wowChar = $this->getSingleObject();
        $template = '';
        if ($this->parameters['data']['contentName']=='stats') {
            $template = WCF::getTPL()->fetch('_tabCharStatistics', 'wcf', ['viewChar' => $wowChar]);
        }
        $slotlist = new CharacterSlotList();
        $slotlist->readObjects();
        if ($this->parameters['data']['contentName']=='equip') {
            $template = WCF::getTPL()->fetch('_tabCharEquip', 'wcf', ['viewChar' => $wowChar, 'slotList' => $slotlist->getObjects()]);
        }
        if ($this->parameters['data']['contentName']=='activity') {
            $trackingList = new TrackingList();
            $trackingList->sqlOrderBy ='trackingOrderNo ASC';
            $trackingList->readObjects();
            $template = '';
            foreach ($trackingList->getObjects() as $tracking) {
                $template .= $tracking->renderTemplate($wowChar);
            }
        }
        return [
            'status'        => true,
            'contentName'   => $this->parameters['data']['contentName'],
            'template'      => $template,
        ];

    }

    /**
     * creates a new WoW Character
     * @return array
     */
    public function create() {
        $result = bnetAPI::checkCharacter($this->parameters['data']['characterName'], $this->parameters['data']['realmSlug'], true);
        if($result['status']) {
            $charID = $result['charID'];
            if (empty($charID)) $charID = bnetAPI::createCharacter($this->parameters['data']['characterName'], $this->parameters['data']['realmSlug'], true);
            $charObject = new WowCharacter($charID);
            bnetUpdate::updateCharacter([$charObject], true);
            if (isset($this->parameters['data']['userAdd']) && $this->parameters['data']['userAdd'] > 0) {
                try {
                	WCF::getSession()->checkPermissions(['user.gman.canAddCharOwner']);
                }
                catch (PermissionDeniedException $exception) {
                    return [
                        'status'    => true,
                        'template' => WCF::getTPL()->fetch('dialogAddChar', 'wcf', ['success' => false, 'char' => $charObject, 'msg' => 'denied']),
                        ];
                }
                try {
                	WCF::getSession()->checkPermissions(['user.gman.canAddCharOwnerWithoutModeration']);
                }
                catch (PermissionDeniedException $exception) {
                    $characterAction = new WowCharacterAction([$charObject], 'setUserModerated', [
                        'userID' => $this->parameters['data']['userAdd']
                    ]);
                    $characterAction->executeAction();
                    return [
                        'status'    => true,
                        'template' => WCF::getTPL()->fetch('dialogAddChar', 'wcf', ['success' => true, 'char' => $charObject, 'msg' => 'moderated']),
                        ];
                }
                $characterAction = new WowCharacterAction([$charObject], 'setUser', [
                    'userID' => $this->parameters['data']['userAdd']
                ]);
                $characterAction->executeAction();

            }
            if (isset($this->parameters['data']['isAjax'])) {
                return [
                    'status'    => true,
                    'message' => WCF::getTPL()->fetch('dialogAddChar', 'wcf', ['success' => true, 'char' => $charObject]),
                    'template' => WCF::getTPL()->fetch('_charSelection', 'wcf', [
                        'success' => true,
                        'char' => $charObject,
                        'application' => isset($this->parameters['data']['appID']) ? new GuildGroupApplication($this->parameters['data']['appID']) : null,
                        ])
                    ];
            }
            else {
                return [
                    'status'    => true,
                    'object'    => $charObject,
                    ];
            }
        }
        else {
            if (isset($this->parameters['data']['isAjax'])) {
                return [
                    'status'    => false,
                    'template' => WCF::getTPL()->fetch('itemTooltip', 'wcf', ['success' => false]),
                    ];
            }
            else {
                return [
                    'status'    => false,
                    'msg'       => $result['msg'],
                    ];
            }

       }
    }

    /**
     * Update Action
     */
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
            if ($wowChar->getOwner()->userID > 0 && $wowChar->getOwner()->userID != WCF::getUser()->userID && !$wowChar->getOwner()->canEdit()) {
                throw new PermissionDeniedException();
            }
        }
    }

	/**
     * Set current character as main character.
     */
    public function setMain() {
        /**
         * @var $wowChar WowCharacter
         */
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

        if ($wowChar->getOwner()->getUserOption('OverrideAvatar')) {
            $userEditor = new UserEditor($wowChar->getOwner());
            $userAvatarAction = new UserAvatarAction(array(), 'fetchRemoteAvatar', array(
                    'url'           => $wowChar->getInset()->getURL(),
                    'userEditor'    => $userEditor
                ));
            $userAvatarAction->executeAction();
        }
        $objectAction = new WowCharacterAction($this->objects, 'setWCFGroups');
        $objectAction->executeAction();

        $userOptionAction = new UserAction(array($wowChar->getOwner()), 'update', array(
         'options' => array(
            ViewableUserOption::getUserOption("characterID")->getObjectID() => $wowChar->characterID
            )
        ));
        $userOptionAction->executeAction();
    }

    /**
     * validation for user assignment
     * @throws UserInputException
     */
    public function validateSetUser() {
		if (empty($this->objects)) {
			$this->readObjects();

			if (empty($this->objects)) {
				throw new UserInputException('objectIDs');
			}
		}
        if (empty($this->parameters['userID'])) throw new UserInputException('userid');
        WCF::getSession()->checkPermissions(['user.gman.canAddCharOwner']);
    }

    /**
     * validation for moderated user assignement
     * @throws UserInputException
     */
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

    /**
     * Assigns a User to a Character (moderated)
     */
    public function setUserModerated() {

        foreach($this->objects as $wowChar) {
            $action = new WowCharacterAction([$wowChar], 'update', [
                'data' => [
                    'tempUserID'    => $this->parameters['userID'],
                    'isDisabled'      => 1,
                ]]);
            $action->executeAction();
            ModerationQueueActivationManager::getInstance()->addModeratedContent('info.falkenbann.gman.moderation.charowner', $wowChar->characterID);
        }
    }

    /**
     * Assigns a User to a Character
     */
    public function setUser() {
        try {
            WCF::getSession()->checkPermissions(['user.gman.canAddCharOwnerWithoutModeration']);
        }
        catch (PermissionDeniedException $exception) {
            $this->setUserModerated();
            return;
        }
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
            //echo "test: "; var_dump($wowChar->tempUserID); die();
            $action = new WowCharacterAction([$wowChar], 'update', [
                'data' => [
                    'userID'        => $wowChar->tempUserID,
                    'isDisabled'    => 0,
                    'tempUserID'    => 0,
                ]]);
            $action->executeAction();
        }
        $objectAction = new WowCharacterAction($this->objects, 'setWCFGroups');
        $objectAction->executeAction();
    }

	/**
     * Enables wowchar.
     */
	public function declineUser() {
        foreach($this->objects as $wowChar) {
            $action = new WowCharacterAction([$wowChar], 'update', [
                'data' => [
                    'isDisabled'  => 0,
                    'tempUserID'  => 0,
                ]]);
            $action->executeAction();
        }
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
       $guild = GuildRuntimeChache::getInstance()->getCachedObject();
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

    /**
     * removes Character from guild
     */
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
        $guild = GuildRuntimeChache::getInstance()->getCachedObject();
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
     * Validates a Group removal
     * @throws UserInputException
     * @throws PermissionDeniedException
     *
     */
    public function validateRemoveFromGroup() {
        $this->validateAddToGroup();
    }

    /**
     * Remove Chars from Guildgroup
     */
    public function removeFromGroup() {
		foreach ($this->getObjects() as $charEditor) {
			$charEditor->removeFromGroups([$this->parameters['groupID']]);
        }
    }

    /**
     * Validates a Group assignment
     * @throws UserInputException
     * @throws PermissionDeniedException
     */
    public function validateAddToGroup() {
		if (empty($this->objects)) {
			$this->readObjects();

			if (empty($this->objects)) {
				throw new UserInputException('objectIDs');
			}
		}
        $this->readInteger('groupID');
        $testgroup = new GuildGroup($this->parameters['groupID']);
        if ($testgroup->groupID == 0) throw new UserInputException('groupID', 'notfound');
        if ($testgroup->wcfGroupID > 0) {
            if (!$testgroup->isAccesible()) throw new PermissionDeniedException();
        }
    }

    /**
     * Adds Chars to guildgroups
     */
    public function addToGroup() {
		foreach ($this->getObjects() as $charEditor) {
			$charEditor->addToGroups([$this->parameters['groupID']], false, false);
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