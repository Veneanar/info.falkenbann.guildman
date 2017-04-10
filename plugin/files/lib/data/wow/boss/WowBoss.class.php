<?php
namespace wcf\data\wow\boss;
use wcf\system\WCF;
use wcf\data\JSONExtendedDatabaseObject;
use wcf\system\request\LinkHandler;
use wcf\system\request\IRouteController;

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
class WowBoss extends JSONExtendedDatabaseObject implements IRouteController {

    /**
     * @inheritDoc
     */
	protected static $databaseTableName = 'gman_wow_boss';

    /**
     * @inheritDoc
     */
	protected static $databaseTableIndexName = 'bossID';

    /**
     * {@inheritDoc}
     */
    protected static $JSONfield = 'bnetData';
    /**
     * Icon of the Item
     * @var WowBossIcon
     */
    private $iconimage = null;

    /**
     * Get Name
     * @return string
     */
    public function getName() {
        return $this->bossName;
    }
    /**
     * @inheritDoc
     */
	public function getLink() {
		return LinkHandler::getInstance()->getLink('ArmoryBoss', [
			'application'   => 'wcf',
			'object'        => $this,
		        ]);
	}
    /**
     * Returns the boss's icon.
     *
     * @return	\wcf\data\user\avatar\IUserAvatar
     */
	public function getIcon() {
        if ($this->iconimage === null) {
            $this->iconimage = new WowBossIcon($this);
        }
        return $this->iconimage;
    }
    /**
     * returns a simple tooltip
     * @return string
     */
    public function getSimpleTooltip() {
        return $this->description;
    }
	/**
     * @inheritDoc
     */
	public function getTitle() {
		return $this->bossName;
	}
}