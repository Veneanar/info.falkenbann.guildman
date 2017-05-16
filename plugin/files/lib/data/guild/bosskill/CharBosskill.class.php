<?php
namespace wcf\data\guild\bosskill;
use wcf\data\DatabaseObject;
use wcf\data\DatabaseObjectDecorator;
use wcf\system\WCF;
use wcf\data\wow\boss\WowBoss;


/**
 * Represents a Char bosskill
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 * @property integer		 $statID			 PRIMARY KEY
 * @property integer		 $charID			 character ID
 * @property integer		 $killDate			 (first) kill date
 * @property integer		 $quantity
 * @property integer		 $lastupdate			bouns f. Items

 *
 */
class CharBosskill extends DatabaseObject {

    /**
     * bosskill
     * @var Bosskill
     */
    private $bosskill = null;
	/**
     * {@inheritDoc}
     */
	protected static $databaseTableName = 'gman_char_bosskills';
	/**
     * {@inheritDoc}
     */
	protected static $databaseTableIndexName = 'statID';

    /**
     * Summary of getBosskill
     * @return Bosskill
     */
    public function getBosskill() {
        if (empty($this->bosskill)) {
            $this->bosskill = Bosskill::getByStatID($this->statID);
        }
        return $this->bosskill;
    }

    /**
     * Summary of getBoss
     * @return WowBoss
     */
    public function getBoss() {
        return $this->getBosskill()->getBoss();
    }

}