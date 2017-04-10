<?php
namespace wcf\system\moderation\queue\activation;
use wcf\system\moderation\queue\AbstractModerationQueueHandler;
use wcf\data\moderation\queue\ModerationQueue;
use wcf\system\moderation\queue\ModerationQueueManager;
use wcf\data\moderation\queue\ViewableModerationQueue;
use wcf\system\moderation\queue\activation\IModerationQueueActivationHandler;
use wcf\system\WCF;
use wcf\data\wow\character\WowCharacter;
use wcf\data\wow\character\WowCharacterAction;
use wcf\data\wow\character\WowCharacterList;

/**
 * An implementation of ModerationQueueActivationHandler for wow character activation.
 *
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */
class WowCharacterQueueActivationHandler extends AbstractModerationQueueHandler implements  IModerationQueueActivationHandler {
    /**
     * database object class name
     * @var	string
     */
	protected $className = WowCharacter::class;
	/**
     * @inheritDoc
     */
	protected $definitionName = 'com.woltlab.wcf.moderation.activation';

	/**
     * @inheritDoc
     */
	protected $objectType = 'info.falkenbann.gman.moderation.charowner';

    /**
     * @inheritDoc
     */
	public function assignQueues(array $queues) {
		$assignments =  $orphanedQueueIDs = [];
		foreach ($queues as $queue) {
            $checkChar = $this->getCharacter($queue->objectID);
            // Der Char wurde anderweitig aktiviert z.b. via blizzard-connect oder via ACP
            if ($checkChar->userID > 0 || !$checkChar->isDisabled) {
                $orphanedQueueIDs[] = $queue->queueID;
            }
			$assignUser = false;
            if (WCF::getSession()->getPermission('mod.gman.canEditCharOwner'))  {
                $assignUser = true;
            }
			$assignments[$queue->queueID] = $assignUser;
		}
        ModerationQueueManager::getInstance()->removeOrphans($orphanedQueueIDs);
		ModerationQueueManager::getInstance()->setAssignment($assignments);
	}

	/**
     * @inheritDoc
     */
	public function canRemoveContent(ModerationQueue $queue) {
		if ($this->isValid($queue->objectID)) {
			return WCF::getSession()->getPermission('mod.gman.canEditCharOwner');
		}
		return false;
	}

	/**
     * @inheritDoc
     */
	public function enableContent(ModerationQueue $queue) {
        $wowChar = $this->getCharacter($queue->objectID);
		if ($this->isValid($queue->objectID) && $wowChar->isDisabled) {
			$characterAction = new WowCharacterAction([$wowChar], 'confirmUser');
			$characterAction->executeAction();
		}
	}

	/**
     * @inheritDoc
     */
	public function getDisabledContent(ViewableModerationQueue $queue) {
		$list = new WowCharacterList();
		/** @noinspection PhpUndefinedFieldInspection */
		$list->setObjectIDs([$queue->getAffectedObject()->characterID]);
		$list->readObjects();
		$chars = $list->getObjects();

		WCF::getTPL()->assign([
			'char' => reset($chars),
		]);
		return WCF::getTPL()->fetch('moderationCharEnable');
	}

	/**
     * Returns a wowchar object by charid or null if wowcharacter id is invalid.
     * prepared for caching wowchars
     *
     * @param	integer		$objectID
     * @return	WowCharacter
     */
	protected function getCharacter($objectID) {
		return new WowCharacter($objectID);
	}

	/**
     * @inheritDoc
     */
	public function getContainerID($objectID) {
		return 0;
	}

	/**
     * @inheritDoc
     */
	public function populate(array $queues) {
		$objectIDs = [];
		foreach ($queues as $object) {
			$objectIDs[] = $object->objectID;
		}
        $charList = new WowCharacterList();
		$charList->setObjectIDs($objectIDs);
		$charList->readObjects();
		$chars = $charList->getObjects();
		// fetch comments
		foreach ($queues as $object) {
			if ($chars[$object->objectID] !== null) {
				$object->setAffectedObject($chars[$object->objectID]);
			}
			else {
				$object->setIsOrphaned();
			}
		}
	}
	/**
     * @inheritDoc
     */
	public function isValid($objectID) {
		if ($this->getCharacter($objectID) === null) {
			return false;
		}
		return true;
	}

	/**
     * @inheritDoc
     */
	public function removeContent(ModerationQueue $queue, $message) {
		if ($this->isValid($queue->objectID)) {
			$characterAction = new WowCharacterAction([$this->getCharacter($queue->objectID)], 'declineUser');
			$characterAction->executeAction();
		}
	}
}