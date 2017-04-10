<?php
namespace wcf\data;

/**
 * Every wow object which returns a tooltip via AJAX has to
 * implement this interface.
 *
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 */
interface ITooltipAction {
	/**
	 * Returns a WoW Tooltip
	 *
     * @return	array
	 */
	public function getTooltip();

	/**
     * Validates the "getTooltip" action.
	 */
	public function validateGetTooltip();
}
