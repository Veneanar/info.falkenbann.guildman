<?php
namespace wcf\system\menu\user\profile\content;
use wcf\system\menu\user\profile\content\IUserProfileMenuContent;
use wcf\data\wow\character\WowCharacterList;
use wcf\data\guild\Guild;
use wcf\system\WCF;
use wcf\system\cache\runtime\GuildRuntimeChache;
use wcf\system\SingletonFactory;

/**
 * Shows user's characters
 *
 * Access to the bnetAPI
 * @author	Veneanar Falkenbann
 * @copyright	2017  Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */
class WowCharacterListContent extends SingletonFactory implements IUserProfileMenuContent {

	/**
     * @see	wcf\system\menu\user\profile\content\IUserProfileMenuContent::getContent()
     */
	public function getContent($userID) {
        $charList = new WowCharacterList();
        $charList->getConditionBuilder()->add('userID = ?', [$userID]);
        $charList->sqlOrderBy = "c_level DESC";
        $charList->readObjects();
		WCF::getTPL()->assign(array(
			'stats'     => $charList->countObjects(),
			'charList'  => $charList->getObjects(),
            'guild'     => GuildRuntimeChache::getInstance()->getCachedObject(),
		));
		return WCF::getTPL()->fetch('userProfileWowCharacterList');
	}

    /**
     * @see	wcf\system\menu\user\profile\content\IUserProfileMenuContent::isVisible()
     */
	public function isVisible($userID) {
		// only owner and allowed users can see tab
		$this->visible = false;
		if ($userID == WCF::getUser()->userID) {
			$this->visible = true;
		}
		if (WCF::getSession()->getPermission('user.gman.canViewChar')) {
			$this->visible = true;
		}
		return $this->visible;
	}
}
