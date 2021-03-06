<?php
namespace wcf\system\cronjob;
use wcf\data\wow\character\WowCharacterAction;
use wcf\system\wow\bnetUpdate;
use wcf\data\cronjob\Cronjob;
use wcf\system\exception\SystemException;
/**
 * Updates the all Bosses in the database.
 *
 * Should run once a day.
 *
 * @author	Veneanar Falkenbann
 * @copyright	2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman

 */
class BossesUpdateCronjob extends AbstractCronjob {
    private static function doaction() {
        bnetUpdate::updateRaidBosses();
    }
    /**
     * @see wcf\system\cronjob\ICronjob::execute()
     */
    public function execute(Cronjob $cronjob) {
        parent::execute($cronjob);
        if (GMAN_BNET_KEY == '') static::doaction();
    }
    public static function directexecute($wcfdir) {
        if (GMAN_BNET_KEY == '')  static::doaction();
    }
}
