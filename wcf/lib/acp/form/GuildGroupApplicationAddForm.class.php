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
use wcf\data\guild\group\application\field\ViewableApplicationFieldList;
use wcf\data\guild\group\application\action\ApplicationActionList;
use wcf\data\guild\group\application\action\ViewableApplicationActionList;
use wcf\data\user\group\UserGroupList;




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
     * @var integer
     */
    public $applicationID = 0;

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
     * Formsubmit Fields
     * @var array[]
     */
    public $fieldList = [];

	/**
     * List of Actions
     * @var	\wcf\data\guild\group\application\action\ViewableApplicationActionList[]
     */
	public $applicationActionList = [];

    /**
     * Formsubmit Actions
     * @var array[];
     */
    public $actionList = [];

    /**
     * poll desc.
     * @var string
     */
    public $pollDescription = '';

    /**
     * poll title
     * @var string
     */
    public $pollTitle = '';

    /**
     * is app commentable
     * @var boolean
     */
    public $isCommentable = false;

    /**
     * is app active
     * @var boolean
     */
    public $isActive = false;

    /**
     * app has poll?
     * @var boolean
     */
    public $hasPoll = false;

    /**
     *  Form action
     * @var string
     */
    public $action = 'add';

    /**
     * app requieres user account?
     * @var boolean
     */
    public $requireUser = false;

	/**
	 * @inheritDoc
	 */
	public function readParameters() {
		parent::readParameters();
		$this->guild = GuildRuntimeChache::getInstance()->getCachedObject();
        I18nHandler::getInstance()->register('description');
		I18nHandler::getInstance()->register('title');
        I18nHandler::getInstance()->register('pollDescription');
		I18nHandler::getInstance()->register('pollTitle');
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
        $this->isActive = isset($_POST['isActive']) ? true: false;
        $this->isCommentable = isset($_POST['isCommentable']) ? true: false;
        $this->requireUser = isset($_POST['requireUser']) ? true: false;


        if (I18nHandler::getInstance()->isPlainValue('pollDescription')) $this->pollDescription = I18nHandler::getInstance()->getValue('pollDescription');
		if (I18nHandler::getInstance()->isPlainValue('pollTitle')) $this->pollTitle = I18nHandler::getInstance()->getValue('pollTitle');
        $this->hasPoll = isset($_POST['hasPoll']) ? true : false;

        // Fields
        if (isset($_POST['fieldID'])) {
            $fieldRange = array_keys($_POST['fieldID']);
            foreach ($fieldRange as $key) {
                $this->fieldList[] = [
                    'fieldID'           => $_POST['fieldID'][$key],
                    'fieldPermission'   => $_POST['fieldPermission'][$key],
                    'fieldOrder'        => $_POST['fieldOrder'][$key],
                    'fieldRequierd'     => isset($_POST['fieldRequierd'][$key]) ? 1:0,
                    ];
            }
        }

        // Actions
        if (isset($_POST['actionID'])) {
            $actionRange = array_keys($_POST['actionID']);
            foreach ($actionRange as $key) {
                $this->actionList[] = [
                    'actionID'        => $_POST['actionID'][$key],
                    'actionTrigger'   => $_POST['actionTrigger'][$key],
                    'actionVariable'  => isset($_POST['actionVariable'][$key]) ? $_POST['actionVariable'][$key] : 0,
                    ];
            }
        }

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

        if ($this->hasPoll) {
            if (!I18nHandler::getInstance()->validateValue('pollTitle')) {
                if (I18nHandler::getInstance()->isPlainValue('pollTitle')) {
                    throw new UserInputException('pollTitle');
                }
                else {
                    throw new UserInputException('pollTitle', 'multilingual');
                }
            }

            // validate description
            if (!I18nHandler::getInstance()->validateValue('pollDescription', false, false)) {
                throw new UserInputException('pollDescription');
            }
        }

		if ($this->appGroupID == 0) {
            throw new UserInputException('appGroupID', 'empty');
        }
        else {
            $this->groupObject = new GuildGroup($this->appGroupID);
            if ($this->groupObject->getObjectID()==0) throw new UserInputException('wcfGroupID', 'notFound');
		}

        if ($this->appArticleID > 0) {
            $artcile = new Article($this->appArticleID);
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
			    'title'          => $this->title,
                'description'    => $this->description,
                'appArticleID'      => $this->appArticleID,
                'appGroupID'        => $this->appGroupID,
                'appForumID'        => $this->appForumID,
                'isActive'          => intval($this->isActive),
                'hasPoll'           => intval($this->hasPoll),
                'pollTitle'         => $this->pollTitle,
                'pollDescription'   => $this->pollDescription,
                'isCommentable'     => intval($this->isCommentable),
                'requireUser'       => intval($this->requireUser),
			]
		]);
		$this->applicationObject = $this->objectAction->executeAction()['returnValues'];

        // save fields
        $applicationAction = new GuildGroupApplicationAction([$this->applicationObject], 'upsertFields', [
                    'fields' => $this->fieldList
                    ]);
        $applicationAction->executeAction();

        // save actions
        $applicationAction = new GuildGroupApplicationAction([$this->applicationObject], 'upsertActions', [
                    'actions' => $this->actionList
                    ]);
        $applicationAction->executeAction();

        // save I18n values
        $this->saveI18nValue($this->applicationObject, 'description');
		$this->saveI18nValue($this->applicationObject, 'title');
        $this->saveI18nValue($this->applicationObject, 'pollDescription');
		$this->saveI18nValue($this->applicationObject, 'pollTitle');

        ACLHandler::getInstance()->save($this->applicationObject->appID, $this->objectTypeID);

        $this->saved();

        // reset values
        I18nHandler::getInstance()->reset();
        ACLHandler::getInstance()->disableAssignVariables();
        $this->title = $this->pollDescription = $this->pollTitle = $this->description = '';
        $this->appForumID = $this->appArticleID = $this->appGroupID = 0;
        $this->isCommentable = $this->hasPoll = $this->requireUser = $this->isActive = false;

		WCF::getTPL()->assign('success', true);
		WCF::getTPL()->assign('applicationObject', $this->applicationObject);
	}
	/**
     * Saves i18n values.
     *
     * @param	Board		$board
     * @param	string		$columnName
     */
	public function saveI18nValue(GuildGroupApplication $application, $columnName) {
		if (!I18nHandler::getInstance()->isPlainValue($columnName)) {
            $suffix = (strpos($columnName, 'poll') !== false) ? '.poll' : '';
            $suffix .= (strpos($columnName, 'Description') !== false) ? '.description' : '';
			I18nHandler::getInstance()->save($columnName, 'wcf.gman.apllication'.$application->appID.$suffix, 'wcf.gman', PackageCache::getInstance()->getPackageID('info.falkenbann.gman'));
			$applicationEditor = new GuildGroupApplicationEditor($application);
			$applicationEditor->update([
				$columnName => 'wcf.gman.apllication'.$application->appID.$suffix
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

        $avaibleFieldList = new ApplicationFieldList;
        $avaibleFieldList->readObjects();

        $avaibleActionList = new ApplicationActionList;
        $avaibleActionList->readObjects();

		WCF::getTPL()->assign([
            'applicationID'         => $this->applicationID,
            'guild'                 => $this->guild,
            'articleList'           => $articleList->getObjects(),
            'boardList'             => $boardList->getObjects(),
            'groupList'             => $groupList->getObjects(),
            'appGroupID'            => $this->appGroupID,
            'appForumID'            => $this->appForumID,
            'appArticleID'          => $this->appArticleID,
            'isActive'              => $this->isActive,
            'applicationObject'     => $this->applicationObject,
            'title'                 => $this->title,
            'description'           => $this->desciption,
            'action'                => $this->action,
            'objectTypeID'          => $this->objectTypeID,
            'avaibleFieldList'      => $avaibleFieldList->getObjects(),
            'avaibleActionList'     => $avaibleActionList->getObjects(),
            'applicationFieldList'  => $this->applicationFieldList,
            'applicationActionList' => $this->applicationActionList,
            'hasPoll'               => $this->hasPoll,
            'isCommentable'         => $this->isCommentable,
            'pollDescription'       => $this->pollDescription,
            'pollTitle'             => $this->pollTitle,
            'requireUser'           => $this->requireUser,
		]);
        //echo "assign Vars: <pre>"; var_dump($this->wcfGroupID); echo "</pre>"; die();
	}

}
