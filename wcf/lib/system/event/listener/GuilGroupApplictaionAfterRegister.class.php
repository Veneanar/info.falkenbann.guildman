<?php
namespace wcf\system\event\listener;
use wcf\data\guild\group\application\GuildGroupApplicationCache;
use wcf\system\WCF;
use wcf\util\StringUtil;
/**
 * Shows the application when in AppForum
 *
 * @author	Veneanar Falkenbann
 * @copyright	2016-2018 Sylvanas Garde e.V.
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	
 */
class GuilGroupApplictaionAfterRegister implements IParameterizedEventListener {
	/**
     * @inheritDoc
     */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
        //echo "<pre>"; var_dump($eventObj->board); echo "</pre>"; die();
        //if (!isset($eventObj->board->boardID)) return;
        //$application = GuildGroupApplicationCache::getInstance()->getApplicationForBord($eventObj->board->boardID);
        //if ($application !== null) {
        //    WCF::getTPL()->assign('guildGroupApplication', $application);
        //}
	}
}
