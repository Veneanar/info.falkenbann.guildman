<?php
namespace wcf\data\guild\application;
use wcf\data\DatabaseObject;

/**
 * Represents a Gildenbewerbung
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php> 
 * @package	info.falkenbann.guildman
 *
 * @property 		 $			 PRIMARY KEY 
 * @property integer		 $appID			
 * @property integer		 $threadID			Thread ID
 * @property integer		 $pollID			Umfrage ID
 * @property integer		 $charID			Character ID
 * @property string		 $name			
 * @property integer		 $autoClose			Soll automatisch abgelehnt werden Dauer im ACP setzen
 * @property integer		 $pollEnd			Umfrage Ende Dauer im ACP setzen
 * @property integer		 $assignedOfficerID			Zugewiesener Offizier
 * @property integer		 $interviewDate			Datum des INterviews
 * @property integer		 $appState			Status
 * @property integer		 $openDate			Bewerbungsdatum
 * 
 */

class GuildApplication extends DatabaseObject {
	/**
	 * {@inheritDoc}
	 */
	protected static $databaseTableName = 'gman_application';
	/**
	 * {@inheritDoc}
	 */
	protected static $databaseTableIndexName = '';

}