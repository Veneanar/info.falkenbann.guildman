<?php
namespace wcf\data\wow\spell;
use wcf\data\ISearchAction;
use wcf\data\ITooltipAction;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\exception\UserInputException;
use wcf\system\WCF;
use wcf\system\exception\UserException;
use wcf\util\StringUtil;
use wcf\system\background\job\WowSpellUpdateJob;


/**
 * Executes WoW Character-related actions.
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */

class WowSpellAction extends AbstractDatabaseObjectAction implements ISearchAction, ITooltipAction{
	/**
     * {@inheritDoc}
     */
	public static $baseClass = WowSpellEditor::class;
	/**
     * @inheritDoc
     */
	protected $allowGuestAccess = ['getTooltip', 'getSearchResultList'];

    /**
     * @inheritDoc
     */
	public function getTooltip() {
        $wowItem = new WowSpell($this->parameters['data']['spellID']);
        //             'template' => WCF::getTPL()->fetch('itemTooltip')
        return [
            'success' => true,
            'hallo' => 'hallo',
            'template' => WCF::getTPL()->fetch('spellTooltip', 'wcf', ['item' => $wowItem])
            ];
    }

    /**
     * @inheritDoc
     */
	public function validateGetTooltip() {
        $this->readInteger('itemID', false, 'data');
    }

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

    public static function bulkUpdate($isCron = false, $forceALL = false) {
        $spellList = new WowSpellList();
        if (!$forceALL)  $spellList->getConditionBuilder()->add("bnetData IS NULL OR bnetData = ''");
        $spellList->readObjectIDs();
        $spellList->readObjects();
        $spells = $spellList->getObjects();
        $updateList = [];
        $jobs = [];
        $counter = 0;
        foreach ($spells as $spell) {
            $updateList[] = $spell->spellID;
            $counter++;
            if (!$isCron && $counter == (GMAN_BNET_JOBSIZE *10)) {
                $counter = 0;
                $jobs[] = new WowSpellUpdateJob($updateList);
                $updateList = [];
            }
        }
        return ($isCron) ? $updateList : $jobs;
    }

}