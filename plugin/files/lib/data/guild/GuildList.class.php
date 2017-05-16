<?php
namespace wcf\data\guild;
use wcf\data\DatabaseObjectList;

/**
 * Represents a list of Gildenbewerbungs.
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php> 
 * @package	info.falkenbann.guildman
 * 
 */

class GuildList extends DatabaseObjectList {
	/**
	 * {@inheritDoc}
	 */
	public static $baseClass = Guild::class;

}