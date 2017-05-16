<?php
namespace wcf\data\guild\tracking;
use wcf\data\DatabaseObject;
use wcf\system\WCF;
use wcf\data\wow\character\CharacterTrackedDataList;


/**
 * For future usage
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 * @property integer		$dataID		                PRIMARY KEY
 * @property string		    $trackingID			        tracking ID
 * @property string		    $dataClass			        Class: Guild, CharBosskill, Character
 * @property string		    $dataSource		            source
 * @property string		    $sourceType			        property or method?
 * @property string		    $sourceParam			    sourceParam / JSON array
 * @property string		    $dataType			        INTEGER / STRING / DATE / ARRAY
 * @property string         $cmt                         comment

 *
 */

class TrackingData extends DatabaseObject {

    protected $dataSource = null;
	/**
     * {@inheritDoc}
     */
	protected static $databaseTableName = 'gman_tracking_data';
	/**
     * {@inheritDoc}
     */
	protected static $databaseTableIndexName = 'dataID';

    public function getTitle() {
        WCF::getLanguage()->get($this->trackingTitle);
    }

    public function getDescription() {
        WCF::getLanguage()->get($this->trackingTitle);
    }

    static public function getDataIDList($trackingID) {
        $retval = [];
        $sql = "SELECT DISTINCT dataID FROM wcf".WCF_N."_gman_tracking_data WHERE trackingID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([$trackingID]);
        while ($row = $statement->fetchArray()) {
            $retval[] = $row["dataID"];
        }
        return $retval;
    }
    static public function getDataIDAndNameList($trackingID) {
        $retval = [];
        $sql = "SELECT DISTINCT dataID, dataName FROM wcf".WCF_N."_gman_tracking_data WHERE trackingID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([$trackingID]);
        while ($row = $statement->fetchArray()) {
            $retval[] = ['id' => $row["dataID"], 'name' => $row["dataName"]];
        }
        return $retval;
    }
}