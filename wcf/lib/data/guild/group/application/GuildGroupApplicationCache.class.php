<?php
namespace wcf\data\guild\group\application;
use wcf\system\cache\builder\GuildGroupApplicationCacheBuilder;
use wcf\system\SingletonFactory;

/**
 * Manages the package cache.
 *
 * @author	Marcel Werk
 * @copyright	2001-2017 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	WoltLabSuite\Core\Data\Package
 */
class GuildGroupApplicationCache extends SingletonFactory {
	/**
	 * list of cached packages
     * @var	mixed[][]
	 */
	protected $applications = [];

	/**
	 * @inheritDoc
	 */
	protected function init() {
		$this->applications = GuildGroupApplicationCacheBuilder::getInstance()->getData();
	}

	/**
	 * Returns a specific package.
	 *
     * @param	integer		$appID
	 * @return	GuildGroupApplication
	 */
	public function getApplication($appID) {
		if (isset($this->applications['applications'][$appID])) {
			return $this->applications['applications'][$appID];
		}
		return null;
	}

    public function getApplicationForBord($boradID) {
		if (isset($this->applications['forumIDs'][$boradID])) {
			return $this->applications['applications'][$this->applications['forumIDs'][$boradID]];
		}
		return null;
    }

}
