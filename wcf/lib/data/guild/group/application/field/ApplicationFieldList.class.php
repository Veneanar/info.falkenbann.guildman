<?php
namespace wcf\data\guild\group\application\field;
use wcf\data\DatabaseObjectList;
/**
 * Represents a list of application fields.
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */
class ApplicationFieldList extends DatabaseObjectList {
	/**
	 * {@inheritDoc}
	 */
	public static $baseClass = ApplicationField::class;

}
