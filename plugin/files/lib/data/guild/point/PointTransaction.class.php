<?php
namespace wcf\data\guild\point;
use wcf\data\DatabaseObject;

/**
 * Represents a Punktetransaktion
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 * @property integer		 $transID			 PRIMARY KEY
 * @property integer		 $charID			Charackter der die Punkte bekommt
 * @property integer		 $groupID			Gruppe
 * @property integer		 $eventID			Event von welchem die Punkte kommen
 * @property integer		 $amount			Menge
 * @property integer		 $typeID			Typ
 * @property integer		 $itemID			Item, falls gegeben
 * @property string		 $comment			Kommentar (optional)
 * @property integer		 $issuerID			Charakter, der die Puntke vergeben hat
 * @property integer		 $transDate			Transaktionsdautm
 * @property integer		 $autoDelete			Wird diese Transaktion automatisch entfernt
 * @property integer		 $deleteDate			Loeschdatum
 *
 */

class PointTransaction extends DatabaseObject {
	/**
	 * {@inheritDoc}
	 */
	protected static $databaseTableName = 'gman_pointtrans';
	/**
	 * {@inheritDoc}
	 */
	protected static $databaseTableIndexName = 'transID';

}