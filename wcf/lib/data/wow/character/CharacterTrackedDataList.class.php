<?php
namespace wcf\data\wow\character;
use wcf\data\DatabaseObjectList;
use wcf\system\WCF;

/**
 * Represents a Tracked Data
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */
class CharacterTrackedDataList extends DatabaseObjectList {
	/**
     * {@inheritDoc}
     */
	public $className = CharacterTrackedData::class;

    static public function getDateList($dataID) {
        $retval = [];
        $sql = "SELECT DISTINCT dataTime FROM wcf".WCF_N."_gman_character_tracked_statistics WHERE dataID = ? ORDER BY dataTime ASC";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([$dataID]);
        while ($row = $statement->fetchArray()) {
            $retval[] = $row["dataTime"];
        }
        return $retval;
    }
}