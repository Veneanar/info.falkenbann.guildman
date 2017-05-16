<?php
namespace wcf\data\wow\character;
use wcf\system\WCF;
use wcf\util\JSON;
use wcf\system\WCFACP;
use wcf\system\request\LinkHandler;
use wcf\system\request\IRouteController;
use wcf\system\cache\runtime\GuildRuntimeChache;
use wcf\data\IUserContent;
use wcf\data\user\User;
use wcf\data\user\UserProfile;
use wcf\data\guild\Guild;
use wcf\data\guild\group\GuildGroup;
use wcf\data\wow\WowRace;
use wcf\data\wow\WowClasses;
use wcf\data\wow\realm\WowRealm;
use wcf\data\JSONExtendedDatabaseObject;
use wbb\data\thread\Thread;
use wbb\data\post\Post;
use wcf\data\wow\item\WowItem;
use wcf\data\wow\item\ViewableWowItem;

/**
 * Represents a WoW Charackter
 * @author	Veneanar Falkenbann
 * @copyright	2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 * @property integer                $characterID                    Char ID
 * @property string		            $charname		                Name des Chars
 * @property string		            $realmSlug		                Realmslug
 * @property string		            $realmName		                Realmname
 * @property string                 $accountID                      account identifier
 * @property integer                $c_level                        chached level
 * @property integer                $c_acms                         chached acms amount
 * @property integer                $c_race                         chached race
 * @property integer                $c_class                        chached class
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
class WowCharacter extends JSONExtendedDatabaseObject implements IRouteController, IUserContent {

	/**
	 * {@inheritDoc}
	 */
	protected static $databaseTableName = 'gman_character';

	/**
	 * {@inheritDoc}
	 */
	protected static $databaseTableIndexName = 'characterID';

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
     * Character Statistics
     * @var CharacterStatistics
     */
    private $charStatistics = null;

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
     * saves the chars's Useraccount.
     * @var User
     */
    private $user = null;

    /**
     * tracked bosskills
     * @var mixed
     */
    private $trackedBosskills = [];

    /**
     * internal benchmark
     * @var float
     */
    private $lastcall = 0;

    /**
     * char's acms
     * @var WowCharacterAchievment[]
     */
    private $wowAcm = [];


    /**
     * Returns a WoWChar Object from a given char and realm name
     *
     * @return	WowCharacter|null
     */
	public static function getByCharAndRealm($name, $realm, $isSlug = true) {
        if (!$isSlug) {
            $realm = WowRealm::getByName($realm);
            if ($realm===null) return null;
        }
        $sql = "SELECT	*
			    FROM		wcf".WCF_N."_gman_character
			    WHERE		charname LIKE ?
                AND         realmslug LIKE ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([$name, $realm]);
		$row = $statement->fetchArray();
		if (!$row) $row = [];
		return new WowCharacter(null, $row);
    }

    /**
     * returns the main Charcter from a User
     * @param mixed $userID
     * @return WowCharacter
     */
    public static function getMainCharacterFromUser($userID) {
        $sql = "SELECT	*
			    FROM		wcf".WCF_N."_gman_character
			    WHERE		userID = ?
                AND         isMain = 1";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([$userID]);
		$row = $statement->fetchArray();
		if (!$row) $row = [];
		return new WowCharacter(null, $row);
    }

    /**
     * returns the mainchar from a given WOwChar either ID or Bject
     * @param integer $characterID
     * @param WowCharacter $wowChar
     * @return null|WowCharacter
     */
    public static function getMainCharacterFromCharacter($characterID = null, WowCharacter $wowChar = null) {
        $charObj = new WowCharacter($characterID, null, $wowChar);
		return $charObj->userID > 0 ? static::getMainCharacterFromUser($charObj->userID) : null;
    }

    /**
     * checks and corrects a charinfo array
     *  @param  $charInfo   array   Information about the character request: name, realm, id
     *  @param  $fuzzy      boolean if set, the realm parameter is not neccesary. the code will return the first character with given name
     *  @return	array|null
     */
    public static function completeCharInfo(array $charInfo, $fuzzy = false) {
        $obj = null;
        // keine ID?
        if (empty($charInfo['id'])) {
            if (empty($charInfo['name'])) return null; // ohne namen: keine chance!
            // kein Realm gefunden
            if (empty($charInfo['realm'])) {
                if (!$fuzzy) return null;
                // Such ungenau? Dann nimm den erstbesten Character den du findest, ignoriere den Realm.
                $sql = "SELECT	*
			    FROM		wcf".WCF_N."_gman_character
			    WHERE		charname LIKE ?
                LIMIT 1";
                $statement = WCF::getDB()->prepareStatement($sql);
                $statement->execute([$charInfo['name']]);
                $row = $statement->fetchArray();
                if (!$row) return null; // kein Char gefunden -> null!
                $obj = new WowCharacter(null, $row);
            }
            else {
                // Realm angegeben, Prüfe auf Slug
                if (WowRealm::isSlug($charInfo['realm'])) {
                    $ret = WowRealm::getByName($charInfo['realm']);
                    if ($ret===null) return null;
                    $charInfo['realm'] = $ret->slug;
                    // ersetze den Realmnamen durch den slugnamen
                }
                // Realmname und char geprüft, erstelle ein Object
                $obj = static::getByCharAndRealm($charInfo['name'], $charInfo['realm']);
            }
        }
        // ID gegeben
        else {
            // prüfe ob einer der werte leer ist, oder realm name
            if (empty($charInfo['name']) || empty($charInfo['realm']) ) {
                $obj = new WowCharacter($charInfo['id']);
            }
            else {
            // ID vorhanden, realm vorhanden und name vorhanden. Prüfe auf slugname.
                if (WowRealm::isSlug($charInfo['realm'])) {
                    $ret = WowRealm::getByName($charInfo['realm']);
                    if ($ret===null) return null;
                    $charInfo['realm'] = $ret->slug;
                    // ersetze den Realmnamen durch den slugnamen
                }
                // Alle daten geprüft, kein Änderung notwendig.
                return $charInfo;
            }
        }
        // irgendetwas war nicht in Ordnung, korrigierte Daten werden zurückgegeben.
        return [
            'id'    => $obj->characterID,
            'name'  => $obj->charname,
            'realm' => $obj->realmSlug,
            ];
    }

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
     * returns Character Statistics
     *
     * @return CharacterStatistics
     */
    public function getCharacterStatistics() {
        if ($this->charStatistics === null) {
            $this->charStatistics = new CharacterStatistics($this->characterID);
        }
        return $this->charStatistics;
    }

    /**
     * Returns the char's ItemSet.
     *
     * @return	WowCharacterItemSet
     */
	public function getEquip($slotID = 0) {
        if ($this->equip === null) {
            $this->equip = new WowCharacterItemSet($this->characterID);
        }
        return $this->equip;
    }

    /**
     * Returns the char's profile picture.
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
            if (!file_exists($this->profilemain->getLocation()) ) {
                $this->profilemain = new WowDefaultCharacterAvatar($this->race, $this->gender,"profilemain");
            }
        }
        return $this->profilemain;
    }

	/**
     * @inheritDoc
     */
    public function getObjectID() {
        return $this->characterID;
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
            $myGuild = GuildRuntimeChache::getInstance()->getCachedObject();
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
     *  @param  $realm  boolean show Realm
     *  @param  $long   boolean show all Information
     * @return	string
     */
    public function getNice($realm = false, $long = true) {
        return $this->name . $realm ? ' ('.$this->realm.')' : ''. $long ? ' '. $this->getLevel() . ' '. $this->getRace()->name  . ' '. $this->getClass()->name : '';
    }

	/**
     * Returns character name and details as HTML Output.
     *
     * @return	string
     */
    public function getNiceTag($realm = false, $long = true) {
        if ($long) {
            return  $this->name . $realm ? ' ('.$this->realm.') ' : ' ' . $this->getLevel() . ' '. $this->getRace()->getTag() . ' '. $this->getClass()->getTag();

        } else {
            return '<span color="'.$this->getClass()->color.'">'. $this->name . $realm ? ' ('.$this->realm.')' : '' .'</span>';
        }

    }

    /**
     * Returns an array with all the groups in which the actual character is a member.
     *
     * @return	integer[]
     */
	public function getGroupIDs() {
		if (empty($this->groupIDs)) {
            $this->groupIDs[] = 0;
			$sql = "SELECT	groupID
						FROM	wcf".WCF_N."_gman_char_to_group
						WHERE	characterID = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute([$this->characterID]);
			$this->groupIDs = $statement->fetchAll(\PDO::FETCH_COLUMN);
			sort($this->groupIDs, SORT_NUMERIC);
		}
		return array_filter($this->groupIDs, function($a) { return ($a !== 0); });
	}

    public function getTrackedStatistics() {

    }

    /**
     * Returns an array with all the groups in which the actual character is a member.
     *
     * @return	GuildGroup[]
     */
    public function getGroups() {
        if (empty($this->groups)) {
            $gids = $this->getGroupIDs();
            foreach ($gids as $groupID) {
                if ($groupID > 0) {
                    $this->groups[] = new GuildGroup($groupID);
                }
            }
        }
        return $this->groups;
    }

    /**
     * Returns the owner as User
     *
     * @return	User
     */
    public function getOwner() {
        if ($this->user===null) $this->user = new User($this->userID);
        return $this->user;
    }

    /**
     * Returns the owner as User
     *
     * @return	User
     */
    public function getQueuedOwner($profile = false) {
        if ($this->getOwner()->userID == 0) $this->user = new User($this->tempUserID);
        return $profile ? new UserProfile($this->user) : $this->user;
    }

    /**
     * Returns the owners grouplist
     *
     * @return	Integer[]
     */
    public function getAccountGroupIDs() {
        $accountList = new WowCharacterList();
        $accountList->getConditionBuilder()->add('userID = ?', [$this->userID]);
        $accountList->readObjects();
        $charList = $accountList->getObjects();
        $groupIDs = [];
        foreach($charList as $char) {
            $groupIDs = array_merge($groupIDs, $char->getGroupIDs());
        }
        return array_filter(array_unique($groupIDs), function($a) { return ($a !== 0); });
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
     * Returns true if current user may edit this char.
     *
     * @return	boolean
     */
	public function isEditable() {
		// insufficient permissions

        $user = new User($this->userID);
        if ($user->getObjectID > 0) {
            return $user->canEdit();
        }
        else {
            if (WCF::getSession()->getPermission('user.gman.canAddCharOwner')) return true;
        }
		return false;
	}

    /**
     * @inheritDoc
     */
	public function getLink() {
		return (class_exists(WCFACP::class, false) || !PACKAGE_ID) ?
            LinkHandler::getInstance()->getLink('CharacterEdit', [
			'application'   => 'wcf',
			'object'        => $this,
            'isACP'         => true,
		    ]):
            LinkHandler::getInstance()->getLink('ArmoryChar', [
			'application'   => 'wcf',
			'object'        => $this,
		    ]);

	}

	/**
     * @inheritDoc
     */
	public function getTitle() {
		return $this->name;
	}
	/**
     * Returns message creation timestamp.
     *
     * @return	integer
     */

	public function getTime() {
        return $this->bnetUpdate;
    }

	/**
     * Returns author's user id.
     *
     * @return	integer
     */
	public function getUserID() {
        return $this->getQueuedOwner()->userID;
    }

	/**
     * Returns author's username.
     *
     * @return	string
     */
	public function getUsername() {
        return $this->getQueuedOwner()->username;
    }

    /**
     * internal benchmarktool
     * @return double|int
     */
    public function getRuntime() {
        if ($this->lastcall==0) {
            $this->lastcall = microtime(true);
        }
        $retval = microtime(true) - $this->lastcall;
        $this->lastcall = microtime(true);
        return round($retval,4);
    }
    /**
     * Summary of getChartext
     * @return string
     */
    public function getChartext() {
        if (empty($this->charText)) {
            return WCF::getLanguage()->get("wcf.page.gman.armory.char.nostory");
        }
        return $this->charText;
    }

    public function getWowArsenalLink() {
            $host = '';
            if (GMAN_BNET_REGION == 'eu.api.battle.net') {
                $host = 'http://eu.battle.net/wow/de/character/';
            }
            elseif (GMAN_BNET_REGION == 'us.api.battle.net') {
                $host = 'http://us.battle.net/wow/de/character/';
            }
            elseif (GMAN_BNET_REGION == 'kr.api.battle.net') {
                $host = 'http://kr.battle.net/wow/de/character/';
            }
            elseif (GMAN_BNET_REGION == 'tw.api.battle.net') {
                $host = 'http://tw.battle.net/wow/de/character/';
            }
            else {
                $host = 'http://us.battle.net/wow/de/character/';
            }
            return $host . $this->realmSlug .'/'.urlencode($this->name);
    }

    public function getWowProgressLink() {
        $host = '';
        if (GMAN_BNET_REGION == 'eu.api.battle.net') {
            $host = 'https://www.wowprogress.com/character/eu/';
        }
        elseif (GMAN_BNET_REGION == 'us.api.battle.net') {
            $host = 'https://www.wowprogress.com/character/us/';
        }
        elseif (GMAN_BNET_REGION == 'kr.api.battle.net') {
            $host = 'https://www.wowprogress.com/character/kr/';
        }
        elseif (GMAN_BNET_REGION == 'tw.api.battle.net') {
            $host = 'https://www.wowprogress.com/character/tw/';
        }
        else {
            $host = 'https://www.wowprogress.com/character/us/';
        }
        return $host . $this->realmSlug .'/'.$this->name;
    }

    public function getWarcraftlogsLink() {
        return 'https://www.warcraftlogs.com/';
    }

    public function getHighestMythicDungeon() {
        date_default_timezone_set('UTC');
        $time1 = strtotime("last " .GMAN_BNET_FIRSTDAYOFWEEK) + (3600 * 3);
        $sql = "SELECT * FROM wcf".WCF_N."_gman_character_feedlist
                WHERE characterID = ?
                AND feedTime > ?
                AND context LIKE 'challenge-mode-jackpot'";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([$this->characterID, $time1]);
        $row = $statement->fetchSingleRow();
        if (!isset($row['itemID'])) return 0;
        $bonus = isset($row['bonusLists']) ? JSON::decode($row['bonusLists']) : [];
        $item = new ViewableWowItem(new WowItem($row['itemID']), 'challenge-mode-jackpot', $bonus);
        if(preg_match('/\d+/',$item->nameDescription ,$matches) == true){
            return $matches[0];
        }
        return 0;
    }

    public function getAchievment($acmID) {
        if (!isset($this->wowAcm[$acmID])) {
            $this->wowAcm[$acmID] = WowCharacterAchievment::getForCharacter($this->characterID, $acmID);
        }
        return $this->wowAcm[$acmID];
    }
}