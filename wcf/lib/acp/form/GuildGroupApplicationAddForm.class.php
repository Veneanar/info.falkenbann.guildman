<?php
namespace wcf\acp\form;
use wcf\form\AbstractForm;
use wcf\data\package\PackageCache;
use wcf\system\condition\ConditionHandler;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;
use wcf\util\StringUtil;
use wcf\util\ArrayUtil;
use wcf\data\guild\group\GuildGroup;
use wcf\data\guild\group\GuildGroupList;
use wcf\system\acl\ACLHandler;
use wcf\data\article\Article;
use wcf\data\article\ArticleList;
use wbb\data\board\Board;
use wbb\data\board\BoardList;
use wcf\system\cache\runtime\GuildRuntimeChache;
use wcf\data\guild\group\application\GuildGroupApplication;
use wcf\data\guild\group\application\GuildGroupApplicationAction;
use wcf\data\guild\group\application\GuildGroupApplicationEditor;
use wcf\system\language\I18nHandler;
use wcf\data\guild\group\application\field\ApplicationFieldList;
use wcf\data\guild\group\application\field\ViewableApplicationAction;



/**
 * Gruppennewerbung hinzufügen
 * @author	    Veneanar Falkenbann
 * @copyright	2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	    GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	    info.falkenbann.guildman
 *
 */

class GuildGroupApplicationAddForm extends AbstractForm {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.gman.appadd';

	/**
	 * @inheritDoc
	 */
	public $neededPermissions = ['admin.gman.canAddGroups'];

	/**
	 * @inheritDoc
	 */
	public $neededModules = [];

	/**
     * name of the template for the called page
     * @var	string
     */
	public $templateName = 'groupApplicationAdd';

	/**
     * object type id
     * @var	integer
     */
	public $objectTypeID = 0;

	/**
     * Group ID
     * @var	integer
     */
	public $appGroupID = 0;

	/**
	 * Group Object
	 * @var	GuildGroup
	 */
	public $groupObject = null;

	/**
     * app Title
     * @var	string
     */
	public $title = '';

	/**
	 * description
	 * @var	string
	 */
	public $desciption = '';

	/**
     * is active?
     * @var	integer
     */
	public $isActive = 0;

    /**
     * Board ID
     * @var	integer
     */
	public $appForumID = 0;

     /**
     * Article ID
     * @var	integer
     */
	public $appArticleID = 0;

    /**
     * Guild
     * @var \wcf\data\guild\Guild
     */
    public $guild = null;

    /**
     * Application Object
     * @var GuildGroupApplication
     */
    public $applicationObject = null;

    /**
     * List of Fields
     * @var	\wcf\data\guild\group\application\field\ViewableApplicationFieldList[]
     */
	public $applicationFieldList = [];

	/**
     * List of Actions
     * @var	\wcf\data\guild\group\application\action\ViewableApplicationActionList[]
     */
	public $applicationActionList = [];

    public $isCommentable = 0;

    public $hasPoll = 0;

	/**
	 * @inheritDoc
	 */
	public function readParameters() {
		parent::readParameters();
		$this->guild = GuildRuntimeChache::getInstance()->getCachedObject();
        I18nHandler::getInstance()->register('description');
		I18nHandler::getInstance()->register('title');

        $this->objectTypeID = ACLHandler::getInstance()->getObjectTypeID('info.falkenbann.gman.userapplication');

    }

	/**
	 * @inheritDoc
	 */
	public function readFormParameters() {
		parent::readFormParameters();
		// read i18n values
		I18nHandler::getInstance()->readValues();

		// handle i18n plain input
		if (I18nHandler::getInstance()->isPlainValue('description')) $this->description = I18nHandler::getInstance()->getValue('description');
		if (I18nHandler::getInstance()->isPlainValue('title')) $this->title = I18nHandler::getInstance()->getValue('title');

        if (isset($_POST['appArticleID']))      $this->appArticleID         = intval($_POST['appArticleID']);
        if (isset($_POST['appGroupID']))        $this->appGroupID           = intval($_POST['appGroupID']);
        if (isset($_POST['appForumID']))        $this->appForumID           = intval($_POST['appForumID']);
        if (isset($_POST['isCommentable']))     $this->isCommentable        = intval($_POST['isCommentable']);
        if (isset($_POST['hasPoll']))           $this->hasPoll              = intval($_POST['hasPoll']);

    }

	/**
     * @inheritDoc
     */
	public function validate() {
		parent::validate();

		// validate title
		if (!I18nHandler::getInstance()->validateValue('title')) {
			if (I18nHandler::getInstance()->isPlainValue('title')) {
				throw new UserInputException('title');
			}
			else {
				throw new UserInputException('title', 'multilingual');
			}
		}

		// validate description
		if (!I18nHandler::getInstance()->validateValue('description', false, true)) {
			throw new UserInputException('description');
		}

		if ($this->appGroupID == 0) {
            throw new UserInputException('appGroupID', 'empty');
        }
        else {
            $this->groupObject = new GuildGroup($this->appGroupID);
            if ($this->groupObject->getObjectID()==0) throw new UserInputException('wcfGroupID', 'notFound');
		}

        if ($this->articleID > 0) {
            $artcile = new Article($this->articleID);
            if ($artcile === null) {
                throw new UserInputException('articleID', 'notFound');
            }
        }

        if ($this->appForumID == 0) {
            throw new UserInputException('appGroupID', 'empty');
        }
        else {
            $board = new Board($this->appForumID);
            if ($board===null) {
                throw new UserInputException('boardID', 'notFound');
            }
        }
    }

	/**
	 * @inheritDoc
	 */
	public function save() {
		parent::save();
		$this->objectAction = new GuildGroupApplicationAction([], 'create', [
			'data' =>  [
			    'appTitle'          => $this->title,
                'appDescription'    => $this->description,
                'appArticleID'      => $this->appArticleID,
                'appGroupID'        => $this->appGroupID,
                'appForumID'        => $this->appForumID,
                'isActive'          => $this->isActive,
			]
		]);
		$this->applicationObject = $this->objectAction->executeAction()['returnValues'];
        $this->saveI18nValue($this->applicationObject, 'description');
		$this->saveI18nValue($this->applicationObject, 'title');

        ACLHandler::getInstance()->save($this->applicationObject->appID, $this->objectTypeID);

        $this->saved();

// reset values
        I18nHandler::getInstance()->reset();
        ACLHandler::getInstance()->disableAssignVariables();
        $this->title = '';
        $this->description = '';
        $this->appGroupID = 0;
        $this->appArticleID = 0;
        $this->appForumID = 0;
        $this->isActive = 0;
		WCF::getTPL()->assign('success', true);
		WCF::getTPL()->assign('application', $this->application);
	}
	/**
     * Saves i18n values.
     *
     * @param	Board		$board
     * @param	string		$columnName
     */
	public function saveI18nValue(GuildGroupApplication $application, $columnName) {
		if (!I18nHandler::getInstance()->isPlainValue($columnName)) {
			I18nHandler::getInstance()->save($columnName, 'wcf.gman.apllication'.$application->appID.($columnName == 'description' ? '.description' : ''), 'wbb.board', PackageCache::getInstance()->getPackageID('info.falkenbann.gman'));

			// update description
			$boardEditor = new GuildGroupApplicationEditor($application);
			$boardEditor->update([
				$columnName => 'wcf.gman.apllication'.$application->appID.($columnName == 'description' ? '.description' : '')
			]);
		}
	}

	/**
     * @inheritDoc
     */
	public function assignVariables() {
		parent::assignVariables();

        $articleList = new ArticleList;
        $articleList->readObjects();

        $boardList = new BoardList;
        $boardList->readObjects();

        $groupList = new GuildGroupList;
        $groupList->readObjects();

        I18nHandler::getInstance()->assignVariables();

        ACLHandler::getInstance()->assignVariables($this->objectTypeID);

        $fieldList = new ApplicationFieldList;
        $fieldList->readObjects();

		WCF::getTPL()->assign([
            'guild'                 => $this->guild,
            'articleList'           => $articleList->getObjects(),
            'boardList'             => $boardList->getObjects(),
            'groupList'             => $groupList->getObjects(),
            'appGroupID'            => $this->appGroupID,
            'appForumID'            => $this->appForumID,
            'appArticleID'          => $this->appArticleID,
            'isActive'              => $this->isActive,
            'applicationObject'     => $this->applicationObject,
            'title'                 => '',
            'description'           => '',
            'action'                => 'add',
            'objectTypeID'          => $this->objectTypeID,
            'avaibleFieldList'      => $fieldList->getObjects(),
            'avaibleActionList'     => [],
            'applicationFieldList'  => $this->applicationFieldList,
            'applicationActionList' => $this->applicationActionList,
            'hasPoll'               => $this->hasPoll,
            'isCommentable'         => $this->isCommentable,
		]);
        //echo "assign Vars: <pre>"; var_dump($this->wcfGroupID); echo "</pre>"; die();
	}

}
