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

class ViewableWowItemAction extends WowItemAction implements ITooltipAction{
	/**
     * {@inheritDoc}
     */
	protected $requireACP = array();
	/**
     * @inheritDoc
     */
	protected $allowGuestAccess = ['getTooltip'];

    /**
     * @inheritDoc
     */

	public function getTooltip() {
        $wowItem = null;
        if ($this->parameters['data']['isArtifact']) {
            $relicList = [];
            foreach($this->parameters['data']['itemGems'] as $relic) {
                $data = explode('-', $relic);
                $relicList[] = [
                    'itemId'        => intval($data[0]),
                    'bonusLists'    => isset($data[1]) ? explode('.', $data[1]) : '',
                    ];
            }
            //echo "<pre>"; var_dump($this->parameters['data']['itemGems']); var_dump($relicList); echo "</pre>"; die();
            $wowItem = new ViewableArtifact(
                new WowItem($this->parameters['data']['itemID']),
                $this->parameters['data']['itemContext'],
                $this->parameters['data']['itemBonuslist'],
                $relicList,
                [],
                $this->parameters['data']['itemLevel'],
                $this->parameters['data']['itemEnchant'],
                $this->parameters['data']['itemTransmog']
                );
        }
        else {
            $wowItem = new ViewableWowItem(
                new WowItem($this->parameters['data']['itemID']),
                $this->parameters['data']['itemContext'],
                $this->parameters['data']['itemBonuslist'],
                $this->parameters['data']['itemGems'],
                $this->parameters['data']['itemEnchant'],
                $this->parameters['data']['itemTransmog'],
                $this->parameters['data']['itemSet']
                );
        }
        // echo "<pre>";  var_dump($wowItem); echo "</pre>"; die();
        //             'template' => WCF::getTPL()->fetch('itemTooltip')
        return [
            'success' => true,
            'template' => WCF::getTPL()->fetch('itemTooltip', 'wcf', ['item' => $wowItem])
            ];
    }

    /**
     * @inheritDoc
     */
	public function validateGetTooltip() {
        $this->readInteger('itemID', false, 'data');
        $this->readString('itemContext', true, 'data');
        $this->readInteger('itemEnchant', true, 'data');
        $this->readInteger('itemTransmog', true, 'data');
        $this->readIntegerArray('itemBonuslist', true, 'data');
        $this->readIntegerArray('itemSet', true, 'data');
        $this->readBoolean('isArtifact', false, 'data');
        if ($this->parameters['data']['isArtifact']) {
            $this->readStringArray('itemGems', true, 'data');
        }
        else {
            $this->readIntegerArray('itemGems', true, 'data');
        }
    }
}