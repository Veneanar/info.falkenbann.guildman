<?php
namespace wcf\data\wow\acm;
use wcf\data\JSONExtendedDatabaseObject;

/**
 * Represents a WoW Guild Achievements
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 * @property 		 $			 PRIMARY KEY
 * @property integer		 $gacmID
 * @property string		 $bnetData
 * @property integer		 $bnetUpdate
 *
 */

class WowGuildACM extends JSONExtendedDatabaseObject {
	/**
	 * {@inheritDoc}
	 */
	protected static $databaseTableName = 'gman_wow_gacms';
	/**
	 * {@inheritDoc}
	 */
	protected static $databaseTableIndexName = '';
	/**
     * {@inheritDoc}
     */
    protected static $JSONfield = 'bnetData';
}