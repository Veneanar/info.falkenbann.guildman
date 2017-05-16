<?php
namespace wcf\data\wow\acm;
use wcf\data\DatabaseObjectList;

/**
 * Represents a list of WoW Guild Achievementss.
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php> 
 * @package	info.falkenbann.guildman
 * 
 */

class WowGuildACMList extends DatabaseObjectList {
	/**
	 * {@inheritDoc}
	 */
	public static $baseClass = WowGuildACM::class;

}