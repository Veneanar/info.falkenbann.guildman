<?php
namespace wcf\data\guild\bosskill;
use wcf\data\DatabaseObject;
use wcf\system\WCF;
use wcf\data\wow\boss\WowBoss;

/**
 * Represents a Bosskill (by Guild, per default)
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 * @property integer		 $feedID			 PRIMARY KEY
 * @property string		     $type			Feedtyp
 * @property integer		 $characterID			Chaarkter ID
 * @property integer		 $itemID			Item ID
 * @property integer		 $acmID			Achievment ID
 * @property integer		 $quantity			Anzahl
 * @property string		     $bonusLists			bouns f. Items
 * @property string		     $context			lootcontext
 * @property integer		 $feedTime			Zeitstempel
 * @property integer		 $inGuild			ist der Char in der Gilde
 *
 */

class Bosskill extends DatabaseObject {
    /**
     * boss
     * @var WowBoss     
     */
    private $boss = null;
	/**
     * {@inheritDoc}
     */
	protected static $databaseTableName = 'gman_bosskills';
	/**
     * {@inheritDoc}
     */
	protected static $databaseTableIndexName = 'killID';

    public static function getByStatID($statID) {
        $sql = "SELECT	*
			    FROM		wcf".WCF_N."_gman_bosskills
			    WHERE		statID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([$statID]);
		$row = $statement->fetchArray();
		if (!$row) $row = [];
		return new Bosskill(null, $row);
    }

    public function getBoss() {
        if ($this->boss===null) {
            $this->boss = new WowBoss($this->bossID);
        }
        return $this->boss;
    }

}