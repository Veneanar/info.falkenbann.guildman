<?php
namespace wcf\acp\form;
use wcf\form\AbstractForm;
use wcf\system\exception\IllegalLinkException;
use wcf\system\WCF;
use wcf\system\acl\ACLHandler;
use wcf\data\package\PackageCache;
use wcf\system\language\I18nHandler;
use wcf\system\cache\runtime\GuildRuntimeChache;
use wcf\data\guild\group\application\GuildGroupApplication;
use wcf\data\guild\group\application\GuildGroupApplicationAction;
use wcf\data\guild\group\application\GuildGroupApplicationEditor;
use wcf\data\guild\group\application\field\ApplicationFieldList;
use wcf\data\guild\group\application\field\ViewableApplicationFieldList;
use wcf\data\guild\group\application\action\ApplicationActionList;
use wcf\data\guild\group\application\action\ViewableApplicationActionList;
/**
 * Gilden Gruppen bearbeiten
 * @author	    Veneanar Falkenbann
 * @copyright	2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	    GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	    info.falkenbann.guildman
 *
 */
class GuildGroupApplicationEditForm extends GuildGroupApplicationAddForm {
    /**
     *  Form action
     * @var string
     */
    public $action = 'edit';


	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();

    }

	/**
	 * @inheritDoc
	 */
	public function readData() {
		parent::readData();

        I18nHandler::getInstance()->setOptions('description', PackageCache::getInstance()->getPackageID('info.falkenbann.gman'), $this->board->description, 'wcf.gman.apllication\d+.description');
        I18nHandler::getInstance()->setOptions('title', PackageCache::getInstance()->getPackageID('info.falkenbann.gman'), $this->board->title, 'wcf.gman.apllication\d+');


		if (empty($_POST)) {
            $this->appGroupID = $this->applicationObject->appGroupID;
            $this->appForumID = $this->applicationObject->appForumID;
            $this->appArticleID = $this->applicationObject->appArticleID;
            $this->isActive = $this->applicationObject->isActive;
            $this->title = $this->applicationObject->appTitle;
            $this->desciption = $this->applicationObject->appDescription;
            $this->hasPoll = $this->applicationObject->hasPoll;
            $this->isCommentable = $this->applicationObject->isCommentable;
            $this->pollDescription = $this->applicationObject->pollDescription;
            $this->pollTitle = $this->applicationObject->pollTitle;
            $this->requireUser = $this->applicationObject->requireUser;
        }

        $this->applicationFieldList = ViewableApplicationFieldList::getApplicationFields($this->applicationObject->appID);
        $this->applicationActionList = ViewableApplicationActionList::getApplicationFields($this->applicationObject->appID);

    }


	/**
	 * @inheritDoc
	 */
	public function readParameters() {
		parent::readParameters();

		if (isset($_REQUEST['id'])) $this->applicationID = intval($_REQUEST['id']);
		$this->applicationObject = new GuildGroupApplication($this->applicationID);
		if (!$this->applicationObject->appID) {
			throw new IllegalLinkException();
		}
	}


	/**
     * Updates i18n values.
     *
     * @param	GuildGroupApplication		$application
     * @param	string		$columnName
     */
	public function updateI18nValue(GuildGroupApplication $application, $columnName) {
        $prefix = 'wcf.gman.application';
        $suffix = (strpos($columnName, 'poll') !== false) ? '.poll' : '';
        $suffix .= (strpos($columnName, 'Description') !== false) ? '.description' : '';
        if (I18nHandler::getInstance()->isPlainValue($columnName)) {
            I18nHandler::getInstance()->remove($prefix.$columnName.$suffix);
            return I18nHandler::getInstance()->getValue($columnName);
        }
        else {
            I18nHandler::getInstance()->save($columnName, 'wcf.gman.apllication'.$application->appID.$suffix, 'wcf.gman', PackageCache::getInstance()->getPackageID('info.falkenbann.gman'));
            return $prefix.$columnName.$suffix;
        }
    }


	/**
	 * @inheritDoc
	 */
	public function save() {
		AbstractForm::save();

        // update I18n values
        $this->description = $this->updateI18nValue($this->applicationObject, 'description');
		$this->title = $this->updateI18nValue($this->applicationObject, 'title');
        $this->pollDescription = $this->updateI18nValue($this->applicationObject, 'pollDescription');
		$this->pollTitle = $this->updateI18nValue($this->applicationObject, 'pollTitle');

		$this->objectAction = new GuildGroupApplicationAction([$this->applicationObject], 'update', [
			'data' =>  [
			    'appTitle'          => $this->title,
                'appDescription'    => $this->description,
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
        $this->objectAction->executeAction();

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
        
        ACLHandler::getInstance()->save($this->applicationObject->appID, $this->objectTypeID);

        $this->saved();
		WCF::getTPL()->assign('success', true);
	}
}
