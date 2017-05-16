<?php
namespace wcf\data\guild\group\application;
use wcf\data\DatabaseObjectEditor;
use wcf\data\IEditableCachedObject;
use wcf\system\cache\builder\GuildGroupApplicationCacheBuilder;

/**
 * Provides functions to edit Gildenbewerbungs.
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */

class GuildGroupApplicationEditor extends DatabaseObjectEditor implements IEditableCachedObject{
	/**
	 * {@inheritDoc}
	 */
	public static $baseClass = GuildGroupApplication::class;

    public static function resetCache() {
        GuildGroupApplicationCacheBuilder::getInstance()->reset();
    }

}