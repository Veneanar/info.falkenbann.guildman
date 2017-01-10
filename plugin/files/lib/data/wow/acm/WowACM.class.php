<?php
namespace wcf\data\wow\acm;
use wcf\data\JSONExtendedDatabaseObject;

/**
 * Represents a WoW Char Achievements
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 * @property integer		 $acmID			 PRIMARY KEY
 * @property string		 $bnetData			bnet Daten JSON siehe API
 * @property integer		 $bnetUpdate			bnet Update
 *
 */

class WowACM extends JSONExtendedDatabaseObject {
	/**
	 * {@inheritDoc}
	 */
	protected static $databaseTableName = 'gman_wow_acms';
	/**
	 * {@inheritDoc}
	 */
	protected static $databaseTableIndexName = 'acmID';
	/**
     * {@inheritDoc}
     */
    protected static $JSONfield = 'bnetData';
}