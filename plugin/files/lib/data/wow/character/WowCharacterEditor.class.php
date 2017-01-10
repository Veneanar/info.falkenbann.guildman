<?php
namespace wcf\data\wow\character;
use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit WoW Charackters.
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php> 
 * @package	info.falkenbann.guildman
 * 
 */

class WowCharacterEditor extends DatabaseObjectEditor {
	/**
	 * {@inheritDoc}
	 */
	public static $baseClass = WowCharacter::class;

}