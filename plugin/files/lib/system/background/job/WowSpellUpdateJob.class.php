<?php
namespace wcf\system\background\job;
use wcf\system\wow\exception\AuthenticationFailure;
use wcf\system\wow\bnetAPI;

/**
 * Updates a Bunch of wow spells
 * @author	Veneanar Falkenbann
 * @copyright	2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */

class WowSpellUpdateJob extends AbstractBackgroundJob {
	/**
     * updateList
     * @var array
     */
	protected $updateList;

	/**
     * Creates the job using the given update list containing charID and bnetUpdate
     *
     * @param	array		$updateList [charID, updateBnet]
     */
	public function __construct(array $updateList) {
		$this->updateList = $updateList;
	}

	/**
     * API calls timeout between the tries.
     *
     * @return	int	5 minutes, 10 minutes, 15 minutes.
     */
	public function retryAfter() {
		switch ($this->getFailures()) {
			case 1:
				return 5 * 60;
			case 2:
				return 10 * 60;
			case 3:
				return  15 * 60;
		}
	}

	/**
     * @inheritDoc
     */
	public function perform() {
		try {
			bnetAPI::updateSpell($this->updateList);
		}
		catch (AuthenticationFailure $e) {
			// no need for retrying. Eat Exception and log the error.
			\wcf\functions\exception\logThrowable($e);
		}
	}
}
