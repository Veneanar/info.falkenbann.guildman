<?php
namespace wcf\data\wow;
use wcf\data\DatabaseObject;
use wcf\system\WCF;

/**
 * Represents a WoW Rassen
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *

 * @property integer		$wraceID
 * @property integer		$mask
 * @property string		    $side
 * @property integer		$sideID
 * @property string		    $name
 *
 */

class WowRace extends DatabaseObject {
	/**
     * {@inheritDoc}
	 */
	protected static $databaseTableName = 'gman_wow_races';
	/**
	 * {@inheritDoc}
	 */
	protected static $databaseTableIndexName = 'wraceID';

    private $allyColor = '';

    private $hordeColor = '';

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

    public function getTag() {
        $color =  $this->sideID==0 ? 'color-alliance' : 'color-horde';
        return '<span class="'.$color.'">'. $this->getName() .'</span>';
    }
}