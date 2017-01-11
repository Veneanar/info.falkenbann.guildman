<?php
namespace wcf\data\guild;
use wcf\data\JSONExtendedDatabaseObject;
use wcf\data\DatabaseObject;
use wcf\data\wow\realm\WowRealm;
use wcf\data\wow\character\WowCharacter;
use wcf\data\media\Media;
use wcf\data\media\ViewableMedia;

/**
 * Represents a Gildenbewerbung
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 * @property
 * @property integer		        $guildID			            PRIMARY KEY
 * @property integer		        $articleID			            Article ID
 * @property integer		        $pageID			                Umfrage ID
 * @property integer		        $leaderID			            Character ID
 * @property integer		        $birthday			            Gildengeburtstag
 * @property integer		        $logoID			                Media ID des Logos
 * @property string		            $bnetData
 * @property integer		        $bnetUpdate
 * @property integer		        $guildRank
 * @property-read	integer			$lastModified					Letztes Update der Gildeninformationen
 * @property-read	string			$name							Name der Gilde
 * @property-read	string			$realm							Name des Servers wo die Gilde beheimatet ist
 * @property-read	string			$battlegroup					Servergruppe des Servers
 * @property-read	integer			$level							Level der Gilde
 * @property-read	integer			$side							Fraktion die der Gilde angehört
 * @property-read	integer			$achievementPoints				Erfolgspunkte der Gilde
 * @property-read	array			$emblem							Gildenlogo
 *
 *
 */

class Guild extends JSONExtendedDatabaseObject {
	/**
	 * {@inheritDoc}
	 */
	protected static $databaseTableName = 'gman_guild';
	/**
	 * {@inheritDoc}
	 */
	protected static $databaseTableIndexName = '';
	/**
     * {@inheritDoc}
     */
    protected static $JSONfield = 'bnetData';
    /**
     * The Guild Logo
     *
     * @var	Media
     */
    private $logo = null;

    /**
     * The guild's relam(s).
     *
     * @var WowRealm
     */
    private $homeRealm = null;

    /**
     * The guild leader.
     *
     * @var WowCharacter
     */
    private $leader = null;

    /**
     * Returns the Guildleader
     *
     * @return WowCharacter
     */
    public function getLeader() {
        if (!isset($this->leader)) {
            $this->leader = new WowCharacter($this->leaderID);
        }
        return $this->leader;
    }

    /**
     * Returns the guild's realm(s)
     *
     * @return WoWRealm
     */
    public function getRealm() {
        if (!isset($this->homeRealm)) {
            $this->homeRealm =  new WowRealm($this->realm);
        }
        return $this->homeRealm;
    }

    /**
     * Returns the guild's logo
     *
     * @return	Media
     */
    public function getLogo() {
        if (!isset($this->logo)) {
            $this->logo = new Media($this->logoID);
        }
        return $this->logo;
    }

	public function __construct() {
        $sql = "SELECT	*
				FROM	".static::getDatabaseTableName();
        $statement = \wcf\system\WCF::getDB()->prepareStatement($sql, 1);
        $statement->execute();
        $row = $statement->fetchSingleRow();
        if (!$row) return null;
        parent::__construct(null, $row, null);
	}

}