<?php
namespace wcf\system\worker;
use wcf\data\object\type\ObjectType;
use wcf\data\object\type\ObjectTypeCache;
use wcf\system\request\LinkHandler;
use wcf\data\wow\spell\WowSpell;
use wcf\data\wow\spell\WowSpellList;
use wcf\system\WCF;
use wcf\system\wow\SpellUpdate;
use wcf\system\wow\bnetUpdate;

/**
 * Worker implementation for updating spells.
 *
 * @author	Veneanar Falkenbann
 * @copyright	2017  Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 */
class SpellUpdateWorker extends AbstractRebuildDataWorker {

	/**
     * @inheritDoc
     */
	protected $objectListClassName = WowSpellList::class;

	/**
     * @inheritDoc
     */
	protected $limit = 10;

	/**
     * @inheritDoc
     */
	public function execute() {

        parent::execute();
        if (!$this->loopCount) {
            //& bnetUpdate::updateRaidBosses();
        }

        if (!count($this->objectList)) {
			return;
		}
        foreach ($this->objectList as $spell) {
            $sync = new SpellUpdate($spell->getObjectID());
            $sync->run();
        }
    }

	/**
     * @inheritDoc
     */
	public function validate() {
		WCF::getSession()->checkPermissions(['admin.gman.canChangeGuild']);
	}


}
