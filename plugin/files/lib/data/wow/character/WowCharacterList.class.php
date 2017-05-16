<?php
namespace wcf\data\wow\character;
use wcf\data\DatabaseObjectList;
use wcf\system\background\job\WowCharacterUpdateJob;

/**
 * Represents a list of WoW Charackters.
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */

class WowCharacterList extends DatabaseObjectList {
	/**
	 * {@inheritDoc}
	 */
	public $className = WowCharacter::class;

    public function getGuildCharacters($jobList = false, $splitcount = GMAN_BNET_JOBSIZE) {
        $this->getConditionBuilder()->add('inGuild = ?', [1]);
        $this->readObjects();
        $charcterList = $this->getObjects();
        return $jobList ? $this->createJobList($charcterList, $splitcount) : $charcterList;
    }

    public function getAllCharacters($jobList = false, $splitcount = GMAN_BNET_JOBSIZE) {
        $this->readObjects();
        $charcterList = $this->getObjects();
        return $jobList ? $this->createJobList($charcterList, $splitcount) : $charcterList;
    }

    private function createJobList($charcterList, $splitcount) {
        $jobs = [];
        $counter = 0;
        $updateList = [];
        foreach ($charcterList as $character) {
            $updateList[] = $character;
            $counter++;
            if ($counter == GMAN_BNET_JOBSIZE) {
                $counter = 0;
                $jobs[] = new WowCharacterUpdateJob($character);
                $updateList = [];
            }
        }
    }
}