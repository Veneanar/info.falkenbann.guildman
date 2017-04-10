<?php
namespace wcf\data\guild\bosskill;
use wcf\data\DatabaseObjectList;

/**
 * Represents a list of Bosskills.
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */

class BosskillList extends DatabaseObjectList {
	/**
	 * {@inheritDoc}
	 */
	public static $baseClass = Bosskill::class;

}