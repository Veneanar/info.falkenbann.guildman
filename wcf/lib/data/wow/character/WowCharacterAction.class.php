<?php
namespace wcf\data\wow\character;
use wcf\data\ISearchAction;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\exception\UserInputException;
use wcf\system\background\BackgroundQueueHandler;
use wcf\system\background\job\WowCharacterUpdateJob;
use wcf\system\WCF;
use wcf\system\wow\bnetAPI;

/**
 *
 * Executes WoW Charackter-related actions.
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */

class WowCharacterAction extends AbstractDatabaseObjectAction implements ISearchAction {
	/**
	 * {@inheritDoc}
	 */
	public static $baseClass = WowCharacterEditor::class;
	/**
	 * {@inheritDoc}
	 */
	protected $permissionsUpdate = array('mod.gman.canUpdateChar', 'user.gman.canUpdateOwnChar');
	/**
	 * {@inheritDoc}
	 */
	protected $permissionsCreate = array();
	/**
	 * {@inheritDoc}
	 */
	protected $permissionsDelete = array();
	/**
	 * {@inheritDoc}
	 */
	protected $requireACP = array();
	/**
	 * {@inheritDoc}
	 */
	protected $allowGuestAccess = array();


    /**
     * @inheritDoc
     */
	public function validateGetSearchResultList() {
		// $this->readBoolean('includeUserGroups', false, 'data');
		$this->readString('searchString', false, 'data');

		if (isset($this->parameters['data']['excludedSearchValues']) && !is_array($this->parameters['data']['excludedSearchValues'])) {
			throw new UserInputException('excludedSearchValues');
		}
	}

	/**
     * @inheritDoc
     */
	public function getSearchResultList() {
		$searchString = $this->parameters['data']['searchString'];
		$excludedSearchValues = [];
		if (isset($this->parameters['data']['excludedSearchValues'])) {
			$excludedSearchValues = $this->parameters['data']['excludedSearchValues'];
		}
		$list = [];
		$wowCharList = new WowCharacterList();
		$wowCharList->getConditionBuilder()->add("charID LIKE ?", [$searchString.'%']);
		if (!empty($excludedSearchValues)) {
			$wowCharList->getConditionBuilder()->add("charID NOT IN (?)", [$excludedSearchValues]);
		}
		$wowCharList->sqlLimit = 10;
		$wowCharList->readObjects();
        /**
         * @var WowCharacter $wowChar
         */
		foreach ($wowCharList as $wowChar) {
			$list[] = [
				'icon' => $wowChar->getAvatar("avatar")->getImageTag(16),
				'label' => $wowChar->charID,
				'objectID' => $wowChar->charID,
				'type' => 'user'
			];
		}
		return $list;
	}

    public function validateUpdateData() {
        parent::validateUpdate();
        /**
         * @var WowCharacter $wowChar
         */
        foreach($this->objects as $wowChar) {
            if ($wowChar->userID != WCF::getUser()->userID)  WCF::getSession()->checkPermissions(['mod.gman.canUpdateChar']);
        }
    }

    public function updateData() {
        foreach($this->objects as $wowChar) {
            bnetAPI::updateCharacter([
                'charID'        => $wowChar->charID,
                'bnetUpdate'    => $wowChar->bnetUpdate,
                'forceUpdate'   => true,
                ]);
        }
    }

    public static function bulkUpdate($isCron = false, $forceALL = false) {
        $GMAN_UPDATE_GUILDOONLY = false;
        $charList = new WowCharacterList();
        if ($GMAN_UPDATE_GUILDOONLY && !$forceALL)  $charList->getConditionBuilder()->add('inGuild = ?', [1]);
        $charList->readObjects();
        $updateList = [];
        $jobs = [];
        $counter = 0;
        foreach ($charList as $char) {
            $updateList[] = ['charID' => $char->charID, 'bnetUpdate' => $char->bnetUpdate];
            $counter++;
            if (!$isCron && $counter == GMAN_BNET_JOBSIZE) {
                $counter = 0;
                $jobs[] = new WowCharacterUpdateJob($updateList);
                $updateList = [];
            }
        }
        return ($isCron) ? $updateList : $jobs;
        // BackgroundQueueHandler::getInstance()->enqueueIn
    }

}