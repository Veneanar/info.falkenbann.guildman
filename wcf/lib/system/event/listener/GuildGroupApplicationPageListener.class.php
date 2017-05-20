<?php
namespace wcf\system\event\listener;
use wcf\data\guild\group\application\GuildGroupApplicationCache;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows the application when in AppForum
 *
 * @author	Matthias Schmidt
 * @copyright	2001-2017 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	WoltLabSuite\Core\System\Event\Listener
 */
class GuildGroupApplicationPageListener implements IParameterizedEventListener {

	/**
     * @inheritDoc
     */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
        //echo "<pre>"; var_dump($eventObj->board); echo "</pre>"; die();
        if (!isset($eventObj->board->boardID)) return;
        $application = GuildGroupApplicationCache::getInstance()->getApplicationForBord($eventObj->board->boardID);
        if ($application !== null) {
            WCF::getTPL()->assign('guildGroupApplication', $application);
        }
	}
}