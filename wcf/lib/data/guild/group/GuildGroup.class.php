<?php
namespace wcf\data\guild\group;
use wcf\data\DatabaseObject;
use wcf\system\WCF;
use wcf\data\user\group\UserGroup;
use wcf\system\request\IRouteController;
use wcf\system\request\LinkHandler;
use wcf\data\wow\character\WowCharacterList;
use wcf\data\wow\character\WowCharacter;

/**
 * Represents a Gildenbewerbung
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 * @property integer		 $groupID			    PRIMARY KEY
 * @property string		     $groupName			    Name der Gruppe
 * @property string		     $groupTeaser			    Kurzbeschreibung f. minibox und wowprogress
 * @property integer		 $wcfGroupID			Gruppen ID vom WSC
 * @property integer		 $showCalender			zeige im Kalender
 * @property integer		 $calendarCategoryID			zeige im Kalender
 * @property string		     $calendarTitle			Kalender Standarttitel
 * @property string		     $calendarText			Kalendar Stadrttext
 * @property integer		 $fetchCalendar			Synchronisiere WoW Kalender
 * @property string		     $calendarQuery		    Kalendererkennung
 * @property integer		 $gameRank			    Rang ID (0-10)
 * @property integer		 $showRoaster			zeige Gruppe im Roaster
 * @property integer		 $articIeID			    Artikel ID vom CMS
 * @property integer		 $threadID			    Themen ID vom Forum
 * @property integer		 $boardID			    Forum ID
 * @property integer		 $mediaID			    Medien ID (Bild)
 * @property integer		 $isRaidgruop			Ist das eine Raidgruppe
 * @property integer		 $fetchWCL			    synchronisiere mit WCL
 * @property string		     $wclQuery			    WCL name der Gruppe
 * @property integer		 $orderNo			    Sortierung
 * @property integer		 $lastUpdate			Letztes Update
 *
 */

class GuildGroup extends DatabaseObject implements IRouteController {
	/**
	 * {@inheritDoc}
	 */
	protected static $databaseTableName = 'gman_group';
	/**
	 * {@inheritDoc}
	 */
	protected static $databaseTableIndexName = 'groupID';
	/**
	 * WowCharacterList
	 */
    private $member = null;

	/**
     * Returns true if current user may delete this group.
     *
     * @return	boolean
     */
	public function isDeletable() {
		// insufficient permissions
		if (!WCF::getSession()->getPermission('admin.gman.canDeleteGroups')) return false;

        $userGroup = new UserGroup($this->wcfGroupID);
        if ($userGroup->getObjectID > 0) {
            return $userGroup->isDeletable();
        }
		return true;
	}

	/**
     * Returns true if current user may edit this group.
     *
     * @return	boolean
     */
	public function isEditable() {
		// insufficient permissions
		if (!WCF::getSession()->getPermission('admin.gman.canEditGroups')) return false;

        $userGroup = new UserGroup($this->wcfGroupID);
        if ($userGroup->getObjectID > 0) {
            return $userGroup->isEditable();
        }
		return true;
	}

    public function getTitle() {
        return $this->groupName;
    }
	/**
     * @inheritDoc
     */
	public function getLink() {
		return LinkHandler::getInstance()->getLink('User', [
			'application' => 'wcf',
			'object' => $this,
			'forceFrontend' => true
		]);
	}

    public function getMemberList() {
        if ($this->member === null) {
            $memberList = new WowCharacterList();
            $memberList->sqlJoins = "";
            $memberList->getConditionBuilder()->add("char_to_group.groupID = ?", [$this->groupID]);
            $memberList->sqlJoins .= " LEFT JOIN wcf".WCF_N."_gman_char_to_group char_to_group ON (gman_character.characterID = char_to_group.characterID)";
            $memberList->readObjects();
            $this->member = $memberList->getObjects();
        }
        return $this->member;
    }

    public function getMemberListRank($rank) {
        $memberList = new WowCharacterList();
        $memberList->getConditionBuilder()->add("gameRank = ?", [$rank]);
        $memberList->readObjects();
        return $memberList->getObjects();
    }

}