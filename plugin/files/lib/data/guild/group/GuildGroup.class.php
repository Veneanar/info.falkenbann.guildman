<?php
namespace wcf\data\guild\group;
use wcf\system\WCF;
use wcf\system\WCFACP;
use wcf\system\request\LinkHandler;
use wcf\system\request\IRouteController;
use wcf\data\DatabaseObject;
use wcf\data\media\Media;
use wcf\data\media\ViewableMedia;
use wcf\data\user\group\UserGroup;
use wcf\data\user\User;
use wcf\data\wow\character\WowCharacter;
use wcf\data\wow\character\WowCharacterList;
use wcf\system\database\util\PreparedStatementConditionBuilder;


/**
 * Represents a Gildgroup
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 * @property integer		 $groupID			    PRIMARY KEY
 * @property string		     $groupName			    Name der Gruppe
 * @property string		     $groupTeaser			Kurzbeschreibung f. minibox und wowprogress
 * @property integer		 $wcfGroupID			Gruppen ID vom WSC
 * @property integer		 $showCalender			zeige im Kalender
 * @property integer		 $calendarCategoryID	zeige im Kalender
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
     * WoWCharacterList
	 */
    private $leader = null;

    /**
     * Group icon
     * @var ViewableMedia
     */
    private $icon = null;

    /**
     * Group image
     * @var ViewableMedia
     */
    private $image = null;

    /**
     * WCF Group Object
     * @var userGroup
     */
    private $wcfGroup = null;

    /**
     * temporary member object
     * @var WoWCharacter
     */
    private $tMember = null;

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

    /** Returns the Groups leader
     *
     * @return WoWCharacterList
     */
    public function getLeader() {
        if ($this->leader === null) {
            $leaderList = new WowCharacterList();
            $leaderList->sqlJoins = "";
            $leaderList->getConditionBuilder()->add("group_leader.groupID = ?", [$this->groupID]);
            $leaderList->sqlJoins .= " LEFT JOIN wcf".WCF_N."_gman_group_leader group_leader ON (gman_character.characterID = group_leader.leaderID)";
            $leaderList->readObjects();
            $this->leader = $leaderList->getObjects();
        }
        return $this->leader;
    }

    /**
     * get Chaname if user is member
     * @param User $user
     * @return null|WowCharacter
     */
    public function getMemberNameFromUser(User $user) {
        if ($this->tMember===null) {
            if ($this->isMember(null, $user)) {
                return $this->tMember;
            }
            else {
                return null;
            }
        }
        return $this->tMember;
    }




    /**
     * checks if a char or an user is member of this group.
     * @param WowCharacter $wowChar
     * @param User $user
     * @return bool
     */
    public function isMember(WowCharacter $wowChar = null, User $user = null) {
        if ($wowChar !== null) {
            $sql = "SELECT	*
			    FROM		wcf".WCF_N."_gman_char_to_group
			    WHERE		groupID = ?
                AND         characterID = ?";
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute([$this->getObjectID(), $wowChar->getObjectID()]);
            $row = $statement->fetchArray();
            if (!$row) return false;
            return true;
        }
        if ($user !== null) {
            $userCharList = new WowCharacterList();
            $userCharList->getConditionBuilder()->add("userID = ?", [$user->getObjectID()]);
            $userCharList->readObjectIDs();

            $conditions = new PreparedStatementConditionBuilder();
            $conditions->add("groupID = ?", [$this->getObjectID()]);
            $conditions->add("characterID IN (?)", [$userCharList->getObjectIDs()]);
            $sql = "SELECT	characterID, groupID
			FROM	wcf".WCF_N."_gman_char_to_group
			".$conditions;
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute($conditions->getParameters());
            $row = $statement->fetchArray();
            if (!$row) return false;
            $this->tMember = new WowCharacter($row['characterID']);
            return true;
        }
        return false;
    }



    /**
     * Checks if a user or a character is leader. if omitted the params the session user will be used.
     *
     * @param integer $id either the ID of an User or a WoWCharacter
     * @param boolean $isUser determins which type of id given. if omitted, $id is used as UserID
     *
     * @return bool
     */
    public function isLeader($id = 0, $isUser = true) {
        if ($id==0) $id = WCF::getUser()->userID;
        $leaderList = $this->getLeader();
        foreach($leaderList as $wowChar) {
            if ($isUser) {
                if ($wowChar->userID == $id) return true;
            }
            else {
                if ($wowChar->characterID == $id) return true;
            }
        }
        return false;
    }

	/**
     * Returns true if current user may edit this group.
     *
     * @return	boolean
     */
	public function isAccesible() {
		// insufficient permissions
		if (!WCF::getSession()->getPermission('admin.gman.canAddCharsToGroups')) {
            if (WCF::getSession()->getPermission('admin.gman.canAddCharsToOwnGroup')) {
                if (!$this->isLeader()) return false;
            }
        }
        $userGroup = new UserGroup($this->wcfGroupID);
        if ($userGroup->getObjectID > 0) {
            return $userGroup->isAccessible();
        }
		return true;
	}

    /**
     * get GroupIcon
     * @return ViewableMedia
     */
    public function getIcon() {
        if ($this->icon===null) {
            $this->icon = new ViewableMedia(new Media($this->iconID));
            // Fallback einbauen!
        }
        return $this->icon;
    }

    /**
     * Get Group Image
     * @return ViewableMedia
     */
    public function getImage() {
        if ($this->image===null) {
            $this->image = new ViewableMedia(new Media($this->imageID));
            // Fallback einbauen!

        }
        return $this->image;
    }

    /**
     * Get Group by Groupname
     * @param string $name
     * @return GuildGroup
     */
    public static function getByName($name) {
        $sql = "SELECT	*
			    FROM		wcf".WCF_N."_gman_group
			    WHERE		groupName LIKE ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([$name]);
		$row = $statement->fetchArray();
		if (!$row) $row = [];
		return new GuildGroup(null, $row);
    }

    /**
     * get Menmberlist of the group
     * @return WowCharacter[]
     */
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

    /**
     * get Memberlist with grouprank (deprecated)
     * @param mixed $rank
     * @return DatabaseObject[]
     */
    public function getMemberListRank($rank) {
        $memberList = new WowCharacterList();
        $memberList->getConditionBuilder()->add("gameRank = ?", [$rank]);
        $memberList->readObjects();
        return $memberList->getObjects();
    }

    /**
     * @inheritDoc
     */
	public function getLink() {
		return (class_exists(WCFACP::class, false) || !PACKAGE_ID) ?
            LinkHandler::getInstance()->getLink('GuildGroupEdit', [
			'application'   => 'wcf',
            'isACP'         => true,
			'object'        => $this,
		        ]) :
            LinkHandler::getInstance()->getLink('GuildGroup', [
			'application'   => 'wcf',
			'object'        => $this,
		        ]);
	}

	/**
     * @inheritDoc
     */
	public function getTitle() {
		return $this->groupName;
	}
}