<?php
namespace wcf\acp\page;
use wcf\data\guild\group\GuildGroup;
use wcf\system\exception\NamedUserException;
use wcf\data\guild\Guild;
use wcf\data\guild\group\GuildGroupList;
use wcf\page\SortablePage;
use wcf\system\WCF;
use wcf\system\cache\runtime\GuildRuntimeChache;

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
class GuildGroupApplicationListPage extends SortablePage {

	/**
     * @inheritDoc
     */
	public $activeMenuItem = 'wcf.acp.menu.link.gman.grouplist';

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
	public $validSortFields = ['groupID', 'groupName', 'gameRank', 'gameTitle', 'members','orderNo'];

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
     * guild Object
     * @var	Guild
     */
    public $guild = null;

	/**
     * @inheritDoc
     */
	public function readParameters() {
		parent::readParameters();
        // check guild
        $this->guild = GuildRuntimeChache::getInstance()->getCachedObject();
        if ($this->guild->name == null) {
            throw new NamedUserException(WCF::getLanguage()->get('wcf.acp.notice.gman.noguild'));
        }

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
		$this->sqlOrderBy = ($this->sortField != 'members' ? 'gman_group.' : '').$this->sortField." ".$this->sortOrder;

		parent::readObjects();
	}

	/**
     * @inheritDoc
     */
	public function assignVariables() {
		parent::assignVariables();
		WCF::getTPL()->assign([
            'guild' => $this->guild,
			'deletedGroups' => $this->deletedGroups
		]);
	}
}
