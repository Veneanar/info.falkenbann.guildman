<?php
namespace wcf\data\wow\character;
use wcf\data\guild\Guild;
use wcf\data\guild\group\GuildGroup;
use wcf\data\JSONExtendedDatabaseObject;
use wcf\system\request\IRouteController;
use wcf\system\request\LinkHandler;
use wcf\data\wow\WowClasses;
use wcf\data\wow\WowRace;
use wcf\data\user\User;
use wcf\system\WCF;

/**
 * Represents a WoW Charackter
 * @author	Veneanar Falkenbann
 * @copyright	2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 * @property string		            $charID		                    Name des Charackters-Realm
 * @property integer		        $userID                         WCF UserID
 * @property integer		        $isMain			                ist Hauptchar
 * @property integer		        $inGuild		                ist in der Gilde
 * @property integer		        $realmID			            ID des realms (wow/data)
 * @property string		            $bnetdata			            Daten d. Characters JSON (api.battle.net no field)
 * @property integer		        $primaryGroup		            Primär ID der Gruppe (guid/groups)
 * @property string		            $groups			                weitere Gruppen
 * @property integer		        $bnetUpdate			            Letztes Update (intern)
 * @property integer		        $firstSeen			            Eintragedatum
 * @property integer                $guildRank
 * @property-read	integer			$lastModified					Aktualesierungszeitpunkt des Charakters
 * @property-read	string			$name							Name des Charakters
 * @property-read	string			$realm							Server auf dem der Character beheimatet ist
 * @property-read	string			$battlegroup					Servergruppe in dem der Character sich befindet
 * @property-read	integer			$class							Klasse des Charakters
 * @property-read	integer			$race							Rasse des Charakters
 * @property-read	integer			$gender							Geschlecht des Charakters
 * @property-read	integer			$level							Level des Charakters
 * @property-read	integer			$achievementPoints				Erfolgspunkte des Charakters
 * @property-read	string			$thumbnail						Charakterbild
 * @property-read	string			$calcClass						???
 * @property-read	integer			$faction						Fraktion des Charakters
 * @property-read	integer			$totalHonorableKills			Ehrenpunkte des Charakters
 * @property integer                $isDisabled                     ist der Char aktiviert?
 * @property integer                $tempUserID                     save groupinfos.
 *
 */

class WowCharacter extends JSONExtendedDatabaseObject implements IRouteController {
	/**
	 * {@inheritDoc}
	 */
	protected static $databaseTableName = 'gman_wow_character';
	/**
	 * {@inheritDoc}
	 */
	protected static $databaseTableIndexName = 'charID';
	/**
     * {@inheritDoc}
     */
    protected static $JSONfield = 'bnetData';

    /**
     * saves the chars's avatar.
     *
     * @var	\wcf\data\user\avatar\IUserAvatar
     */
    private $avatar = null;

    /**
     * saves the chars's profilemain.
     *
     * @var	\wcf\data\user\avatar\IUserAvatar
     */
    private $profilemain = null;

    /**
     * saves the chars's inset.
     *
     * @var	\wcf\data\user\avatar\IUserAvatar
     */
    private $inset = null;

    /**
     * saves the chars's equip.
     *
     * @var	WowCharacterItemSet
     */
    private $equip = null;

    /**
     * saves the chars's class information.
     * @var WowClasses
     */
    private $classData = null;

    /**
     * saves the chars's rank name information.
     * @var string
     */
    private $rankName = '';

    /**
     * saves the chars's race inofrmation.
     * @var WoWRace
     */
    private $raceData = null;

    /**
     * saves the chars's group IDs.
     * @var integer[]
     */
    private $groupIDs = [];

    /**
     * saves the chars's groups.
     * @var GuildGroup[]
     */
    private $groups = [];


    /**
     * Returns the user's avatar.
     *
     * @return	\wcf\data\user\avatar\IUserAvatar
     */
	public function getAvatar() {
        if ($this->avatar === null) {
            if ($this->thumbnail) {
                $this->avatar = new WowCharacterAvatar($this, "avatar");
            } else {
                $this->avatar = new WowDefaultCharacterAvatar($this->race, $this->gender, "avatar");
            }
            if (!file_exists($this->avatar->getLocation()) ) {
                $this->avatar = new WowDefaultCharacterAvatar($this->race, $this->gender, "avatar");
            }
        }
        return $this->avatar;
    }

    /**
     * Returns the user's inset.
     *
     * @return	\wcf\data\user\avatar\IUserAvatar
     */
	public function getInset() {
        if ($this->inset === null) {
            if ($this->thumbnail) {
                $this->inset = new WowCharacterAvatar($this, "inset");
            } else {
                $this->inset = new WowDefaultCharacterAvatar($this->race, $this->gender,"inset");
            }
            if (!file_exists($this->inset->getLocation()) ) {
                $this->inset = new WowDefaultCharacterAvatar($this->race, $this->gender,"inset");
            }
        }
        return $this->inset;
    }

    /**
     * Returns the user's inset.
     *
     * @return	WowCharacterItemSet
     */
	public function getEquip() {
        if ($this->equip === null) {
            $this->equip = new WowCharacterItemSet($this);
        }
        return $this->equip;
    }

    /**
     * Returns the user's inset.
     *
     * @return	\wcf\data\user\avatar\IUserAvatar
     */
	public function getProfileMain() {
        if ($this->profilemain === null) {
            if ($this->thumbnail) {
                $this->profilemain = new WowCharacterAvatar($this, "profilemain");
            } else {
                $this->profilemain = new WowDefaultCharacterAvatar($this->race, $this->gender,"profilemain");
            }
            if (!file_exists($this->avatar->getLocation()) ) {
                $this->profilemain = new WowDefaultCharacterAvatar($this->race, $this->gender,"profilemain");
            }
        }
        return $this->profilemain;
    }

	/**
     * Returns localized level information from actual character .
     *
     * @return	string
     */
    public function getLevel() {
        return WCF::getLanguage()->get('wcf.page.gman.wow.level') . $this->level;
    }

	/**
     * Returns race information from actual character .
     *
     * @return	WowRace
     */
    public function getRace() {
        if ($this->raceData === null)  $this->raceData = new WowRace($this->race);
        return $this->raceData;
    }

	/**
     * Returns rank information from actual character .
     *
     * @return	string
     */
    public function getRank() {
        if (empty($this->rankName))  {
            $myGuild = new Guild();
            $this->rankName = $myGuild->getRankName($this->guildRank);
        }
        return $this->rankName;
    }

	/**
     * Returns class information from actual character .
     *
     * @return	WowClasses
     */
    public function getClass() {
        if ($this->classData === null)  $this->classData = new WowClasses($this->class);
        return $this->classData;
    }

	/**
     * Returns character name and details.
     *
     * @return	string
     */
    public function getNice($realm = false, $long = true) {
        return $this->name . $realm ? ' ('.$this->realm.')' : ''. $long ? ' '. $this->getLevel() . ' '. $this->raceData->name . ' '. $this->classData->name : '';
    }

	/**
     * Returns character name and details as HTML Output.
     *
     * @return	string
     */
    public function getNiceTag($realm = false, $long = true) {
        if ($long) {
            return  $this->name . $realm ? ' ('.$this->realm.') ' : ' ' . $this->getLevel() . ' '. $this->raceData->getTag() . ' '. $this->classData->getTag();

        } else {
            return '<span color="'.$this->classData->color.'">'. $this->name . $realm ? ' ('.$this->realm.')' : '' .'</span>';
        }

    }

	/**
     * @inheritDoc
     */
	public function getTitle() {
		return $this->name;
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

    /**
     * Returns an array with all the groups in which the actual character is a member.
     *
     * @return	integer[]
     */
	private function getGroupIDs() {
		if ($this->groupIDs === null) {
            $this->groupIDs[] = 0;
			$sql = "SELECT	groupID
						FROM	wcf".WCF_N."_gman_char_to_group
						WHERE	charID = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute([$this->charID]);
			$this->groupIDs = $statement->fetchAll(\PDO::FETCH_COLUMN);
			sort($this->groupIDs, SORT_NUMERIC);
		}
		return $this->groupIDs;
	}

    /**
     * Returns an array with all the groups in which the actual character is a member.
     *
     * @return	GuildGroup[]
     */
    public function getGroups() {
        if (empty($this->groups)) {
            foreach ($this->groupIDs as $groupID) {
                if ($groupID > 0) {
                    $this->groups[] = new GuildGroup($groupID);
                }
            }
        }
        return $this->groups;
    }


    public function getAccountGroups() {
        $accountList = new WowCharacterList();
        $accountList->getConditionBuilder()->add('WHERE userID = ?', [$this->userID]);
        $accountList->readObjects();
        $charList = $accountList->getObjects();
        $groupIDs = [];
        foreach($charList as $char) {
            $groupIDs[] = $char->getGroupIDs();
        }
        return array_unique($groupIDs);
    }

	/**
     * Returns true if current user may delete this group.
     *
     * @return	boolean
     */
	public function isDeletable() {
		// insufficient permissions
		if (!WCF::getSession()->getPermission('admin.gman.canDeleteGroups')) return false;

        $user = new User($this->userID);
        if ($user->getObjectID > 0) {
            return $user->isDeletable();
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

        $user = new User($this->userID);
        if ($user->getObjectID > 0) {
            return $user->isEditable();
        }
		return true;
	}
}