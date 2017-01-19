<?php
namespace wcf\acp\page;
use wcf\data\guild\group\GuildGroup;
use wcf\data\guild\group\GuildGroupList;
use wcf\page\SortablePage;
use wcf\system\WCF;

/**
 * Shows a list of all WoW Groups
 *
 * @author	    Veneanar Falkenbann
 * @copyright	2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	    GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	    info.falkenbann.guildman
 *
 * @property	GuildGroupList		$objectList
 */
class GuildGroupListPage extends SortablePage {
	/**
     * @inheritDoc
     */
	public $activeMenuItem = 'wcf.acp.menu.link.gman.group.list';

	/**
     * @inheritDoc
     */
	public $neededPermissions = ['admin.gman.canEditGroups', 'admin.gman.canDeleteGroups'];

	/**
     * @inheritDoc
     */
	public $defaultSortField = 'groupName';

	/**
     * @inheritDoc
     */
	public $validSortFields = ['groupID', 'groupName', 'gameRank', 'gameTitle', 'orderNo'];

	/**
     * @inheritDoc
     */
	public $objectListClassName = GuildGroupList::class;

	/**
     * indicates if a group has just been deleted
     * @var	integer
     */
	public $deletedGroups = 0;

	/**
     * @inheritDoc
     */
	public function readParameters() {
		parent::readParameters();

		// detect group deletion
		if (isset($_REQUEST['deletedGroups'])) {
			$this->deletedGroups = intval($_REQUEST['deletedGroups']);
		}
	}

	/**
     * @inheritDoc
     */
	protected function initObjectList() {
		parent::initObjectList();
		$this->objectList->sqlSelects .= "(SELECT COUNT(*) FROM wcf".WCF_N."_gman_char_to_group WHERE groupID = gman_group.groupID) AS members";
	}

	/**
     * @inheritDoc
     */
	protected function readObjects() {
		$this->sqlOrderBy = ($this->sortField != 'members' ? 'user_group.' : '').$this->sortField." ".$this->sortOrder;

		parent::readObjects();
	}

	/**
     * @inheritDoc
     */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign([
			'deletedGroups' => $this->deletedGroups
		]);
	}
}
