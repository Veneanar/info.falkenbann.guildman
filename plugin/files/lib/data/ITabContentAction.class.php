<?php
namespace wcf\data;

/**
 * every loadable tab action musst implement this interface
 * implement this interface.
 *
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 */
interface ITabContentAction {
	/**
     * Returns the Tab Content
     *
     * @return	array
     */
	public function getTabContent();

	/**
     * Validates the "getTabContent" action.
     */
	public function validateGetTabContent();
}
