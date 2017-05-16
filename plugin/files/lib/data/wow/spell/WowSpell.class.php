<?php
namespace wcf\data\wow\spell;
use wcf\system\WCF;
use wcf\system\request\LinkHandler;
use wcf\system\request\IRouteController;
use wcf\data\DatabaseObject;
use wcf\data\JSONExtendedDatabaseObject;

/**
 * Represents a WoW Spell
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman artifactID, spellRank
 *
 * @property        integer		    $itemID			                PRIMARY KEY
 * @property        string		    $bnetData
 * @property-read	integer			$spellID						spell ID
 * @property-read	integer			$enchantID			            enchantID
 * @property-read	integer			$enchantID			            spellRank
 * @property-read	string			$spellName					    Name des Zaubers
 * @property-read	string			$name   					    Name des Zaubers
 * @property-read	integer			$bnetUpdate						Zeitpunkt der Aktualisierung
 * @property-read	string			$icon							Bild des Zaubers
 * @property-read	string			$description					Beschreibung des Zaubers
 * @property-read	string			$range							Reichweite des Zaubers
 * @property-read	string			$powerCost						Kosten des Zaubers
 * @property-read	string			$castTime						Zauberzeit
 * @property-read	string			$cooldown						Abklingzeit des Zaubers
 *
 *
 */
class WowSpell extends JSONExtendedDatabaseObject {

    /**
     * @inheritDoc
     */
	protected static $databaseTableName = 'gman_wow_spells';

    /**
     * @inheritDoc
     */
	protected static $databaseTableIndexName = 'spellID';

    /**
     * {@inheritDoc}
     */
    protected static $JSONfield = 'bnetData';

    /**
     * Icon of the Item
     * @var WowSpellIcon
     */
    private $iconimage = null;

    /**
     * artifact rank
     * @var integer
     */
    public $rank = 0;

    public $bonusRank = 0;

    const SMALL = 18;
    const MEDIUM = 36;
    const LARGE = 56;

    //public function __construct($id, array $row = null, DatabaseObject $object = null) {
    //    if ($id !== null) {
    //        $sql = "SELECT	*
    //            FROM	".static::getDatabaseTableName()."
    //            WHERE	".static::getDatabaseTableIndexName()." = ?";
    //        $statement = WCF::getDB()->prepareStatement($sql);
    //        $statement->execute([$id]);
    //        $row = $statement->fetchArray();

    //        // enforce data type 'array'
    //        if ($row === false) $row = [];
    //    }
    //    else if ($object !== null) {
    //        $row = $object->data;
    //    }

    //    if (isset($row[static::$JSONfield])) {
    //        if (empty($row[static::$JSONfield])) {
    //            $sql = "SELECT	".static::$JSONfield."
    //                FROM	".static::getDatabaseTableName()."
    //                WHERE	".static::getDatabaseTableIndexName()." = ?";
    //            $statement = WCF::getDB()->prepareStatement($sql);
    //            $statement->execute([$id]);
    //            $row = $statement->fetchArray();
    //        }
    //        $bnetData = json_decode($row[static::$JSONfield], true);
    //        $row[static::$JSONfield] = '';
    //        $row = array_merge($bnetData, $row);
    //    }
    //    $this->handleData($row);

    //}

    /**
     * get the Spell via the enchant ID
     * @param integer $id
     * @return null|WowSpell
     */
    static public function getByEnchant($id) {
        $sql = "SELECT	*
				FROM	".static::getDatabaseTableName()."
				WHERE	enchantID = ?
                LIMIT 1";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([$id]);
        $row = $statement->fetchArray();
        if (!$row) return null;
        return new WowSpell(null, $row);
    }

    /**
     * Returns the user's inset.
     *
     * @return	\wcf\data\user\avatar\IUserAvatar
     */
	public function getIcon() {
        if ($this->iconimage === null) {
            $this->iconimage = new WowSpellIcon($this);
        }
        return $this->iconimage;
    }

    /**
     * Get Name
     * @return string
     */
    public function getName() {
        return $this->spellName;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getSimpleTooltip() {
        return $this->getDescription();
    }

    /**
     * Get tag with optinal tooltip
     *
     * @param boolean $tooltip  renders tooltip. if omitted tooltip is true
     * @param boolean $simple   renders a simple tooltip. if omitted tooltip is true
     *
     * @return string
     */
    public function getNameTag($tooltip = true, $simple = true) {
        return '<span class="">'. $this->name.'</span>';
    }

    /**
     * @inheritDoc
     */
	public function getLink() {
		return LinkHandler::getInstance()->getLink('ArmorySpell', [
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
}