<?php
namespace wcf\system\worker;
use wcf\data\object\type\ObjectType;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\request\LinkHandler;
use wcf\data\wow\character\WowCharacter;
use wcf\data\wow\character\WowCharacterList;
use wcf\system\WCF;
use wcf\system\wow\bnetUpdate;

/**
 * Worker implementation for updating characters.
 *
 * @author	Veneanar Falkenbann
 * @copyright	2017  Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 */
class CharacterUpdateWorker extends AbstractRebuildDataWorker {

	/**
     * @inheritDoc
     */
	protected $objectListClassName = WowCharacterList::class;

	/**
     * @inheritDoc
     */
	protected $limit = 2;

	/**
     * @inheritDoc
     */
	protected function initObjectList() {
		parent::initObjectList();
        if (isset($this->parameters['guildOnly'])) {
            $this->objectList->getConditionBuilder()->add("inGuild = 1");
        }
	}

	/**
     * @inheritDoc
     */
	public function validate() {
		WCF::getSession()->checkPermissions(['admin.gman.canChangeGuild']);
	}

	/**
     * @inheritDoc
     */
	public function execute() {
        // $this->objectList->getConditionBuilder()->add('characterID BETWEEN ? AND ?', [$this->limit * $this->loopCount + 1, $this->limit * $this->loopCount + $this->limit]);
        parent::execute();
        if (!$this->loopCount) {
            bnetUpdate::updateGuildMemberList();
        }
        if (!count($this->objectList)) {
			return;
		}
        foreach ($this->objectList as $character) {
            bnetUpdate::updateCharacter([$character], false);
        }
    }
}
