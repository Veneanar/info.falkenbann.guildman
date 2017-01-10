<?php
namespace wcf\data\guild\group;
use wcf\data\DatabaseObject;

/**
 * Represents a Gildenbewerbung
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 * @property integer		 $groupID			    PRIMARY KEY
 * @property string		     $title			        Name der Gruppe
 * @property string		     $teaser			    Kurzbeschreibung f. minibox und wowprogress
 * @property integer		 $wcfGroupID			Gruppen ID vom WSC
 * @property integer		 $showCalender			zeige im Kalender
 * @property string		     $calendarTitle			Kalender Standarttitel
 * @property string		     $calendartext			Kalendar Stadrttext
 * @property integer		 $fetchCalendar			Synchronisiere WoW Kalender
 * @property string		     $calendarQuerry		Kalendererkennung
 * @property string		     $gameTitle			    Name der Ranges im Spiel
 * @property integer		 $gameRank			    Rang ID (0-10)
 * @property integer		 $showRoaster			zeige Gruppe im Roaster
 * @property integer		 $articIeID			    Artikel ID vom CMS
 * @property integer		 $threadID			    Themen ID vom Forum
 * @property integer		 $boardID			    Forum ID
 * @property integer		 $mediaID			    Medien ID (Bild)
 * @property integer		 $isRaidgruop			Ist das eine Raidgruppe
 * @property integer		 $fetchWCL			    synchronisiere mit WCL
 * @property string		     $wclQuerry			    WCL name der Gruppe
 * @property integer		 $orderNo			    Sortierung
 * @property integer		 $lastUpdate			Letztes Update
 *
 */

class GuildGroup extends DatabaseObject {
	/**
	 * {@inheritDoc}
	 */
	protected static $databaseTableName = 'gman_group';
	/**
	 * {@inheritDoc}
	 */
	protected static $databaseTableIndexName = 'groupID';

}