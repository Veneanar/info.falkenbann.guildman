<?php
namespace wcf\data\guild\application;
use wcf\data\DatabaseObjectDecorator;

/**
 * Provides methods for Gildenbewerbung.
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php> 
 * @package	info.falkenbann.guildman
 * 
 */

class GuildApplicationDecorated extends DatabaseObjectDecorator {
	/**
	 * {@inheritDoc}
	 */
	public static $baseClass = GuildApplication::class;

}