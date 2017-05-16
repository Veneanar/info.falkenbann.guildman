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
 *
 * @property string		    $name           Name of the Mount
 * @property integer        $spellID        Spell ID to summon Mount
 * @property integer        $itemID         Item to learn Mount
 * @property integer        $creatureId     creature ID
 * @property integer        $qualityId      quality 0: poor, 1: common 2: uncommon 3: rare, 4: epic 5: legendary
 * @property string         $icon           icon name
 * @property boolean        $isGround       ground mount
 * @property boolean        $isFlying       ground mount
 * @property boolean        $isAquatic      ground mount
 * @property boolean        $isJumping      ground mount
 *
 *
 */

class CharacterPets extends JSONExtendedDatabaseObject {

	/**
     * {@inheritDoc}
	 */
	protected static $databaseTableName = 'gman_character_pets';

	/**
	 * {@inheritDoc}
	 */
	protected static $databaseTableIndexName = 'characterID';


}