<?php
namespace wcf\system\moderation\queue\activation;
use wcf\system\moderation\queue\AbstractModerationQueueHandler;
use wcf\data\moderation\queue\ModerationQueue;
use wcf\data\moderation\queue\ViewableModerationQueue;
use wcf\system\moderation\queue\activation\IModerationQueueActivationHandler;
use wcf\system\WCF;
use wcf\data\wow\character\WowCharacter;
use wcf\data\wow\character\WowCharacterAction;
use wcf\data\wow\character\WowCharacterList;

/**
 * An implementation of IModerationQueueActivationHandler for forum posts.
 *
 * @author	Alexander Ebert
 * @copyright	2001-2017 WoltLab GmbH
 * @license	WoltLab License <http://www.woltlab.com/license-agreement.html>
 * @package	WoltLabSuite\Forum\System\Moderation\Queue\Activation
 */
class WowCharacterQueueActivationHandler extends AbstractModerationQueueHandler implements IModerationQueueActivationHandler {
    /**
     * database object class name
     * @var	string
     */
	protected $className = WowCharacter::class;
	/**
     * @inheritDoc
     */
	protected $definitionName = 'com.woltlab.wcf.moderation.type';

	/**
     * @inheritDoc
     */
	protected $objectType = 'info.falkenbann.gman.moderation.charowner';

	/**
     * @inheritDoc
     */
	public function enableContent(ModerationQueue $queue) {
        $wowChar = new WowCharacter($queue->objectID);
		if ($this->isValid($queue->objectID) && $wowChar->isDisabled) {
			$postAction = new WowCharacterAction([$wowChar], 'enable');
			$postAction->executeAction();
		}
	}

	/**
     * @inheritDoc
     */
	public function getDisabledContent(ViewableModerationQueue $queue) {
		$list = new WowCharacterList();
		/** @noinspection PhpUndefinedFieldInspection */
		$list->setObjectIDs([$queue->getAffectedObject()->charID]);
		$list->readObjects();
		$chars = $list->getObjects();

		WCF::getTPL()->assign([
			'char' => reset($chars),
		]);
		return WCF::getTPL()->fetch('moderationCharEnable');
	}
}