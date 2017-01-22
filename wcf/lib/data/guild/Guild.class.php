<?php
namespace wcf\data\guild;
use wcf\data\JSONExtendedDatabaseObject;
use wcf\data\DatabaseObject;
use wcf\data\wow\realm\WowRealm;
use wcf\data\wow\character\WowCharacter;
use wcf\data\guild\group\GuildGroup;
use wcf\data\guild\group\GuildGroupList;
use wcf\data\media\Media;
use wcf\data\media\ViewableMedia;
use wcf\system\WCF;

/**
 * Represents a Gildenbewerbung
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 * @property
 * @property integer		        $guildID			            PRIMARY KEY
 * @property integer		        $articleID			            Article ID
 * @property integer		        $pageID			                Umfrage ID
 * @property integer		        $leaderID			            Character ID
 * @property integer		        $birthday			            Gildengeburtstag
 * @property integer		        $logoID			                Media ID des Logos
 * @property string		            $bnetData
 * @property integer		        $bnetUpdate
 * @property integer		        $guildRank
 * @property-read	integer			$lastModified					Letztes Update der Gildeninformationen
 * @property-read	string			$name							Name der Gilde
 * @property-read	string			$realm							Name des Servers wo die Gilde beheimatet ist
 * @property-read	string			$battlegroup					Servergruppe des Servers
 * @property-read	integer			$level							Level der Gilde
 * @property-read	integer			$side							Fraktion die der Gilde angehört
 * @property-read	integer			$achievementPoints				Erfolgspunkte der Gilde
 * @property-read	array			$emblem							Gildenlogo
 *
 *
 */

class Guild extends JSONExtendedDatabaseObject {
	/**
	 * {@inheritDoc}
	 */
	protected static $databaseTableName = 'gman_guild';
	/**
	 * {@inheritDoc}
	 */
	protected static $databaseTableIndexName = '';
	/**
     * {@inheritDoc}
     */
    protected static $JSONfield = 'bnetData';
    /**
     * The Guild Logo
     *
     * @var	Media
     */
    private $logo = null;

    /**
     * The guild's relam(s).
     *
     * @var WowRealm
     */
    private $homeRealm = null;

    /**
     * The guild leader.
     *
     * @var WowCharacter
     */
    private $leader = null;

    /**
     * ranklist
     *
     * @var array
     */
    private $rankList = [];

    /**
     * List of Groups not releated to a wow rank
     *
     * @var group\GuildGroup[]
     */
    private $GuildGroupsNotWoW;

    /**
     * List of Groups related to a wow rank
     *
     * @var group\GuildGroup[]
     */
    private $GuildGroupsWoW;

    /**
     * List of Groups IDsnot releated to a wow rank
     *
     * @var integer[]
     */
    private $GuildGroupsNotWoWIDs;

    /**
     * List of Group IDs related to a wow rank
     *
     * @var integer[]
     */
    private $GuildGroupsWoWIDs;

    /**
     * Returns the Guildleader
     *
     * @return WowCharacter
     */
    public function getLeader() {
        if ($this->leader===null) {
            $sql = "SELECT	*
			    FROM		wcf".WCF_N."_gman_wow_character
			    WHERE		guildRank = 0";
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute();
            $row = $statement->fetchArray();
            if (!$row) $row = [];
            $this->leader = new WowCharacter(null, $row);;
        }
        return $this->leader;
    }

    /**
     * Returns the guild's realm(s)
     *
     * @return WoWRealm
     */
    public function getRealm() {
        if ($this->homeRealm===null) {
            $this->homeRealm =  new WowRealm($this->realm);
        }
        return $this->homeRealm;
    }

    /**
     * Returns the guild's logo
     *
     * @return	Media
     */
    public function getLogo() {
        if ($this->logo===null) {
            $this->logo = new Media($this->logoID);
        }
        return $this->logo;
    }

	public function __construct() {
        $sql = "SELECT	*
				FROM	".static::getDatabaseTableName();
        $statement = \wcf\system\WCF::getDB()->prepareStatement($sql, 1);
        $statement->execute();
        $row = $statement->fetchSingleRow();
        if (!$row) return null;
        parent::__construct(null, $row, null);
	}

    public function getFaction() {
        $factext = WCF::getLanguage()->get('wcf.page.gman.wow.horde');
        if ($this->side==0) {
            $factext = WCF::getLanguage()->get('wcf.page.gman.wow.alliance');
        }
        return $factext;
    }
    public function getFactionTag() {
        $color = '#AA0000';
        if ($this->side==0) {
            $color = '#144587';
        }
        return '<span style="color:'.$color.'">'. $this->getFaction .'</span>';
    }

    public function getGuildGroups($onlyWow = false) {
        if ($this->GuildGroupsWoW===null) {
            $guildGroups = new GuildGroupList();
            $guildGroups->getConditionBuilder()->add("gameRank < 11");
            $guildGroups->readObjects();
            $this->GuildGroupsWoW = $guildGroups->getObjects();
            $this->GuildGroupsWoWIDs = $guildGroups->getObjectIDs();
            $guildGroups = new GuildGroupList();
            $guildGroups->getConditionBuilder()->add("gameRank = 11");
            $guildGroups->readObjects();
            $this->GuildGroupsNotWoW = $guildGroups->getObjects();
            $this->GuildGroupsNotWoWIDs = $guildGroups->getObjectIDs();
        }
        return $onlyWow ? $this->GuildGroupsWoW : array_merge($this->GuildGroupsWoW, $this->GuildGroupsNotWoW);
    }

    public function getGuildGroupIds($onlyWow = false) {
        if ($this->GuildGroupsWoW===null) {
            $this->getGuildGroups();
        }
        return $onlyWow ? $this->GuildGroupsWoWIDs : array_merge($this->GuildGroupsWoWIDs, $this->GuildGroupsNotWoWIDs);
    }

    public function convertToWCFGroup(array $idList) {
        if ($this->GuildGroupsWoW===null) {
            $this->getGuildGroups();
        }
        $groupList = $this->getGuildGroups;
        /**
         * @var $group  group\GuildGroup
         */
        $convert = [];
        foreach($idList as $id) {
            foreach($groupList as $group) {
                if ($group->groupID == $id) {
                    $convert[] = $group->wcfGroupID;
                    continue;
                }
            }
        }
        return $convert;
    }


    public function getGroupfromRank($rank) {
        $groupList = $this->getGuildGroups(true);
        foreach($groupList as $group) {
            if ($group->gameRank == $rank) return $group;
        }
        return null;
    }

    public function getRanks() {
        if (empty($this->rankList)) {
            if (strlen(GMAN_MAIN_RANK0) > 1) $this->rankList[] = ['rankID' => 0, 'rankName' => GMAN_MAIN_RANK0];
            if (strlen(GMAN_MAIN_RANK1) > 1) $this->rankList[] = ['rankID' => 1, 'rankName' => GMAN_MAIN_RANK1];
            if (strlen(GMAN_MAIN_RANK2) > 1) $this->rankList[] = ['rankID' => 2, 'rankName' => GMAN_MAIN_RANK2];
            if (strlen(GMAN_MAIN_RANK3) > 1) $this->rankList[] = ['rankID' => 3, 'rankName' => GMAN_MAIN_RANK3];
            if (strlen(GMAN_MAIN_RANK4) > 1) $this->rankList[] = ['rankID' => 4, 'rankName' => GMAN_MAIN_RANK4];
            if (strlen(GMAN_MAIN_RANK5) > 1) $this->rankList[] = ['rankID' => 5, 'rankName' => GMAN_MAIN_RANK5];
            if (strlen(GMAN_MAIN_RANK6) > 1) $this->rankList[] = ['rankID' => 6, 'rankName' => GMAN_MAIN_RANK6];
            if (strlen(GMAN_MAIN_RANK7) > 1) $this->rankList[] = ['rankID' => 7, 'rankName' => GMAN_MAIN_RANK7];
            if (strlen(GMAN_MAIN_RANK8) > 1) $this->rankList[] = ['rankID' => 8, 'rankName' => GMAN_MAIN_RANK8];
            if (strlen(GMAN_MAIN_RANK9) > 1) $this->rankList[] = ['rankID' => 9, 'rankName' => GMAN_MAIN_RANK9];
        }
        return $this->rankList;
    }
}
