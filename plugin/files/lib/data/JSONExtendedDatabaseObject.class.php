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
    protected static $JSONfield = '';

    /**
     * Creates a new instance of the DatabaseObject class.
     *
     * @param	mixed			$id
     * @param	array			$row
     * @param	DatabaseObject		$object
     */
	public function __construct($id, array $row = null, DatabaseObject $object = null) {
        parent::__construct($id, $row, $object);
		if (isset($this->data[static::$JSONfield])) {
            $bnetData = json_decode($this->data[static::$JSONfield], true);
            $this->data = array_merge($bnetData, $this->data);
		}
	}
}