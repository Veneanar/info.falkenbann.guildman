<?php
namespace wcf\data\wow\item;
use wcf\system\WCF;
use wcf\system\wow\bnetAPI;
use wcf\system\wow\bnetIcon;
use wcf\system\request\LinkHandler;
use wcf\system\request\IRouteController;
use wcf\util\JSON;
use wcf\data\wow\spell\WowSpell;
use wcf\data\DatabaseObjectDecorator;
use wcf\data\DatabaseObject;
use wcf\system\exception\SystemException;

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

class ViewableWowItem extends DatabaseObjectDecorator implements IRouteController{

    /**
     * @inheritDoc
     */
	protected static $baseClass = WowItem::class;

    public $context = '';

    /**
     * bonus IDs
     * @var integer[]
     */
    public $bonusList = [];

    /**
     * Itemstats HTML
     * @var string
     */
    public $statTag = '';

    /**
     * List of enchantable slots
     * @var integer[]
     */
    public $enchantableSlots = [11, 16, 2];

    /**
     * gem IDs
     * @var integer[]
     */
    public $gemIDs = [];

    /**
     * Gems in sockets
     * @var WowItem[]
     */
    public $gems = [];

    /**
     * enchant ID
     * @var integer
     */
    public $enchantID = 0;

    /**
     * Enchants
     * @var WowSpell
     */
    public $enchant = null;

    /**
     * setItem IDs
     * @var integer[]
     */
    protected $setIDs = [];

    /**
     * transmog ID
     * @var integer
     */
    public $transmogID = 0;

    /**
     * transmog item
     * @var WoWItem
     */
    public $transmog = null;

    /**
     * object data
     * @var	array
     */
	public $newdata = null;

    private static function isAvaible($objectID, $context, $bonusString) {
        $sql = "SELECT	COUNT(*)
			    FROM		wcf".WCF_N."_gman_wow_itembonus
			    WHERE		itemID = ?
                AND         context LIKE ?
                AND         bonus LIKE ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([$objectID, $context, $bonusString]);
		return $statement->fetchColumn();
    }

	/** @noinspection PhpMissingParentConstructorInspection */
	/**
     * Creates a new DatabaseObjectDecorator object.
     *
     * @param	DatabaseObject		$object
     * @throws	SystemException
     */
	public function __construct(DatabaseObject $object, $context = '', array $bonusList = [], array $gems = [], $enchant = 0, $transmog = 0, array $set = []) {
        parent::__construct($object);
        $row = [];
        if (!empty($context) || !empty($bonusList)) {
            $context = $this->checkContext($context) ? $context : '';
            $bonusString = implode('', $bonusList);
            if (!$this->isAvaible($object->getObjectID(), $context, $bonusString)) {
                bnetAPI::getItem($object->getObjectID(), $context, $bonusList);
            }
            $sql = "SELECT	*
			    FROM		wcf".WCF_N."_gman_wow_itembonus
			    WHERE		itemID = ?
                AND         context LIKE ?
                AND         bonus LIKE ?";
		    $statement = WCF::getDB()->prepareStatement($sql);
		    $statement->execute([$object->getObjectID(), $context, $bonusString]);
		    $row = $statement->fetchArray();
            if ($row === false) $row = [];
            // add Data
            if (isset($row['bnetData'])) {
                //echo "<br>ITEM return: <pre>"; var_dump($this->data); echo "</pre>"; die();
                $this->object->data = array_replace($this->object->data, json_decode($row['bnetData'], true));
            }
        }
        $this->bonusList = $bonusList;
        $this->gemIDs = $gems;
        if ($enchant > 0) {
            $this->enchantID = $enchant;
        }
        if ($transmog > 0) {
            $this->transmogID = $transmog;
        }
        $this->setIDs = $set;
    }

	/**
     * @inheritDoc
     */
    public function __get($name) {
        if (isset($this->newdata[$name])) {
            return $this->data[$name];
        }
        else {
            return parent::__get($name);
        }
    }
    /**
     * returns the item header for tooltip
     * @return array
     */
    public function getItemHeader() {
        return [$this->getItemBind(), $this->getMaxCount()];
    }

    /**
     * returns the price as html tag
     * @return string
     */
    public function getPriceTag() {
        $price = $this->getPrice();
        return '<span class="icon-gold">'.$price[0].'</span> <span class="icon-silver">'.$price[1].'</span><span class="icon-copper">'.$price[2].'</span>';
    }

    /**
     * returns the itme stats as <li> elemts
     * @return string
     */
    public function getStatsTag() {
        $retval ='';
        if (empty($this->statTag)) {
            $statarray = $this->bonusStats;
            array_walk($statarray, function(&$elem) {
                if ($elem['stat'] == 7) {
                    $elem['order'] = 0;
                    $elem['style'] = '';
                }
                else if ($elem['stat'] > 70 && $elem['stat'] < 75 ) {
                    $elem['order'] = 1;
                    $elem['style'] = '';
                }
                else if ($elem['stat'] == 64) {
                    $elem['order'] = 3;
                    $elem['noval'] = true;
                    $elem['style'] = 'class="color-tooltip-green"';
                }
                else {
                    $elem['order'] = 2;
                    $elem['style'] = 'class="color-tooltip-green"';
                }
            });
            usort($statarray, function($a, $b) {
                return $a['order'] - $b['order'];
            });

            if (isset($this->baseArmor) && $this->baseArmor > 0) {
                $retval .= '<li>	'.$this->baseArmor.' '.WCF::getLanguage()->get('wcf.global.gman.item.stat.armor').'</li>';
            }

            foreach($statarray as $stat) {
                $retval .= '<li id="stat-'.$stat['stat'].'" '.$stat['style'].'>';
                $retval .= isset($stat['noval']) ? '' : '+ '. $stat['amount'];
                $retval .= ' '.WCF::getLanguage()->get('wcf.global.gman.item.stat.'.$stat['stat']).'</li>';
            }
            $this->statTag = $retval;
        }
        return $retval;
    }

    /**
     * returns the item description as li element
     * @return string
     */
    public function getDescriptionTag() {
        return isset($this->nameDescription) ? '<li style="color:#'.$this->nameDescriptionColor.'">'. $this->nameDescription.'</li>' : '';
    }

    /**
     * return Item Socket
     * @param integer $index   if an item have more than one socket use $index, if omitted index is 0
     * @return WowItem
     */
    public function getGem($index =0) {
        if (empty($this->gems)) {
            foreach($this->gemIDs as $gemID) {
                $this->gems[] = new WowItem($gemID);
            }
        }
        return isset($this->gems[$index]) ? $this->gems[$index] : null;
    }

    /**
     * returns the comma sperated gemIDs as an string
     * @return string
     */
    public function getGemDataTag($url = false) {
        return $url ? implode(",", $this->gemIDs) : JSON::encode($this->gemIDs);
    }

    /**
     * returns the comma sperated bonusIDs as an string
     * @return string
     */
    public function getBonusDataTag($url = false) {
        return empty($this->bonusList) ? '' : $url ? implode(",", $this->bonusList) :  JSON::encode($this->bonusList);
    }

    /**
     * returns the comma sperated set itemIDs as an string
     * @return string
     */
    public function getSetDataTag($url = false) {
        return empty($this->setIDs) ? '' : $url ? implode(",", $this->setIDs) : JSON::encode($this->setIDs);
    }

    /**
     * returns sokcet count
     * @return int
     */
    public function getSocketCount() {
        return isset($this->socketInfo) ? count($this->socketInfo['sockets']) : 0;
    }

    /**
     * returns true if an item is enchantable
     * @return bool
     */
    public function isEnchanhtable() {
        return in_array($this->inventoryType, $this->enchantableSlots);
    }

    /**
     * returns true if the item is enchanted
     * @return bool
     */
    public function isEnchanted() {
        return $this->enchantID > 0 ? true : false;
    }
    /**
     * returns the enchantment
     * @return null|WowSpell
     */
    public function getEnchant() {
        if ($this->enchant===null) {
            if ($this->enchantID==0) return null;
            $this->enchant = WowSpell::getByEnchant($this->enchantID);
        }
        return $this->enchant;
    }
    /**
     * returns enchant as html tag
     * @param mixed $size
     * @return string
     */
    public function getEnchantmentTag($size = 18) {
        $buffy = $this->getEnchant();
        return $buffy ? ('<div class="box16"> '. $buffy->getIcon()->getIconTag(18) . $buffy->getNameTag() .'</div>') : '';
    }

    /**
     * returns true if the item have sockets
     * @return bool
     */
    public function hasSockets() {
        return isset($this->socketInfo) ? true : false;
    }

    /**
     * retruns sockets and gems in an array
     * @return array[]
     */
    public function getSockets() {
        $sockets = [];
        $typeID = 0;
        $max = count($this->socketInfo['sockets']) -1;
        //var_dump($this->socketInfo); die();
        for ($i=0;$i<=$max; $i++) {
            if (strcasecmp($this->socketInfo['sockets'][$i]['type'], 'Prismatic')) {
                $typeID = 7;
            }
            elseif (strcasecmp($this->socketInfo['sockets'][$i]['type'], 'Cogwheel')) {
                $typeID = 6;
            }
            elseif (strcasecmp($this->socketInfo['sockets'][$i]['type'], 'Meta')) {
                $typeID = 1;
            }
            elseif (strcasecmp($this->socketInfo['sockets'][$i]['type'], 'Red')) {
                $typeID = 2;
            }
            elseif (strcasecmp($this->socketInfo['sockets'][$i]['type'], 'Yellow')) {
                $typeID = 3;
            }
            elseif (strcasecmp($this->socketInfo['sockets'][$i]['type'], 'Blue')) {
                $typeID = 4;
            }
            elseif (strcasecmp($this->socketInfo['sockets'][$i]['type'], 'Hydraulic')) {
                $typeID = 5;
            }
            else {
                $typeID= 11;
            }
            $sockets[] = [
                'type' => $this->socketInfo['sockets'][$i]['type'],
                'typeID' => $typeID,
                'gem' => $this->getGem($i),
                ];
        }
        return $sockets;
    }

    /**
     * returns sockets as li element
     * @return string
     */
    public function getSocketTag() {
        return WCF::getTPL()->fetch('_socketList', 'wcf', ['socketList' => $this->getSockets()]);
    }

    /**
     * get sockte tag with small icon and short text
     * @return string
     */
    public function getSmallSocketTag() {
        return WCF::getTPL()->fetch('_socketList18', 'wcf', ['socketList' => $this->getSockets()]);
    }

    /**
     * @inheritDoc
     */
	public function getLink() {
		return LinkHandler::getInstance()->getLink('ArmoryItem', [
			'application'   => 'wcf',
			'object'        => $this,
		        ]);
	}

	/**
     * @inheritDoc
     */
	public function getTitle() {
		return $this->name;
	}

    /**
     * check if item is artifact
     * @return integer
     */
    public function isArtifact() {
        return 0;
    }

}