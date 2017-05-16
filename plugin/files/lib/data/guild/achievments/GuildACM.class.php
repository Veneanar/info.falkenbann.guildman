<?php
namespace wcf\data\guild\achievments;
use wcf\data\DatabaseObject;

/**
 * Represents a Reached Guild Achievments
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 * @property 		 $			 PRIMARY KEY
 * @property integer		 $acmID
 * @property integer		 $gacmID			Achievment ID
 * @property integer		 $acmTime			Zeitpunkt
 * @property integer		 $articelID			Artikel
 *
 */

class GuildACM extends DatabaseObject {
	/**
	 * {@inheritDoc}
	 */
	protected static $databaseTableName = 'gman_guild_acm';
	/**
	 * {@inheritDoc}
	 */
	protected static $databaseTableIndexName = '';

}