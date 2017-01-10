<?php
namespace wcf\data\wow\item;
use wcf\data\JSONExtendedDatabaseObject;

/**
 * Represents a WoW Item classes
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 * @property integer		 $itemclassID			 PRIMARY KEY
 * @property string		 $bnetData			JSON Daten siehe Bnet API
 * @property integer		 $bnetUpdate			letztes Update
 *
 */

class WowItemClasses extends JSONExtendedDatabaseObject {
	/**
	 * {@inheritDoc}
	 */
	protected static $databaseTableName = 'gman_wow_itemclass';
	/**
	 * {@inheritDoc}
	 */
	protected static $databaseTableIndexName = 'itemclassID';
	/**
     * {@inheritDoc}
     */
    protected static $JSONfield = 'bnetData';
}