<?php
namespace wcf\data\wow\item;
use wcf\data\JSONExtendedDatabaseObject;

/**
 * Represents a WoW Items
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 * @property        integer		    $itemID			                PRIMARY KEY
 * @property        string		    $bnetData
 * @property        integer		    $bnetUpdate
 *
 * @property-read	integer			$id								ID des Items
 * @property-read	integer			$disenchantingSkillRank			Skill des Verzauberers den er haben muss zum entzaubern
 * @property-read	string			$description					Beschreibung
 * @property-read	string			$name							Name des Items
 * @property-read	string			$icon							Bild des Items
 * @property-read	string			$stackable						Ob das item stapelbar ist
 * @property-read	integer			$itemBind						Gibt an ob das Item bei aufheben gebunden ist
 * @property-read	array			$bonusStats						Die Bonuswerte des item
 * @property-read	array			$itemSpells						Zusatzzauber des Items
 * @property-read	integer			$buyPrice						Kaufpreis
 * @property-read	integer			$itemClass						???
 * @property-read	integer			$itemSubClass					???
 * @property-read	integer			$containerSlots					gibt an ob Sockelplätze vorhanden sind
 * @property-read	array			$weaponInfo						Waffeninformation
 * @property-read	integer			$inventoryType					Für welchen Platz es angedacht ist
 * @property-read	string			$equippable						Ob es angelegt werden kann
 * @property-read	integer			$itemLevel						Gegenstandsstufe des Items
 * @property-read	integer			$maxCount						Ob das Item eizigartig ist
 * @property-read	integer			$maxDurability					max. Handelzeit bei z.B einem Ini Drop
 * @property-read	integer			$minReputation					Benötigter Rang (z.B. Fraktionsrang) um das Item anziehen zu können
 * @property-read	integer			$quality						Qualitätsstufe des Items (selten, episch usw.)
 * @property-read	integer			$sellPrice						Verkaufspreis beim Händler
 * @property-read	integer			$requiredSkill					Ob ein Berufeskill benötigt wird
 * @property-read	integer			$requiredLevel					Level die benötigt werden zum anziehen des Items
 * @property-read	integer			$requiredSkillRank				Berufeskill zum anziehen des Items
 * @property-read	array			$socketInfo						???
 * @property-read	array			$itemSource						???
 * @property-read	string			$baseArmor						Gründ Rüstung des Items
 * @property-read	string			$hasSockets						Ob das Item Sockel hat
 * @property-read	integer			$isAuctionable					Ob es im Auktionshaus verkauft werden kann
 * @property-read	integer			$armor							Rüstungsklasse
 * @property-read	integer			$displayInfoId					???
 * @property-read	integer			$nameDescription				Die Beschreibung des Item
 * @property-read	integer			$nameDescriptionColor			Farbe der Beschreibung
 * @property-read	string			$upgradable						Ob das Item Upgrade baer ist mit zb. Obliterium
 * @property-read	string			$heroicTooltip					???
 * @property-read	string			$context						???
 * @property-read	array			$bonusLists						???
 * @property-read	array			$availableContexts				???
 * @property-read	array			$bonusSummary					???
 * @property-read	integer			$artifactId						Artefakt ID
 *
 */

class WowItem extends JSONExtendedDatabaseObject {
	/**
	 * @inheritDoc
	 */
	protected static $databaseTableName = 'gman_wow_items';
	/**
	 * @inheritDoc
	 */
	protected static $databaseTableIndexName = 'itemID';
	/**
     * {@inheritDoc}
     */
    protected static $JSONfield = 'bnetData';

}