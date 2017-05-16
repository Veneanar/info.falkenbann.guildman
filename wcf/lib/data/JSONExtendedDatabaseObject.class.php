<?php
namespace wcf\data;
use wcf\system\WCF;

/**
 * Abstract class for all JSON extended data holder classes.
 *
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 */

abstract class JSONExtendedDatabaseObject extends DatabaseObject {

    /**
     * name of the JSON field
     * @var	string
     */
    protected static $JSONfield = 'bnetData';

    /**
     * Creates a new instance of the DatabaseObject class.
     *
     * @param	mixed			$id
     * @param	array			$row
     * @param	DatabaseObject		$object
     */
	public function __construct($id, array $row = null, DatabaseObject $object = null) {
		if ($id !== null) {
			$sql = "SELECT	*
				FROM	".static::getDatabaseTableName()."
				WHERE	".static::getDatabaseTableIndexName()." = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute([$id]);
			$row = $statement->fetchArray();

			// enforce data type 'array'
			if ($row === false) $row = [];
		}
		else if ($object !== null) {
			$row = $object->data;
		}

		if (isset($row[static::$JSONfield])) {
            $bnetData = empty($row[static::$JSONfield]) ? [] : json_decode($row[static::$JSONfield], true);
            $row[static::$JSONfield] = '';
            $row = array_merge($bnetData, $row);
		}
		$this->handleData($row);

	}
}