<?php
namespace wcf\data\wow\character;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\exception\UserInputException;

/**
 * Executes WoW Charackter-related actions.
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */

class WowCharacterAction extends AbstractDatabaseObjectAction {
	/**
	 * {@inheritDoc}
	 */
	public static $baseClass = WowCharacterEditor::class;
	/**
	 * {@inheritDoc}
	 */
	protected $permissionsUpdate = array();
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
		$this->readBoolean('includeUserGroups', false, 'data');
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
		$wowCharList->getConditionBuilder()->add("charname LIKE ?", [$searchString.'%']);
		if (!empty($excludedSearchValues)) {
			$wowCharList->getConditionBuilder()->add("charname NOT IN (?)", [$excludedSearchValues]);
		}
		$wowCharList->sqlLimit = 10;
		$wowCharList->readObjects();
        /**
         * @var WowCharacter $wowChar
         */
		foreach ($wowCharList as $wowChar) {
			$list[] = [
				'icon' => $wowChar->getAvatar("avatar")->getImageTag(16),
				'label' => $wowChar->charname,
				'objectID' => $wowChar->charID,
				'type' => 'user'
			];
		}
		return $list;
	}

}