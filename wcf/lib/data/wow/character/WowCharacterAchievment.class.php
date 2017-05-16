<?php
namespace wcf\data\wow\character;
use wcf\data\DatabaseObject;
use wcf\util\JSON;
use wcf\system\WCF;

/**
 * Provides methods for WoW Charackter mit Items.
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */

class WowCharacterAchievment extends DatabaseObject {
	/**
     * database table for this object
     * @var	string
     */
	protected static $databaseTableName = 'gman_character_acms';

	/**
     * indicates if database table index is an identity column
     * @var	boolean
     */
	protected static $databaseTableIndexIsIdentity = false;

	/**
     * name of the primary index column
     * @var	string
     */
	protected static $databaseTableIndexName = 'charACMQ';

    public static function getForCharacter($characterID, $acmID) {
        $sql = "SELECT	*
				FROM	wcf".WCF_N."_gman_character_acms
                WHERE   characterID = ?
                AND     acmID = ?";
        $statement = WCF::getDB()->prepareStatement($sql, 1);
        $statement->execute([$characterID, $acmID]);
        $row = $statement->fetchArray();
        return !$row ? null : new WowCharacterAchievment(null, $row);
    }

    public function getCriteria() {
        return $this->acmQuantity;
    }

    public function isCompleted() {
        return $this->acmCompleted;
    }
}