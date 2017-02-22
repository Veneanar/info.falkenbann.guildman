<?php
namespace wcf\system\cronjob;
use wcf\data\wow\character\WowCharacterAction;
use wcf\system\wow\bnetAPI;
use wcf\system\background\BackgroundQueueHandler;
use wcf\data\cronjob\Cronjob;
/**
 * Updates the Chars in a guild table.
 *
 * Should run every 15-30 min.
 *
 * @author	Veneanar Falkenbann
 * @copyright 2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman

 */
class GuildCharUpdateCronjob extends AbstractCronjob {
    private static function doaction($wcfdir = '') {
        if (GMAN_BNET_USEEXTERNCRON) {
            $data = WowCharacterAction::bulkUpdate(true, false);
            bnetAPI::updateCharacter($data, $wcfdir);
        }
        if (GMAN_BNET_USEJOBS) {
            $data = WowCharacterAction::bulkUpdate(false, false);
            if (BackgroundQueueHandler::getInstance()->getRunnableCount() < 5) BackgroundQueueHandler::getInstance()->enqueueIn($data);
        }
    }
    /**
     * @see wcf\system\cronjob\ICronjob::execute()
     */
    public function execute(Cronjob $cronjob) {
        parent::execute($cronjob);
        if (GMAN_BNET_KEY != '') static::doaction();
    }
    public static function directExecute($wcfdir) {
        if (GMAN_BNET_KEY != '')  static::doaction($wcfdir);
    }
}
