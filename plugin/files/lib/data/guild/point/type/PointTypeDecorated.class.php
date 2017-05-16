<?php
namespace wcf\data\guild\point\type;
use wcf\data\DatabaseObjectDecorator;

/**
 * Provides methods for Punktetyp.
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php> 
 * @package	info.falkenbann.guildman
 * 
 */

class PointTypeDecorated extends DatabaseObjectDecorator {
	/**
	 * {@inheritDoc}
	 */
	public static $baseClass = PointType::class;

}