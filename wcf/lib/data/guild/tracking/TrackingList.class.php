<?php
namespace wcf\data\guild\tracking;
use wcf\data\DatabaseObjectList;

/**
 * Represents a Tracking List
 * @author	Veneanar Falkenbann
 * @copyright	2016 -  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */
class TrackingList extends DatabaseObjectList {
	/**
     * {@inheritDoc}
     */
	public static $baseClass = Tracking::class;

	/**
     * {@inheritDoc}
     */
	public $sqlLimit = 0;
}