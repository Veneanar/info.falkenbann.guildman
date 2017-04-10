<?php
namespace wcf\system\cronjob;
use wcf\data\wow\character\WowCharacterList;
use wcf\system\wow\bnetUpdate;
use wcf\data\cronjob\Cronjob;
use wcf\system\exception\SystemException;
/**
 * Updates the all Chars in the database.
 *
 * Should run once a day.
 *
 * @author	Veneanar Falkenbann
 * @copyright	2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman

 */
class AllCharactersUpdateCronjob extends AbstractCronjob {
    private static function doaction($wcfdir = '') {
        if (GMAN_BNET_USEEXTERNCRON) {
            $charListObject = new WowCharacterList();
            bnetUpdate::updateCharacter($charListObject->getAllCharacters(), false, $wcfdir);
        }
        if (GMAN_BNET_USEJOBS) {
            $charListObject = new WowCharacterList();
            bnetUpdate::updateCharacter($charListObject->getAllCharacters(true), false, $wcfdir);
        }
    }
    /**
     * @see wcf\system\cronjob\ICronjob::execute()
     */
    public function execute(Cronjob $cronjob) {
        parent::execute($cronjob);
        if (GMAN_BNET_KEY == '') static::doaction();
    }
    public static function directexecute($wcfdir) {
        if (GMAN_BNET_KEY == '')  static::doaction($wcfdir);
    }
}
