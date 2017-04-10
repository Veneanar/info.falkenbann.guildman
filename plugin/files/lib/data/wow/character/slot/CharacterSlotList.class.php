<?php
namespace wcf\data\wow\character\slot;
use wcf\data\DatabaseObjectList;

/**
 * Represents a list of WoW Charackters.
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */

class CharacterSlotList extends DatabaseObjectList {
	/**
     * {@inheritDoc}
     */
	public static $baseClass = CharacterSlot::class;

}