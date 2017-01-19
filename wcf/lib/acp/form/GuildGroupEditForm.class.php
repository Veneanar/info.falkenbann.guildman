<?php
namespace wcf\acp\form;
use wcf\data\guild\group\GuildGroup;
use wcf\data\guild\group\GuildGroupAction;
use wcf\form\AbstractForm;
use wcf\system\exception\IllegalLinkException;
use wcf\system\WCF;

/**
 * Gilden Gruppen bearbeiten
 * @author	    Veneanar Falkenbann
 * @copyright	2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	    GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	    info.falkenbann.guildman
 *
 */
class GuildGroupEditForm extends GuildGroupAddForm {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.gman.guildgroupedit';

	/**
	 * id of the edited ad
	 * @var	integer
	 */
	public $groupID = 0;

	/**
	 * edited ad object
     * @var	GuildGroup
	 */
	public $guildGroupObject = null;

	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();

		WCF::getTPL()->assign([
			'action' => 'edit',
			'adObject' => $this->guildGroupObject
		]);
	}

	/**
	 * @inheritDoc
	 */
	public function readData() {
		parent::readData();
		if (empty($_POST)) {
			$this->groupName            = $this->guildGroupObject->groupName;
            $this->groupTeaser          = $this->guildGroupObject->groupTeaser;
            $this->groupWcfID           = $this->guildGroupObject->groupWcfID;
            $this->showCalender         = $this->guildGroupObject->showCalender;
            $this->calendarCategoryID   = $this->guildGroupObject->calendarCategoryID;
            $this->calendarTitle        = $this->guildGroupObject->calendarTitle;
            $this->calendarText         = $this->guildGroupObject->calendarText;
            $this->calendarQuery        = $this->guildGroupObject->calendarQuery;
            $this->categoryList         = $this->guildGroupObject->categoryList;
            $this->gameRank             = $this->guildGroupObject->gameRank;
            $this->showRoaster          = $this->guildGroupObject->showRoaster;
            $this->articleID            = $this->guildGroupObject->articleID;
            $this->boardID              = $this->guildGroupObject->boardID;
            $this->imageID              = $this->guildGroupObject->imageID;
            $this->threadID             = $this->guildGroupObject->threadID;
            $this->isRaidgruop          = $this->guildGroupObject->isRaidgruop;
            $this->fetchWCL             = $this->guildGroupObject->fetchWCL;
            $this->wclQuery             = $this->guildGroupObject->wclQuery;
            $this->orderNo              = $this->guildGroupObject->orderNo;
        }
    }


	/**
	 * @inheritDoc
	 */
	public function readParameters() {
		parent::readParameters();

		if (isset($_REQUEST['id'])) $this->groupID = intval($_REQUEST['id']);
		$this->guildGroupObject = new GuildGroup($this->groupID);
		if (!$this->guildGroupObject->groupID) {
			throw new IllegalLinkException();
		}
	}

	/**
	 * @inheritDoc
	 */
	public function save() {
		AbstractForm::save();

		$this->objectAction = new GuildGroupAction([$this->guildGroupObject], 'update', [
			'data' =>  [
			    'groupName'         => $this->groupName,
                'groupTeaser'       => $this->groupTeaser,
                'groupWcfID'        => $this->groupWcfID,
                'showCalender'      => $this->showCalender,
                'calendarTitle'     => $this->calendarTitle,
                'calendarText'      => $this->calendarText,
                'calendarQuery'     => $this->calendarQuery,
                'calendarCategoryID'=> $this->calendarCategoryID,
                'gameRank'          => $this->gameRank,
                'showRoaster'       => $this->showRoaster,
                'articleID'         => $this->articleID,
                'boardID'           => $this->boardID,
                'imageID'           => $this->imageID[0],
                'threadID'          => $this->threadID,
                'isRaidgruop'       => $this->isRaidgruop,
                'fetchWCL'          => $this->fetchWCL,
                'wclQuery'          => $this->wclQuery,
                'orderNo'           => $this->orderNo,
                'lastUpdate'        => TIME_NOW
			]
		]);
		$this->objectAction->executeAction();

		$this->saved();

		WCF::getTPL()->assign('success', true);
	}
}
