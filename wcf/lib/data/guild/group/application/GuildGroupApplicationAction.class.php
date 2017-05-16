<?php
namespace wcf\data\guild\group\application;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\wow\character\WowCharacterAction;
use wcf\data\wow\character\WowCharacterList;
use wcf\data\guild\group\GuildGroupList;
use wcf\data\guild\Guild;
use wcf\system\clipboard\ClipboardHandler;
use wcf\data\IClipboardAction;
use wcf\util\StringUtil;
use wcf\data\ISearchAction;
use wcf\system\cache\runtime\GuildRuntimeChache;

/**
 * Executes Gildenbewerbung-related actions.
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */

class GuildGroupApplicationAction extends AbstractDatabaseObjectAction {
	/**
	 * {@inheritDoc}
	 */
	public static $baseClass = GuildGroupApplicationEditor::class;
	/**
	 * {@inheritDoc}
	 */
	protected $permissionsUpdate = array('admin.gman.canEditGroups');
	/**
	 * {@inheritDoc}
	 */
	protected $permissionsCreate = array('admin.gman.canAddGroups');
	/**
	 * {@inheritDoc}
	 */
	protected $permissionsDelete = array('admin.gman.canDeleteGroups');
	/**
	 * {@inheritDoc}
	 */
	protected $requireACP = array('update', 'delete');
	/**
	 * {@inheritDoc}
	 */
	protected $allowGuestAccess = array();

}