<?php
namespace wcf\acp\page;
use wcf\data\wow\character\WowCharacter;
use wcf\data\guild\Guild;
use wcf\data\wow\character\WowCharacterList;
use wcf\system\event\EventHandler;
use wcf\system\clipboard\ClipboardHandler;
use wcf\system\exception\NamedUserException;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\cache\runtime\GuildRuntimeChache;
use wcf\page\SortablePage;
use wcf\data\wow\WoWClassesList;
use wcf\data\wow\WowRaceList;
use wcf\system\WCF;
use wcf\data\user\User;
use wcf\util\StringUtil;

/**
 * Shows a list of all WoW Groups
 *
 * @author	    Veneanar Falkenbann
 * @copyright	2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	    GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	    info.falkenbann.guildman
 *
 * @property	WowCharacterList		$objectList
 */
class GuildGroupListPage extends SortablePage {
	/**
     * @inheritDoc
     */
	public $activeMenuItem = 'wcf.menu.link.gman.armorylist';

	/**
     * @inheritDoc
     */
	public $defaultSortField = 'charname';

	/**
     * @inheritDoc
     */
	public $validSortFields = ['characterID', 'charname', 'realmSlug', 'guildRank', 'c_level', 'c_race', 'c_class', 'averageItemLevel','averageItemLevelEquipped'];

	/**
     * indicates if a group has just been deleted
     * @var	integer
     */
	public $deletedChars = 0;

	/**
     * guild Object
     * @var	WoWCharacter[]
     */
    public $chars = [];

	/**
     * owner Object
     * @var	User
     */
    public $ownerObject = null;

    /**
     * condition builder for user filtering
     * @var	PreparedStatementConditionBuilder
     */
	public $conditions = null;

	/**
     * guild Object
     * @var	Guild
     */
    public $guild = null;

    /**
     * groupID
     * @var	integer
     */
	public $groupID = 0;

    /**
     * classID
     * @var	integer
     */
	public $classID = 0;

    /**
     * raceID
     * @var	integer
     */
	public $raceID = 0;

    /**
     * ownerID
     * @var	integer
     */
	public $ownerID = 0;
    /**
     * mminumum avg itemlevel
     * @var	integer
     */
    public $minAVGILVL = 0;
    /**
     * mminumum char level
     * @var	integer
     */
    public $minLevel = 0;
    /**
     * rank filter
     * @var	integer
     */
    public $rankID = -1;

    /**
     * minumum avg ilvl equiped
     * @var	integer
     */
    public $minAVGILVLequipped = 0;

    /**
     * groupID from filtered Grouplist
     * @var	integer
     */
    public $getGroupList = 0;
    /**
     * charName to search for
     * @var	string
     */
    public $charName = '';



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
        if (!empty($_REQUEST['charName']))      $this->charName     = StringUtil::trim($_REQUEST['charName']);
        if (!empty($_REQUEST['raceID']))        $this->raceID       = intval($_REQUEST['raceID']);
		if (!empty($_REQUEST['classID']))       $this->classID      = intval($_REQUEST['classID']);
		if (isset($_REQUEST['rankID']))         $this->rankID       = intval($_REQUEST['rankID']);
        if (!empty($_REQUEST['minAVGILVL']))    $this->minAVGILVL   = intval($_REQUEST['minAVGILVL']);
        if (!empty($_REQUEST['minLevel']))      $this->minLevel     = intval($_REQUEST['minLevel']);
        if (!empty($_REQUEST['ownerName']))         {
            $this->ownerObject = User::getUserByUsername(StringUtil::trim($_REQUEST['ownerName']));
            if ($this->ownerObject->userID > 0) $this->ownerID = $this->ownerObject->userID;
        }

		$this->conditions = new PreparedStatementConditionBuilder();
        if ($this->raceID > 0) {
			$this->conditions->add('c_race = ?', [$this->raceID]);
		}
        if ($this->classID > 0) {
			$this->conditions->add('c_class = ?', [$this->classID]);
		}
        if ($this->ownerID > 0) {
			$this->conditions->add('userID = ?', [$this->ownerID]);
		}
        if ($this->rankID > -1) {
			$this->conditions->add('guildRank = ?', [$this->rankID]);
		}
        if ($this->minLevel > 0) {
			$this->conditions->add('c_level >= ?', [$this->minLevel]);
		}
        if ($this->minAVGILVL > 0) {
			$this->conditions->add('averageItemLevel >= ?', [$this->minAVGILVL]);
		}
        if ($this->minAVGILVLequipped > 0) {
			$this->conditions->add('averageItemLevelEquipped >= ?', [$this->minAVGILVLequipped]);
		}
        if (!empty($this->charName)) {
			$this->conditions->add('gman_character.charname LIKE ?', ["%".$this->charName."%"]);
		}
	}

	/**
     * @inheritDoc
     */
	public function readData() {
		parent::readData();
        $this->readChars();
    }


    /**
     * @inheritDoc
     */
	protected function initObjectList() {
		// does nothing
	}

    /**
     * @inheritDoc
     */
	public function countItems() {
		// call countItems event
		EventHandler::getInstance()->fireAction($this, 'countItems');

		$sql = "SELECT	COUNT(*)
			FROM	wcf".WCF_N."_gman_character gman_character
            LEFT JOIN wcf".WCF_N."_gman_character_equip gman_character_equip ON (gman_character_equip.characterID = gman_character.characterID)
			".$this->conditions;
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute($this->conditions->getParameters());

		return $statement->fetchColumn();
	}

    protected function readChars() {
        $sql = '';
        if ($this->getGroupList > 0) {
            $sql = "SELECT		char_to_group.characterID
			FROM		wcf".WCF_N."_gman_char_to_group char_to_group
            LEFT JOIN wcf".WCF_N."_gman_character gman_character ON (char_to_group.characterID = gman_character.characterID)
			LEFT JOIN wcf".WCF_N."_gman_character_equip gman_character_equip ON (gman_character_equip.characterID = gman_character.characterID)
			".$this->conditions."
			ORDER BY	".($this->sortField)." ".$this->sortOrder;
        }
        else {
            $sql = "SELECT		gman_character.characterID
			FROM		wcf".WCF_N."_gman_character gman_character
			LEFT JOIN wcf".WCF_N."_gman_character_equip gman_character_equip ON (gman_character_equip.characterID = gman_character.characterID)
            ".$this->conditions."
			ORDER BY	".($this->sortField)." ".$this->sortOrder;
        }

		$statement = WCF::getDB()->prepareStatement($sql, $this->itemsPerPage, ($this->pageNo - 1) * $this->itemsPerPage);
		$statement->execute($this->conditions->getParameters());
		$charIDs = $statement->fetchAll(\PDO::FETCH_COLUMN);
		foreach($charIDs as $charID) {
            $this->chars[] = new WowCharacter($charID);
        }
    }
	/**
     * @inheritDoc
     */
	protected function readObjects() {

	}

	/**
     * @inheritDoc
     */
	public function assignVariables() {
		parent::assignVariables();

        $classesList = new WoWClassesList;
        $classesList->readObjects();
        $classes = $classesList->getObjects();

        $racesList = new WowRaceList;
        $racesList->getConditionBuilder()->add('sideID = ?', [$this->guild->side]);
        $racesList->readObjects();
        $races = $racesList->getObjects();

		WCF::getTPL()->assign([
            'guild' => $this->guild,
			'deletedChars' => $this->deletedChars,
			'chars' => $this->chars,
            'raceID' => $this->raceID,
		    'classID' =>   $this->classID,
		    'rankID' => $this->rankID,
            'minAVGILVL' =>  $this->minAVGILVL,
            'minLevel' =>   $this->minLevel,
            'ownerName' => $this->ownerID >0 ? $this->ownerObject->username : '',
            'races' => $races,
            'classes' => $classes,
            'charName' => $this->charName,
		]);
	}
}
