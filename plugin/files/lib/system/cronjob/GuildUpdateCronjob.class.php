<?php
namespace wcf\system\cronjob;
use wcf\system\wow\bnetUpdate;
use wcf\data\cronjob\Cronjob;
use wcf\system\exception\SystemException;
/**
 * Updates the Char Events table.
 *
 * Should run every 4 hours.
 *
 * @author	Veneanar Falkenbann
 * @copyright	2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman

 */
class GuildUpdateCronjob extends AbstractCronjob {
    private static function doaction() {
        bnetUpdate::updateGuild();
    }
    /**
     * @see wcf\system\cronjob\ICronjob::execute()
     */
    public function execute(Cronjob $cronjob) {
        parent::execute($cronjob);
        if (GMAN_BNET_KEY == '') static::doaction();
    }
    public static function directexecute() {
        if (GMAN_BNET_KEY == '')  static::doaction();
    }
}
