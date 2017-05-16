<?php
namespace wcf\data\guild\tracking;
use wcf\data\DatabaseObjectList;

/**
 * Represents a Tracked Data List
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */
class TrackingDataList extends DatabaseObjectList {
	/**
     * {@inheritDoc}
     */
	public static $baseClass = TrackingData::class;

	/**
     * sql limit
     * @var	integer
     */
	public $sqlLimit = 0;
}