<?php
namespace wcf\data\wow\item;
use wcf\data\ISearchAction;
use wcf\data\ITooltipAction;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;
use wcf\system\exception\UserException;
use wcf\util\StringUtil;


/**
 * Executes WoW Character-related actions.
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */

class WowItemAction extends AbstractDatabaseObjectAction implements ISearchAction{
	/**
     * {@inheritDoc}
     */
	public static $baseClass = WoWitemEditor::class;
	/**
     * {@inheritDoc}
     */
	protected $requireACP = array();
	/**
     * @inheritDoc
     */
	protected $allowGuestAccess = ['getSearchResultList'];

    /**
     * @inheritDoc
     */
	public function validateGetSearchResultList() {
		$this->readString('searchString', false, 'data');
	}

	/**
     * @inheritDoc
     */
	public function getSearchResultList() {
        //$searchString = StringUtil::firstCharToUpperCase($this->parameters['data']['searchString']);
        //$list = [];
        //$wowCharList = new WowCharacterList();
        //$wowCharList->getConditionBuilder()->add("charname LIKE ?", [$searchString.'%']);
        //$wowCharList->sqlLimit = 10;
        //$wowCharList->readObjects();
        //$wowChars = $wowCharList->getObjects();
        ///**
        // * @var WowCharacter $wowChar
        // */
        //foreach ($wowChars as $wowChar) {
        //    $list[] = [
        //        'icon'      => $wowChar->getAvatar("avatar")->getImageTag(16),
        //        'label'     => $wowChar->name,
        //        'objectID'  => $wowChar->characterID,
        //        'type'      => 'user'
        //    ];
        //}
        //return $list;
	}
}