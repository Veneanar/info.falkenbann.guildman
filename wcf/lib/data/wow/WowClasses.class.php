<?php
namespace wcf\data\wow;
use wcf\data\DatabaseObject;
use wcf\system\WCF;

/**
 * Represents a WoW Klassen
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 * @property integer		$wclassID      PRIMARY KEY
 * @property integer	    $mask
 * @property string		    $powerType			Ressourcentyp
 * @property string		    $name			    Klassenname
 * @property string		    $color			    Klassenfarbe HMLT Code
 *
 */

class WowClasses extends DatabaseObject {
	/**
	 * {@inheritDoc}
	 */
	protected static $databaseTableName = 'gman_wow_classes';
	/**
	 * {@inheritDoc}
	 */
	protected static $databaseTableIndexName = 'wclassID';

    public function getTag() {
        return '<span style="color:'.$this->data['color'].'">'. $this->name .'</span>';
    }

}