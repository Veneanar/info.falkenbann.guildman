<?php
namespace wcf\data\wow\realm;
use wcf\system\WCF;
use wcf\util\JSON;
use wcf\data\DatabaseObject;

/**
 * Represents a WoW Realms
 * @author	Veneanar Falkenbann
 * @copyright	2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 * @property string		    $name			PRIMARY KEY
 * @property string		    $type			pvp, pve etc
 * @property string		    $population		low high
 * @property integer		$queue			ja/nein
 * @property integer		$status			-1: Unknown 0: Idle 1: Populating 2: Active 3: Concluded
 * @property string		    $battlegroup
 * @property string		    $timezone
 * @property array		    $connected_realms
 * @property string		    $slug
 * @property string		    $locale
 * @property integer		$isGuildRealm
 *
 */

class WowRealm extends DatabaseObject {

	/**
     * {@inheritDoc}
	 */
	protected static $databaseTableName = 'gman_wow_realm';

	/**
	 * {@inheritDoc}
	 */
	protected static $databaseTableIndexName = 'slug';

	/**
     * list of connected realms
     * @var	WowRealm[]
     */
    private $connectedRealms = null;

    /**
     * count of connected realms
     * @var	integer
     */
    private $realmCount = -1;

    /**
     * returns how many realms are connected
     * @return	integer
     */
    public function getConnetedRealmCount() {
        if ($this->realmCount < 0) {
            $this->connected_realms = JSON::decode($this->connected_realms, true);
            foreach ($this->connected_realms as $realmslug) {
                if ($this->slug != $realmslug) $this->connectedRealms[] = new WowRealm($realmslug);
            }
            $this->realmCount = count($this->connectedRealms);
        }
        return $this->realmCount;
    }

    /**
     * returns connected realm as array
     * @return	WowRealm[]
     */
    public function getConnectedRealms() {
        $this->getConnetedRealmCount();
        return $this->connectedRealms;
    }

    /**
     * get WOWRealm by slug
     * @return	WowRealm
     */
    public static function getByName($name) {
        $sql = "SELECT	*
			    FROM		wcf".WCF_N."_gman_wow_realm
			    WHERE		name LIKE ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([$name]);
		$row = $statement->fetchArray();
		if (!$row) $row = [];
		return new WowRealm(null, $row);
    }

}