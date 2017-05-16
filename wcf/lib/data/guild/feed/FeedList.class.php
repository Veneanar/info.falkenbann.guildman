<?php
namespace wcf\data\guild\feed;
use wcf\data\DatabaseObject;

/**
 * Represents a Char Feeds
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 * @property integer		 $feedID			 PRIMARY KEY
 * @property string		     $type			Feedtyp
 * @property integer		 $charID			Chaarkter ID
 * @property integer		 $itemID			Item ID
 * @property integer		 $acmID			Achievment ID
 * @property integer		 $quantity			Anzahl
 * @property string		     $bonusLists			bouns f. Items
 * @property string		     $context			lootcontext
 * @property integer		 $feedTime			Zeitstempel
 * @property integer		 $inGuild			ist der Char in der Gilde
 *
 */

class FeedList extends DatabaseObject {
	/**
	 * {@inheritDoc}
	 */
	protected static $databaseTableName = 'gman_feedlist';
	/**
	 * {@inheritDoc}
	 */
	protected static $databaseTableIndexName = 'feedID';

}