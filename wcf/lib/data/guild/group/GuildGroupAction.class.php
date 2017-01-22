<?php
namespace wcf\data\guild\group;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\wow\character\WowCharacterAction;
use wcf\data\guild\group\GuildGroupList;
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

    public function update() {
        parent::update();
        if (isset($this->parameters['rankChange'])) {
            $charList = GuildGroupList::getMemberListRank($this->parameters['oldRank']);
            $action = new WowCharacterAction($charList, 'changeRank', [
                'rank' => $this->parameters['data']['gameRank'],
            ]);
            $action->executeAction();
        }
        if (isset($this->parameters['groupChange'])) {
            $charList = GuildGroupList::getMemberList($this->parameters['oldGroup']);

            if ($this->parameters['data']['groupWcfID']>0) {
 			    $action = new WowCharacterAction($charList, 'addToGroups', [
				    'groups' => [$this->parameters['data']['groupWcfID']],
				    'addDefaultGroups' => false,
                    'addWCFGroups' => true
			    ]);
			    $action->executeAction();
            }
            if ($this->parameters['oldGroup']>0) {
			    $action = new WowCharacterAction($charList, 'removeFromGroups', [
				    'groups' => [$this->parameters['oldGroup']],
                    'deleteWCFGroups' => true
			    ]);
			    $action->executeAction();
            }
        }
    }

    public function validateDelete() {
        parent::validateUpdate();
    }

    public function delete() {
        parent::delete();
        /**
         * @var $guildGroup GuildGroup
         */
        foreach($this->objects as $guildGroup) {
            if ($guildGroup->wcfGroupID>0) {
                $charList = GuildGroupList::getMemberList($guildGroup->wcfGroupID);
			    $action = new WowCharacterAction($charList, 'removeFromGroups', [
				    'groups' => [$guildGroup->wcfGroupID],
                    'deleteWCFGroups' => true
			    ]);
			    $action->executeAction();
            }
        }
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