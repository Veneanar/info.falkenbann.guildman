<?php
namespace wcf\data;

/**
 * Every database object action whose objects can be validated via AJAX has to
 * implement this interface.
 *
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 */
interface IValidateAction {
	/**
	 * Returns true if value is valid
	 *
     * <code>
     * $array = array(
     *   'status'  => integer,  // the id
     *   'msg'   => string,     // the message
     * );
     * </code>
     *
     * @return	array   $array
	 */
	public function getValidateResult();

	/**
     * Validates the "getValidateResult" action.
	 */
	public function validateGetValidateResult();
}
