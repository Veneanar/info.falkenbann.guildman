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
 * @package	info.falkenbann.guildman
 *
 * @property        integer		    $itemID			                PRIMARY KEY
 * @property        string		    $bnetData
 * @property-read	integer			$spellID						spell ID
 * @property-read	integer			$enchantID			            enchantID
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

    const SMALL = 18;
    const MEDIUM = 36;
    const LARGE = 56;

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

    public function getSimpleTooltip() {
        return $this->description;
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