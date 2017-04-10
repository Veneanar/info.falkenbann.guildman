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
        $wowItem = new ViewableWowItem(
            new WowItem($this->parameters['data']['itemID']),
            $this->parameters['data']['itemContext'],
            $this->parameters['data']['itemBonuslist'],
            $this->parameters['data']['itemGems'],
            $this->parameters['data']['itemEnchant'],
            $this->parameters['data']['itemTransmog'],
            $this->parameters['data']['itemSet']
            );
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
        $this->readIntegerArray('itemBonuslist', true, 'data');
        $this->readInteger('itemEnchant', true, 'data');
        $this->readInteger('itemTransmog', true, 'data');
        $this->readIntegerArray('itemGems', true, 'data');
        $this->readIntegerArray('itemSet', true, 'data');
    }
}