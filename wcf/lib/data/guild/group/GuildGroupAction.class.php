<?php
namespace wcf\data\guild\group;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\wow\character\WowCharacterAction;
use wcf\data\wow\character\WowCharacterList;
use wcf\data\guild\group\GuildGroupList;
use wcf\data\guild\Guild;
use wcf\system\clipboard\ClipboardHandler;
use wcf\data\IClipboardAction;

/**
 * Executes Gildenbewerbung-related actions.
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */

class GuildGroupAction extends AbstractDatabaseObjectAction implements IClipboardAction{
	/**
	 * {@inheritDoc}
	 */
	public static $baseClass = GuildGroupEditor::class;
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
	protected $requireACP = array('update', 'delete');
	/**
	 * {@inheritDoc}
	 */
	protected $allowGuestAccess = array();

    public function create() {
        $guildGroup = parent::create();
           // 'changeWCFGroup'    => $this->wcfGroupID > 0 ? true : false,
          //  'changeRank'        => $this->gameRank < 11 ? true : false,
        if ($this->parameters['changeRank']) {
            $charList = new WowCharacterList();
            $charList->getConditionBuilder()->add("guildRank = ?", [$this->parameters['data']['gameRank']]);
            $charList->readObjects();
            $objectAction = new WowCharacterAction($charList->getObjects(), 'setRank', [
                'rank'      => $this->parameters['data']['gameRank'],
            ]);
            $objectAction->executeAction();
        }
        if ($this->parameters['changeWCFGroup']) {
            $charList = $guildGroup->getMemberList();
            $objectAction = new WowCharacterAction($charList, 'setWCFGroups', ['oldWCFGroups' => []]);
            $objectAction->executeAction();
        }



    }

    public function update() {
        $guild = new Guild();
        $oldWCFGroups = !empty($this->parameters['oldWCFGroups']) ? $this->parameters['oldWCFGroups'] : $guild->convertToWCFGroup($guild->getGuildGroupIds());
        parent::update();
        foreach($this->objects as $guildGroup) {
            if (isset($this->parameters['changeRank']) && $this->parameters['changeRank']) {
                $charList = $guildGroup->getMemberList();
                $objectAction = new WowCharacterAction($charList, 'setRank', [
                    'rank'      => $this->parameters['data']['gameRank'],
                ]);
                $objectAction->executeAction();
            }
            if ((isset($this->parameters['changeWCFGroup']) && $this->parameters['changeWCFGroup'])) {
                $charList = $guildGroup->getMemberList();
                $objectAction = new WowCharacterAction($charList, 'setWCFGroups', ['oldWCFGroups' => $oldWCFGroups]);
                $objectAction->executeAction();
            }
        }
    }

    public function validateDelete() {
        parent::validateUpdate();
    }

    public function delete() {
        foreach($this->objects as $guildGroup) {
            if ($guildGroup->wcfGroupID > 0) {
                $charList = $this->guildGroupObject->getMemberList();
                $objectAction = new WowCharacterAction($charList, 'setWCFGroups');
                $objectAction->executeAction();
            }
        }
        parent::delete();
        $this->unmarkAll();
    }

	/**
     * @inheritDoc
     */
	public function validateUnmarkAll() {
		// does nothing
	}

	/**
     * @inheritDoc
     */
	public function unmarkAll() {
		ClipboardHandler::getInstance()->removeItems(ClipboardHandler::getInstance()->getObjectTypeID('info.falkenbann.gman.guildgroup'));
	}

	/**
     * Unmarks chars.
     *
     * @param	integer[]	$guildGroupIDs
     */
	protected function unmarkItems(array $guildGroupIDs = []) {
		if (empty($charIDs)) {
			$charIDs = $this->objectIDs;
		}

		if (!empty($charIDs)) {
			ClipboardHandler::getInstance()->unmark($guildGroupIDs, ClipboardHandler::getInstance()->getObjectTypeID('info.falkenbann.gman.guildgroup'));
		}
	}
}