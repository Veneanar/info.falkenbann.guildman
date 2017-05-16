<?php
namespace wcf\data\wow\character;
use wcf\system\WCF;
use wcf\util\JSON;
use wcf\data\JSONExtendedDatabaseObject;

/**
 * Represents a WoW Mount
 * @author	Veneanar Falkenbann
 * @copyright	2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman

 * @property integer        $id        Spell ID to summon Mount
 * @property string		    $name           Name of the Mount
 * @property integer        $statistics         Item to learn Mount
 * @property integer        $bnetUpdate          creature ID
 * @property array          $subCategories      quality 0: poor, 1: common 2: uncommon 3: rare, 4: epic 5: legendary

 *
 *
 */

class CharacterStatistics extends JSONExtendedDatabaseObject {

	/**
     * {@inheritDoc}
	 */
	protected static $databaseTableName = 'gman_character_statistics';

	/**
	 * {@inheritDoc}
	 */
	protected static $databaseTableIndexName = 'characterID';

    public function getMaincategories() {
        return $this->data;
    }


}