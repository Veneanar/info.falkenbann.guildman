<?php
namespace wcf\data\guild\group\application;
use wcf\data\DatabaseObjectList;
/**
 * Represents a list of group applications.
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */
class GuildGroupApplicationList extends DatabaseObjectList {
	/**
	 * {@inheritDoc}
	 */
	public static $baseClass = GuildGroupApplication::class;

}
