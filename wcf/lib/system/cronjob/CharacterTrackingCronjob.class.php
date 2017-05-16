<?php
namespace wcf\system\cronjob;
use wcf\data\guild\tracking\TrackingList;
use wcf\system\cache\runtime\GuildRuntimeChache;
use wcf\data\wow\character\WowCharacterList;
use wcf\system\WCF;
use wcf\data\cronjob\Cronjob;
/**
 * Updates the tracking variables for all max level chars.
 *
 * Should run once a day between 01:00-03:00
 *
 * @author	Veneanar Falkenbann
 * @copyright 2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman

 */
class CharacterTrackingCronjob extends AbstractCronjob {
    private static function doaction() {
        $guild = GuildRuntimeChache::getInstance()->getCachedObject();
        $timeout = TIME_NOW - (GMAN_BNET_TRACKINGDATA * 24 * 3600) + 3600;
        $sql = "DELETE FROM wcf".WCF_N."_gman_character_tracked_statistics WHERE dataTime < ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([$timeout]);
        $sql = "DELETE FROM wcf".WCF_N."_gman_character_feedlist WHERE feedTime < ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([$timeout]);
        $trackingList = new TrackingList();
        $trackingList->readObjects();
        $charListObject = new WowCharacterList();
        $charListObject->getConditionBuilder()->add('c_level = ?', [$guild->currentMaxLevel]);
        $charListObject->readObjects();

        $chacrterList = $charListObject->getObjects();

        foreach ($chacrterList as $character) {
            foreach ($trackingList->getObjects() as $trackingObject) {
                $trackingObject->collectData($character);
            }
        }
    }
    /**
     * @see wcf\system\cronjob\ICronjob::execute()
     */
    public function execute(Cronjob $cronjob) {
        parent::execute($cronjob);
        if (GMAN_BNET_KEY != '') static::doaction();
    }
    public static function directExecute() {
        if (GMAN_BNET_KEY != '')  static::doaction();
    }
}
