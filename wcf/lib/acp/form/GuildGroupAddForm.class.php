<?php
namespace wcf\acp\form;
use calendar\data\category\CalendarCategory;
use calendar\data\category\CalendarCategoryNodeTree;
use wcf\form\AbstractForm;
use wcf\system\condition\ConditionHandler;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;
use wcf\util\StringUtil;
use wcf\util\ArrayUtil;
use wcf\data\guild\group\GuildGroup;
use wcf\data\guild\group\GuildGroupAction;
use wcf\data\user\group\UserGroup;
use wcf\data\article\Article;
use wcf\data\article\ArticleList;
use wcf\data\media\ViewableMedia;
use wcf\data\media\Media;
use wcf\data\media\ViewableMediaList;
use wbb\data\thread\Thread;
use wbb\data\board\Board;
use wbb\data\board\BoardList;
use wcf\data\user\group\UserGroupList;
use wcf\data\guild\Guild;
use wcf\data\wow\character\WowCharacterAction;
use wcf\data\wow\character\WowCharacterList;
use wcf\data\wow\character\WowCharacter;
use wcf\system\cache\runtime\GuildRuntimeChache;

/**
 * Gruppen hinzufügen
 * @author	    Veneanar Falkenbann
 * @copyright	2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	    GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	    info.falkenbann.guildman
 *
 */

class GuildGroupAddForm extends AbstractForm {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.gman.groupadd';

	/**
	 * @inheritDoc
	 */
	public $neededPermissions = ['admin.gman.canAddGroups'];

	/**
	 * @inheritDoc
	 */
	public $neededModules = [];

	/**
	 * Name of the Group
	 * @var	string
	 */
	public $groupName = '';

	/**
	 * Group teaser
	 * @var	string
	 */
	public $groupTeaser = '';


	/**
	 * group ID assigned to WCF group
	 * @var	integer
	 */
	public $wcfGroupID = 0;

	/**
     * group assigned to WCF group
     * @var	UserGroup;
     */
	public $groupWcf = null;

	/**
	 * show in calendar?
	 * @var	boolean
	 */
	public $showCalender = false;

	/**
	 * Calendar Title
	 * @var	string
	 */
	public $calendarTitle = '';

	/**
     * Calendar Title
     * @var	string
     */
	public $calendarText = '';

	/**
	 * fetch calendar from wow
	 * @var	boolean
	 */
	public $fetchCalendar = false;

	/**
     * query for calendar
     * @var	string
     */
	public $calendarQuery = '';

	/**
	 * Assign to ingame Rank
	 * @var	integer
	 */
	public $gameRank = 11;

	/**
     * Show in roaster
     * @var	boolean
     */
	public $showRoaster = false;

	/**
     * Article ID
     * @var	integer
     */
	public $articleID = 0;

	/**
     * Thread ID
     * @var	integer
     */
	public $threadID = 0;

    /**
     * Board ID
     * @var	integer
     */
	public $boardID = 0;

    /**
     * Logo Media ID
     * @var	integer
     */
    public $iconID = 0;

    /**
     * Logo Media ID
     * @var	integer
     */
    public $imageID = 0;

	/**
     * is raidgroup
     * @var	boolean
     */
	public $isRaidgruop = false;
    /**
     * fetch data from Warcraftlogs
     * @var	boolean
     */
	public $fetchWCL = false;

	/**
     * WCF Query
     * @var	string
     */
	public $wclQuery = '';

	/**
     * Order
     * @var	integer
     */
	public $orderNo = 0;

	/**
     * category list
     * @var	\RecursiveIteratorIterator
     */
	public $categoryList;


      /**
     * category id for calendar
     * @var	integer
     */
	public $calendarCategoryID = 0;

	/**
     * array of leader given by the user
     * @var	string[]
     */
	protected $leader = [];

	/**
     * userlist containing the group leaders
     * @var	WowCharacterList
     */
	protected $leaderList;


	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();



        $articleList = new ArticleList;
        $articleList->readObjects();
        $articles = $articleList->getObjects();

        $boardList = new BoardList;
        $boardList->readObjects();
        $boards = $boardList->getObjects();

        $wcfGroupList = new UserGroupList();
        $wcfGroupList->getConditionBuilder()->add("groupType > 3");
        $wcfGroupList->getConditionBuilder()->add("groupID != 4");
        $wcfGroupList->getConditionBuilder()->add("groupID != 5");
        $wcfGroupList->readObjects();
        $wcfGroups = $wcfGroupList->getObjects();

        $excludedCategoryIDs = array_diff(CalendarCategory::getAccessibleCategoryIDs(), CalendarCategory::getAccessibleCategoryIDs(['canUseCategory']));
		$categoryTree = new CalendarCategoryNodeTree('com.woltlab.calendar.category', 0, false, $excludedCategoryIDs);
		$categoryList = $categoryTree->getIterator();

        // echo "<pre>"; var_dump($wcfGroups); echo "</pre>"; die;
        $guild = GuildRuntimeChache::getInstance()->getCachedObject();
        $ranks = $guild->getRanks();
		WCF::getTPL()->assign([
			'action'            => 'add',
			'groupName'         => $this->groupName,
            'groupTeaser'       => $this->groupTeaser,
            'wcfGroupID'        => $this->wcfGroupID,
            'wcfGroups'         => $wcfGroups,
            'showCalender'      => $this->showCalender,
            'calendarCategoryID'=> $this->calendarCategoryID,
            'calendarTitle'     => $this->calendarTitle,
            'calendarText'      => $this->calendarText,
            'calendarQuery'     => $this->calendarQuery,
            'categoryList'      => $categoryList,
            'gameRank'          => $this->gameRank,
            'rankList'          => $ranks,
            'showRoaster'       => $this->showRoaster,
            'articleID'         => $this->articleID,
            'articleList'       => $articles,
            'boardID'           => $this->boardID,
            'boardList'         => $boards,
            'imageID'           => $this->imageID,
            'iconID'            => $this->iconID,
            'image'             => $this->imageID ? new ViewableMedia(new Media($this->imageID)) : null,
            'icon'              => $this->iconID ? new ViewableMedia(new Media($this->iconID)) : null,
            'threadID'          => $this->threadID,
            'isRaidgruop'       => $this->isRaidgruop,
            'fetchWCL'          => $this->fetchWCL,
            'wclQuery'          => $this->wclQuery,
            'orderNo'           => $this->orderNo,
            'leader' => implode(', ', $this->leader)
		]);
        //echo "assign Vars: <pre>"; var_dump($this->wcfGroupID); echo "</pre>"; die();
	}

	/**
	 * @inheritDoc
	 */
	public function readData() {
		parent::readData();

		// get categories
		$excludedCategoryIDs = array_diff(CalendarCategory::getAccessibleCategoryIDs(), CalendarCategory::getAccessibleCategoryIDs(['canUseCategory']));
		$categoryTree = new CalendarCategoryNodeTree('com.woltlab.calendar.category', 0, false, $excludedCategoryIDs);
		$this->categoryList = $categoryTree->getIterator();

	}

	/**
	 * @inheritDoc
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		if (isset($_POST['groupName']))     $this->groupName        = StringUtil::trim($_POST['groupName']);
        if (isset($_POST['groupTeaser']))   $this->groupTeaser      = StringUtil::trim($_POST['groupTeaser']);
        if (isset($_POST['wcfGroupID']))    $this->wcfGroupID       = intval($_POST['wcfGroupID']);
        if (WCF::getSession()->getPermission('admin.content.cms.canUseMedia')) {
            if (isset($_POST['imageID']))   $this->imageID  = intval($_POST['imageID']);
            if (isset($_POST['iconID']))    $this->iconID   = intval($_POST['iconID']);
        }
        if (isset($_POST['showCalender']))  {
            $this->showCalender     = true;
            if (isset($_POST['calendarTitle'])) $this->calendarTitle    = StringUtil::trim($_POST['calendarTitle']);
            if (isset($_POST['calendarText']))  $this->calendarText     = StringUtil::trim($_POST['calendarText']);
            if (isset($_POST['calendarQuery'])) $this->calendarQuery    = StringUtil::trim($_POST['calendarQuery']);
            if (isset($_POST['calendarCategoryID']))   $this->calendarCategoryID       = $_POST['calendarCategoryID'];
        }
        if (isset($_POST['gameRank']))      $this->gameRank         = intval($_POST['gameRank']);
        if (isset($_POST['showRoaster']))   $this->showRoaster      = true;
        if (isset($_POST['articleID']))     $this->articleID        = intval($_POST['articleID']);
        if (isset($_POST['boardID']))       $this->boardID          = intval($_POST['boardID']);
        if (isset($_POST['threadID']))      $this->threadID         = intval($_POST['threadID']);
        if (isset($_POST['isRaidgruop']))   $this->isRaidgruop      = true;
        if (isset($_POST['fetchWCL'])) {
            $this->fetchWCL         = true;
            if (isset($_POST['wclQuery']))      $this->wclQuery         = StringUtil::trim($_POST['wclQuery']);
        }
        if (isset($_POST['orderNo']))       $this->orderNo          = intval($_POST['orderNo']);
		if (isset($_POST['leader'])) {
			$leaders = ArrayUtil::trim(explode(',', $_POST['leader']));

			// remove duplicates
			foreach ($leaders as $leader) {
				$this->leader[mb_strtolower($leader)] = $leader;
			}
		}
	}


    /**
     * Reads the gopup icons.
     */


	/**
     * @inheritDoc
     */
	public function validate() {
		parent::validate();

		if (empty($this->groupName)) {
			throw new UserInputException('groupName');
		}
        if (strlen($this->groupName) > 50) {
            throw new UserInputException('groupName', 'toolong');
        }
        if (strlen($this->groupName) < 3) {
            throw new UserInputException('groupName', 'tooshort');
        }
        if (strlen($this->groupTeaser) > 250) {
            throw new UserInputException('groupTeaser', 'toolong');
        }
		if ($this->wcfGroupID > 0) {
            $this->groupWcf = new UserGroup($this->wcfGroupID);
            if ($this->groupWcf->getObjectID()==0) throw new UserInputException('wcfGroupID');
		}

        if ($this->showCalender) {
            if ($this->calendarCategoryID == 0) {
                throw new UserInputException('calendarCategoryID');
            }
            $category = CalendarCategory::getCategory($this->calendarCategoryID);
            if ($category === null) {
                throw new UserInputException('calendarCategoryID', 'invalid');
            }
            if (!$category->isAccessible() || !$category->getPermission('canUseCategory')) {
                throw new UserInputException('calendarCategoryID', 'invalid');
            }
        }

        if ($this->articleID > 0) {
            $artcile = new Article($this->articleID);
            if ($artcile === null) {
                throw new UserInputException('articleID', 'notFound');
            }
        }

        if ($this->threadID > 0) {
            $page = new Thread($this->threadID);
            if ($page===null) {
                throw new UserInputException('threadID', 'notFound');
            }
        }

        if ($this->boardID > 0) {
            $board = new Board($this->boardID);
            if ($board===null) {
                throw new UserInputException('boardID', 'notFound');
            }
        }
        if ($this->imageID > 0) {
            $board = new Media($this->imageID);
            if ($board===null) {
                throw new UserInputException('imageID', 'notFound');
            }
        }
        if ($this->iconID > 0) {
            $board = new Media($this->iconID);
            if ($board===null) {
                throw new UserInputException('iconID', 'notFound');
            }
        }

		try {
			if (!empty($this->leader)) {
				$this->leaderList = new WowCharacterList();
				$this->leaderList->getConditionBuilder()->add('charname IN (?)', [$this->leader]);
				$this->leaderList->readObjects();

				$tmp = [];
				foreach ($this->leaderList as $wowChar) {
					$tmp[mb_strtolower($wowChar->charname)] = $wowChar->charname;
				}

				$difference = array_diff_key($this->leader, $tmp);
				if (!empty($difference)) {
					WCF::getTPL()->assign([
						'unknownLeader' => $difference
					]);
					throw new UserInputException('leader', 'notFound');
				}

				$this->leader = $tmp;
			}
		}
		catch (UserInputException $e) {
			throw new UserInputException('leader', 'notFound');
		}

	}

	/**
	 * @inheritDoc
	 */
	public function save() {
		parent::save();
		$this->objectAction = new GuildGroupAction([], 'create', [
            'changeWCFGroup'    => $this->wcfGroupID > 0 ? true : false,
            'changeRank'        => $this->gameRank < 11 ? true : false,
			'data' =>  [
			    'groupName'         => $this->groupName,
                'groupTeaser'       => $this->groupTeaser,
                'wcfGroupID'        => $this->wcfGroupID,
                'showCalender'      => intval($this->showCalender),
                'calendarTitle'     => $this->calendarTitle,
                'calendarText'      => $this->calendarText,
                'calendarQuery'     => $this->calendarQuery,
                'calendarCategoryID'=> $this->calendarCategoryID,
                'gameRank'          => $this->gameRank,
                'showRoaster'       => intval($this->showRoaster),
                'articleID'         => $this->articleID > 0 ? $this->articleID : null,
                'boardID'           => $this->boardID > 0 ? $this->boardID : null ,
                'imageID'           => $this->imageID > 0 ? $this->imageID: null,
                'iconID'           => $this->iconID > 0 ? $this->iconID: null,
                'threadID'          => $this->threadID > 0 ? $this->threadID : null,
                'isRaidgruop'       => intval($this->isRaidgruop),
                'fetchWCL'          => intval($this->fetchWCL),
                'wclQuery'          => $this->wclQuery,
                'orderNo'           => $this->orderNo,
                'lastUpdate'        => TIME_NOW
			]
		]);
		$group = $this->objectAction->executeAction()['returnValues'];
        if ($this->leaderList !== null) {
			// save group leaders
			$sql = "INSERT INTO wcf".WCF_N."_gman_group_leader (groupID, leaderID) VALUES (?, ?)";
			$statement = WCF::getDB()->prepareStatement($sql);
			foreach ($this->leaderList as $wowChar) {
				$statement->execute([$group->groupID, $wowChar->characterID]);
                $characterAction = new WowCharacterAction([$wowChar], 'AddToGroup', [
                        'groupID' => $group->groupID
                        ]);
               $characterAction->executeAction();
			}
		}
		$this->saved();

		// reset values
        $this->leader = [];
		$this->groupName = '';
        $this->groupTeaser = '';
        $this->wcfGroupID = 0;
        $this->showCalender = false;
        $this->calendarTitle = '';
        $this->calendarText = '';
        $this->calendarQuerry = '';
        $this->calendarCategoryID = 0;
        $this->gameRank = 11;
        $this->showRoaster = false;
        $this->articleID = 0;
        $this->boardID = 0;
        $this->imageID = 0;
        $this->iconID = 0;
        $this->threadID = 0;
        $this->isRaidgruop = false;
        $this->fetchWCL = false;
        $this->wclQuery = '';
        $this->orderNo = 0;
		WCF::getTPL()->assign('success', true);
	}
}
