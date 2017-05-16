<?php
namespace wcf\system\cache\builder;
use wcf\data\guild\group\application\GuildGroupApplicationList;

/**
 * Caches all installed packages.
 *
 * @author	Marcel Werk
 * @copyright	2001-2017 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	WoltLabSuite\Core\System\Cache\Builder
 */
class GuildGroupApplicationCacheBuilder extends AbstractCacheBuilder {
	/**
     * @inheritDoc
     */
	public function rebuild(array $parameters) {
		$data = [
			'applications' => [],
			'forumIDs' => []
		];

		$applicationList = new GuildGroupApplicationList();
		$applicationList->readObjects();

		foreach ($applicationList as $application) {
			$data['applications'][$application->appID] = $application;
			$data['forumIDs'][$application->appForumID] = $application->appID;
        }

		return $data;
	}
}
