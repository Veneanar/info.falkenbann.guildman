<?php
namespace wcf\data\wow\character;
use wcf\data\DatabaseObjectDecorator;
use wcf\data\DatabaseObject;

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

     /**
	 * Creates a new DatabaseObjectDecorator object.
	 *
	 * @param	DatabaseObject		$object
	 */
	public function __construct(DatabaseObject $object) {
        parent::__construct( $object);

    }

    public function update() {


    }

}