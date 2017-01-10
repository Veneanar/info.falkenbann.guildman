<?php
namespace wcf\data\guild\point\type;
use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes Punktetyp-related actions.
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php> 
 * @package	info.falkenbann.guildman
 * 
 */

class PointTypeAction extends AbstractDatabaseObjectAction {
	/**
	 * {@inheritDoc}
	 */
	public static $baseClass = PointTypeEditor::class;
	/**
	 * {@inheritDoc}
	 */
	protected $permissionsUpdate = array();
	/**
	 * {@inheritDoc}
	 */
	protected $permissionsCreate = array();
	/**
	 * {@inheritDoc}
	 */
	protected $permissionsDelete = array();
	/**
	 * {@inheritDoc}
	 */
	protected $requireACP = array();
	/**
	 * {@inheritDoc}
	 */
	protected $allowGuestAccess = array();

}