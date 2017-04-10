<?php
namespace wcf\data\wow\character\slot;
use wcf\data\DatabaseObject;
use wcf\system\WCF;

/**
 * Represents a WoW Mount
 * @author	Veneanar Falkenbann
 * @copyright	2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 * @property string		    $name           Name of the Mount
 * @property integer        $slotID         PRIMARY
 * @property string         $cssName        css Class
 * @property boolean        $cosmetic       is cosmetic slot
 * @property boolean        $optional       is optional slot
 * @property boolean        $isAquatic      ground mount
 * @property boolean        $isJumping      ground mount
 *
 *
 */

class CharacterSlot extends DatabaseObject {

	/**
     * {@inheritDoc}
     */
	protected static $databaseTableName = 'gman_wow_charslot';

	/**
     * {@inheritDoc}
     */
	protected static $databaseTableIndexName = 'slotID';

    /**
     * full local Name of the Slot
     * @var string
     */
    private $fullName = '';

    /**
     * returns the locaized slotname
     * @return string
     */
    public function getName() {
        if (empty($this->fullName)) {
            $this->fullName = WCF::getLanguage()->get($this->name);
        }
        return $this->fullName;
    }

    public function __toString() {
        return $this->fieldName();
    }

}