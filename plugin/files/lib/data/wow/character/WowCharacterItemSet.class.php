<?php
namespace wcf\data\wow\character;
use wcf\data\DatabaseObjectDecorator;
use wcf\data\DatabaseObject;
use wcf\system\WCF;

/**
 * Provides methods for WoW Charackter mit Items.
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */

class WowCharacterItemSet extends DatabaseObjectDecorator {
	/**
	 * {@inheritDoc}
	 */
	public static $baseClass = WowCharacter::class;

    public $averageItemLevelEquipped = 0;

    public $averageItemLevel = 0;

    private $equip = [];

     /**
	 * Creates a new DatabaseObjectDecorator object.
	 *
	 * @param	DatabaseObject		$object
	 */
	public function __construct(DatabaseObject $object) {
        // parent::__construct($object);
			$sql = "SELECT	*
				FROM	wcf".WCF_N."_gman_character_equip
				WHERE	charID = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute([$object->getObjectID()]);
			$row = $statement->fetchArray();
			if ($row === false) $row = [];
            $this->averageItemLevel = @$row['averageItemLevel'] ?: 0;
            $this->averageItemLevelEquipped = @$row['averageItemLevelEquipped'] ?: 0;
            $this->equip  = $row;
    }



    /**
     * Escute item calls.
     *
     * @param	string		$name   name of the slot
     * @param   integer     $redner rendertype
     * @return  string|null
     */
   public function __call($name, $render = 0) {
       if ($this->__isset($name)) {
           if ($render == 0) {
               return $this->equip[$name];
           }
           else {
               return $this->equip[$name];
           }
       }
       return null;
    }
}