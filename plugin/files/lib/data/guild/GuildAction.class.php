<?php
namespace wcf\data\guild;
use wcf\data\AbstractDatabaseObjectAction;

/**
 * Executes Gildenbewerbung-related actions.
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php> 
 * @package	info.falkenbann.guildman
 * 
 */

class GuildAction extends AbstractDatabaseObjectAction {
	/**
	 * {@inheritDoc}
	 */
	public static $baseClass = GuildEditor::class;
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