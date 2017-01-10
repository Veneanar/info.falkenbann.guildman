<?php
namespace wcf\data\wow\realm;
use wcf\data\DatabaseObjectList;

/**
 * Represents a list of WoW Realmss.
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php> 
 * @package	info.falkenbann.guildman
 * 
 */

class WowRealmList extends DatabaseObjectList {
	/**
	 * {@inheritDoc}
	 */
	public static $baseClass = WowRealm::class;

}