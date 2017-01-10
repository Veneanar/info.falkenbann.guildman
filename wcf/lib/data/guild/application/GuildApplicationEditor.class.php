<?php
namespace wcf\data\guild\application;
use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit Gildenbewerbungs.
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php> 
 * @package	info.falkenbann.guildman
 * 
 */

class GuildApplicationEditor extends DatabaseObjectEditor {
	/**
	 * {@inheritDoc}
	 */
	public static $baseClass = GuildApplication::class;

}