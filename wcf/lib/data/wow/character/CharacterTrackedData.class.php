<?php
namespace wcf\data\wow\character;
use wcf\data\DatabaseObject;
use wcf\data\user\avatar\IUserAvatar;
use wcf\util\StringUtil;
use wcf\system\WCF;

/**
 * Represents a Tracked Data
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */
class CharacterTrackedData extends DatabaseObject {
	/**
     * {@inheritDoc}
     */
	protected static $databaseTableName = 'gman_character_tracked_statistics';
	/**
     * {@inheritDoc}
     */
	protected static $databaseTableIndexName = 'statID';

    public function getValue($isInt = true) {
        return $isInt ? $this->dataIntegerValue : $this->dataStringValue;
    }
    public function getDate() {
        return $this->dataTime;
    }

}
