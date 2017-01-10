<?php
namespace wcf\data\guild\point\type;
use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit Punktetyps.
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php> 
 * @package	info.falkenbann.guildman
 * 
 */

class PointTypeEditor extends DatabaseObjectEditor {
	/**
	 * {@inheritDoc}
	 */
	public static $baseClass = PointType::class;

}