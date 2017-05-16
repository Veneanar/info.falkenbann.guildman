<?php
namespace wcf\system\option\user;
use wcf\data\user\option\UserOption;
use wcf\data\user\User;
use wcf\data\wow\character\WowCharacter;
use wcf\system\WCF;

/**
 * User option output implementation for the output of WoW Character deatils.
 *
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 */
class WowCharacterDetails implements IUserOptionOutput {
	/**
	 * @see	wcf\system\option\user\IUserOptionOutput::getOutput()
	 */

	public function getOutput(User $user, UserOption $option, $value) {
		if (empty($value)) return '';
		$char = new  WowCharacter(intval($value));
		return WCF::getTPL()->fetch('charShow24', 'wcf', ['char' => $char]);
	}
}
