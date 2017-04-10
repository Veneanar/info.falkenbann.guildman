<?php
namespace wcf\data\wow\item;
use wcf\system\WCF;
use wcf\system\wow\bnetAPI;
use wcf\system\wow\bnetIcon;
use wcf\system\request\LinkHandler;
use wcf\system\request\IRouteController;
use wcf\util\JSON;
use wcf\data\DatabaseObject;
use wcf\data\wow\spell\WowSpell;
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

    /**
     * Icon of the Item
     * @var WowItemIcon
     */
    protected $iconimage = null;

    const SMALL = 18;
    const MEDIUM = 36;
    const LARGE = 56;



    /**
	 * Summary of __construct
	 * @param integer $id
	 * @param string $context
	 * @param integer $bonusList
	 * @param integer $gems
	 * @param integer $enchant
	 * @param integer $transmog
	 * @param integer $set
	 */
	public function __construct($id) {
        $row = [];
        if (is_string($id)) {
            $row = $this->constructEmpty($id);
        }
        else {
            if ($this->isAvaible($id) < 1) {
                bnetAPI::getItem($id);
            }
			$sql = "SELECT	*
				FROM	".static::getDatabaseTableName()."
				WHERE	".static::getDatabaseTableIndexName()." = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute([$id]);
			$row = $statement->fetchArray();
            if ($row === false) {
                $row = [];
            }
        }
		if (isset($row[static::$JSONfield])) {
            $bnetData = empty($row[static::$JSONfield]) ? [] : json_decode($row[static::$JSONfield], true);
            $row[static::$JSONfield] = '';
            $row = array_merge($bnetData, $row);
		}
		$this->handleData($row);
    }

    private static function isAvaible($id) {
        $sql = "SELECT	COUNT(*)
			    FROM		wcf".WCF_N."_gman_wow_items
			    WHERE		itemID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([$id]);
        return $statement->fetchColumn();
    }

    /**
     * checks item context
     * @param string        $context
     * @return boolean
     */
    public function checkContext($context) {
        if (isset($this->availableContexts)) return in_array($context, $this->availableContexts);
        return false;
    }

    /**
     * constructs an empty slotitem
     * @param mixed $name
     * @return mixed
     */
    private function constructEmpty($name) {
	    $sql = "SELECT	*
				FROM	".static::getDatabaseTableName()."
				WHERE	itemName LIKE ? LIMIT 1";
	    $statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([$name]);
		return  $statement->fetchArray();
    }

    /**
     * Returns the item's icon
     *
     * @return	\wcf\data\user\avatar\IUserAvatar
     */
	public function getIcon() {
        if ($this->iconimage === null) {
            if (isset($this->icon)) $this->iconimage = new WowItemIcon($this);
        }
        return $this->iconimage;
    }

    /**
     * !returns a simplified tooltip info
     * @return string
     */
    public function getSimpleTooltip() {
        return $this->name;
    }


    /**
     * Returns the Itemname
     * @return string
     */
    public function getName() {
        if ($this->itemID < 50) return WCF::getLanguage()->get('wcf.global.gman.notequiped');
        return $this->name;
    }

    /**
     * Returns the Name as HTML tag
     * @return string
     */
    public function getNameTag() {
        return '<span class="color-q'.$this->quality.'">'. $this->getName().'</span>';
    }


    /**
     * get the requirements of an item as string array
     * @return string[]
     */
    public function getRequierments() {
        $rq = [];
        if ($this->requiredLevel) $rq[] = WCF::getLanguage()->getDynamicVariable('wcf.page.gman.item.levelrequierd',['level' => $this->requiredLevel]);
        if ($this->requiredSkill) $rq[] = WCF::getLanguage()->getDynamicVariable('wcf.page.gman.item.skillrequired.', ['skill' => $this->requiredSkill,'level' =>  $this->requiredLevel]);
        if ($this->minReputation) $rq[] = WCF::getLanguage()->getDynamicVariable('wcf.page.gman.item.reprequired', ['rep' => $this->minReputation, 'faction' =>  WCF::getLanguage()->get('wcf.global.faction.' . $this->minFactionId)]);
        return $rq;
    }

    /**
     * returns the name of the slot type
     * @return string
     */
    public function getInventoryType() {
        return WCF::getLanguage()->get('wcf.global.gman.slottype'. $this->inventoryType);
    }

    /**
     * Returns the binding type as string
     * @return string|\wcf\data\language\string
     */
    public function getItemBind() {
        return $this->itemBind==1 ? WCF::getLanguage()->get('wcf.page.gman.item.bop') : WCF::getLanguage()->get('wcf.page.gman.item.boe');
    }

    /**
     * returns how often a item is equipable
     * @return null|string|\wcf\data\language\string
     */
    public function getMaxCount() {
        return $this->maxCount ? WCF::getLanguage()->get('wcf.page.gman.item.maxcount') : null;
    }

    /**
     * returns the price as gold, silver, copper arry
     * @return array
     */
    public function getPrice() {
        $money = intval($this->sellPrice);
        $copper = $money % 100;
        $silver = floor( ( $money % 10000 ) / 100 );
        $gold = floor( $money / 10000 );
        return [$gold, $silver, $copper];
    }

}
