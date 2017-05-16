<?php
namespace wcf\data\guild\tracking;
use wcf\data\DatabaseObject;
use wcf\system\WCF;
use wcf\util\JSON;
use wcf\data\wow\character\CharacterTrackedDataList;
use wcf\data\wow\character\WowCharacter;


/**
 * For future usage
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 * @property integer		$trackingID			        PRIMARY KEY
 * @property string		    $trackingName			    interne Bezeichnung
 * @property string		    $trackingTitle			    externe Bezeichnung
 * @property string		    $trackingDescription		Beschreibung
 * @property string		    $trackingTemplate			templateTemplate
 * @property string		    $trackingTab			    templateTab
 * @property string		    $trackingOrderNo			Reihenfolge

 *
 */

class Tracking extends DatabaseObject {

    /**
     * data render list
     * @var CharacterTrackedDataList[]
     */
    protected $dataRender = [];

    /**
     * tracking data list
     * @var TrackingDataList[]
     */
    protected $trackingData = [];
    /**
     * wow character
     * @var WoWCharacter
     */
    protected $character = null;
	/**
     * {@inheritDoc}
     */
	protected static $databaseTableName = 'gman_tracking';
	/**
     * {@inheritDoc}
     */
	protected static $databaseTableIndexName = 'trackingID';

    /**
     * get Tracking's title
     */
    public function getTitle() {
        WCF::getLanguage()->get($this->trackingTitle);
    }

    /**
     * get Tracking's description
     */
    public function getDescription() {
        WCF::getLanguage()->get($this->trackingDescription);
    }
    /**
     * get Tracking Data
     * @return TrackingData[]
     */
    public function getTrackingData() {
        if (empty($this->trackingData)) {
            $trackingData = new TrackingDataList();
            $trackingData->getConditionBuilder()->add('trackingID = ?', [$this->trackingID]);
            $trackingData->readObjects();
            $this->trackingData = $trackingData->getObjects();
        }
        return $this->trackingData;
    }

    private function returnValue($dataObject, $initObject) {
        $obj = $initObject;
        $methodArray = explode('->', $dataObject->dataSource);
        $i = 0;
        $len = count($methodArray);
        $dataValue = null;
        foreach ($methodArray as $method) {
            $param = JSON::decode($dataObject->sourceParam);
            $method = str_replace(array( '(', ')' ), '', $method);
            if ($i == $len - 1) {
                $dataValue = $dataObject->sourceType=='method' ? call_user_func_array([$obj, $method], $param['param'][$i]) : $obj->$method;
            }
            else {
                $obj = call_user_func_array([$obj, $method], $param['param'][$i]);
            }
            $i++;
        }
        return $dataValue;
    }


    public function collectData($charcter) {
        date_default_timezone_set('UTC');
        $resetTime = strtotime("last " .GMAN_BNET_FIRSTDAYOFWEEK) + (3600 * 3);
        $sql = "INSERT INTO wcf".WCF_N."_gman_character_tracked_statistics
                                            (characterID, dataID, dataIntegerValue, dataStringValue, dataTime)
                VALUES                      (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                                            dataIntegerValue = VALUES(dataIntegerValue),
                                            dataStringValue = VALUES(dataStringValue)";
        $statement = WCF::getDB()->prepareStatement($sql);
        WCF::getDB()->beginTransaction();
        $this->character = $charcter;
        $dataList = $this->getTrackingData();
        foreach ($dataList as $dataObject) {
            $dataValue = 0;
            if ($dataObject->dataClass=='Character') {
                $dataValue = $this->returnValue($dataObject, $this->character);
            }
            else {
            }
            $statement->execute([
                $this->character->characterID,
                $dataObject->dataID,
                ($dataObject->dataType=="INTEGER" || $dataObject->dataType=="DATE") ? $dataValue : 0,
                ($dataObject->dataType=="ARRAY" || $dataObject->dataType=="STRING") ? $dataValue : '',
                $resetTime
            ]);
        }
        WCF::getDB()->commitTransaction();
    }

    /**
     * Summary of renderData
     * @return array[]
     */
    private function renderData() {
        $retval = [];
        //$dataList = $this->getTrackingData();
        $dataList = TrackingData::getDataIDList($this->trackingID);
        $dataAndNameList = TrackingData::getDataIDAndNameList($this->trackingID);
        $dateList = CharacterTrackedDataList::getDateList(reset($dataList));
        foreach ($dateList as $date) {
            $characterDataList = new CharacterTrackedDataList();
            $characterDataList->getConditionBuilder()->add('characterID = ? AND dataTime = ? AND dataID IN (?) ', [$this->character->characterID, $date, $dataList]);
            $characterDataList->sqlLimit = ceil(GMAN_BNET_TRACKINGDATA / 7);
            $characterDataList->readObjects();
            $worklist = $characterDataList->getObjects();
            foreach ($worklist as $dataObject) {
                foreach ($dataAndNameList as $dataAndName) {
                    if ($dataObject->dataID == $dataAndName['id']) {
                        $retval[$date][$dataAndName['name']] = $dataObject;
                    }
                }
            }
        }

        //echo "<pre>"; var_dump($retval); echo "</pre>"; die();

        //$characterDataList->
        //foreach ($characterDataList->getObjects() as $object) {
        //    $retval[$dateList[$i]] = $object;
        //    $i++;
        // }
                // $retval[$date] = $characterDataList->getObjects();
            //echo "<pre>"; var_dump($dataObject->getObjectID(), $this->character->characterID, $characterDataList->getObjects()); echo "</pre>";

        return $retval;
    }
    /**
     * Summary of renderTemplate
     * @param WowCharacter $charcter
     */
    public function renderTemplate($charcter) {
        $this->character = $charcter;
        return WCF::getTPL()->fetch($this->trackingTemplate, 'wcf', [$this->trackingName => $this->renderData(), 'viewChar' => $charcter]);
    }

}