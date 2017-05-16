<?php
namespace wcf\data\guild\point\type;
use wcf\data\DatabaseObject;

/**
 * Represents a Punktetyp
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 * @property 		 $			 PRIMARY KEY
 * @property integer		 $typeID
 * @property integer		 $groupID			Gruppe
 * @property string		 $content			Text
 *
 */

class PointType extends DatabaseObject {
	/**
	 * {@inheritDoc}
	 */
	protected static $databaseTableName = 'gman_pointtype';
	/**
	 * {@inheritDoc}
	 */
	protected static $databaseTableIndexName = '';

}