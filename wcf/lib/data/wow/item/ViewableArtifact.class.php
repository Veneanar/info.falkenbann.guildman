<?php
namespace wcf\data\wow\item;
use wcf\data\wow\spell\WowSpell;
use wcf\data\wow\spell\ArtifactSpell;
use wcf\system\wow\bnetAPI;
use wcf\util\JSON;
use wcf\system\WCF;
use wcf\data\DatabaseObject;
/**
 * Represents an Artifact
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 */
class ViewableArtifact extends ViewableWowItem {

    /**
     * List of all trait spells & ranks
     * @var array
     */
    public $traitData = [];

    /**
     * List of trait
     * @var WoWSpell[]
     */
    public $traitList = [];

    /**
     * List of trait
     * @var array
     */
    public $orderedTraitList = [];

    /**
     * List of relic ids and bonusus
     * @var mixed
     */
    public $relicData = [];
    /**
     * List of relics
     * @var ViewableWowItem[]
     */

    public $relicList = [];

    public $requiredXP = [
        1=>100,
        2=>300,
        3=>325,
        4=>350,
        5=>375,
        6=>400,
        7=>425,
        8=>450,
        9=>525,
        10=>625,
        11=>750,
        12=>875,
        13=>1000,
        14=>6840,
        15=>8830,
        16=>11280,
        17=>14400,
        18=>18620,
        19=>24000,
        20=>30600,
        21=>39520,
        22=>50880,
        23=>64800,
        24=>82500,
        25=>105280,
        26=>138650,
        27=>182780,
        28=>240870,
        29=>315520,
        30=>417560,
        31=>546000,
        32=>718200,
        33=>946660,
        34=>1245840,
        35=>1635200,
        36=>1915000,
        37=>10000000,
        38=>13000000,
        39=>17000000,
        40=>22000000,
        41=>29000000,
        42=>38000000,
        43=>49000000,
        44=>64000000,
        45=>83000000,
        46=>108000000,
        47=>140000000,
        48=>182000000,
        49=>237000000,
        50=>308000000,
        51=>400000000,
        52=>520000000,
        53=>676000000,
        54=>880000000,
        55=>1144000000,
        56=>1488000000,
        57=>1936000000,
        58=>2516000000,
        59=>3272000000,
        60=>4252000000,
        61=>5528000000,
        62=>7188000000,
        63=>9344000000,
        64=>12148000000,
        65=>15792000000,
        66=>20528000000,
        67=>26688000000,
        68=>34696000000,
        69=>45104000000,
        70=>58636000000,
        71=>76228000000,
        72=>99096000000,
        73=>128824000000,
        74=>167472000000,
        75=>217712000000,
        76=>283024000000,
        77=>367932000000,
        78=>478312000000,
        79=>622000000000,
        80=>808000000000,
        81=>1050000000000,
        82=>1370000000000,
        83=>1780000000000,
        84=>2310000000000,
        85=>3000000000000,
        86=>3900000000000,
        87=>5070000000000,
        88=>6590000000000,
        89=>8570000000000,
        90=>11100000000000,
        91=>14500000000000,
        92=>18800000000000,
        93=>24500000000000,
        94=>31800000000000,
        95=>41400000000000,
        96=>53800000000000,
        97=>69900000000000,
        98=>90900000000000,
        99=>118000000000000,
        100=>154000000000000,
        101=>200000000000000,
        102=>260000000000000,
        103=>338000000000000,
        104=>439000000000000,
        105=>570000000000000,
        106=>742000000000000,
        107=>964000000000000,
        108=>1.25e+15,
        109=>1.63e+15,
        110=>2.12e+15,
        111=>2.75e+15,
        112=>3.58e+15,
        113=>4.65e+15,
        114=>6.05e+15,
        115=>7.86e+15,
        116=>1.02e+16,
        117=>1.33e+16,
        118=>1.73e+16,
        119=>2.25e+16,
        120=>2.92e+16,
        121=>3.8e+16,
        122=>4.93e+16,
        123=>6.41e+16
    ];

    /**
     * Summary of __construct
     * @param DatabaseObject $object
     * @param mixed $context
     * @param array $bonusList
     * @param array $relics
     * @param array $traits
     * @param mixed $itemlevel
     * @param mixed $enchant
     * @param mixed $transmog
     */
    public function __construct(DatabaseObject $object, $context = '', array $bonusList = [], array $relics = [], array $traits = [], $itemlevel, $enchant = 0, $transmog = 0) {
        parent::__construct($object);
        $row = [];
        $bonusString = implode('', $bonusList);

        if (!$this->isAvaible($object->getObjectID(), '', $bonusString)) {

            bnetAPI::getItem($object->getObjectID(), '', $bonusList);
        }
        $sql = "SELECT	*
		        FROM		wcf".WCF_N."_gman_wow_itembonus
			    WHERE		itemID = ?
                AND         bonus LIKE ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([$object->getObjectID(), $bonusString]);
		$row = $statement->fetchArray();
        if ($row === false) $row = [];
        if (isset($row['bnetData'])) {
            //echo "<br>ITEM return: <pre>"; var_dump($this->data); echo "</pre>"; die();
            $this->object->data = array_replace($this->object->data, json_decode($row['bnetData'], true));
        }
        $this->bonusList = $bonusList;
        if ($enchant > 0) {
            $this->enchantID = $enchant;
        }
        if ($transmog > 0) {
            $this->transmogID = $transmog;
        }
        $this->traitData = $traits;
        $this->relicData = $relics;
        $this->object->data['itemLevel'] = $itemlevel;
        $this->itemLevel = $itemlevel;
    }

    /**
     * return Item Socket
     * @param integer $index   if an item have more than one socket use $index, if omitted index is 0
     * @return WowItem
     */
    public function getGem($index =0) {
        if (empty($this->relicList)) {
            foreach($this->relicData as $data) {
                $this->relicList[] = new ViewableWowItem(new WowItem($data['itemId']), '', $data['bonusLists']);
            }
        }
        //echo "<pre>";  var_dump($this->relicList); echo "</pre>"; die();
        return isset($this->relicList[$index]) ? $this->relicList[$index] : null;
    }

    /**
     *  rerturns all relicIDs /gemIDs
     * @return mixed
     */
    public function getGemIDs() {
        $retval = [];
        foreach ($this->relicData as $data) {
            $retval[] = $data['itemId'] . "-" . implode('.', $data['bonusLists']);
        }
        return $retval;
    }

    /**
     * returns the comma sperated gemIDs as an string
     * @return string
     */
    public function getGemDataTag($url = false) {
        return $url ? implode(",", $this->getGemIDs()) : JSON::encode($this->getGemIDs());
    }

    /**
     * check if item is artifact
     * @return integer
     */
    public function isArtifact() {
        return 1;
    }

    /**
     * get sockte tag with small icon and short text
     * @return string
     */
    public function getSmallSocketTag() {
        // 'size' => 18
        return WCF::getTPL()->fetch('_relicList', 'wcf', ['socketList' => $this->getSockets(),'size' => 18]);
    }

    public function getTraits() {
        if (empty($this->traitList)) {
            foreach($this->traitData as $data) {
                $spellId = ArtifactSpell::getSpellFromArtifact($data['id']);
                if ($spellId==0) {
                    echo "<pre>";  var_dump($data['id']); echo "</pre>"; die();
                }
                //echo "id: " . $data['id'] ."/ rang: ". $data['rank'] . "/ spell: ".$spellId ." <br>";
                $this->traitList[] = new ArtifactSpell(new WowSpell($spellId), $data['rank']);
            }
        }
        return $this->traitList;
    }

    public function getOrderedTraits() {
        if (empty($this->orderedTraitList)) {
            $traitList = $this->getTraits();
            foreach($traitList as $trait) {
                $this->orderedTraitList[$trait->orderNo][] = $trait;
            }
        }
        return $this->orderedTraitList;
    }

    public function getTotalXPNeeded($level) {
        $totalXP = 0;
        for ($i=1;$i<=$level;$i++) {
            $totalXP += $this->requiredXP[$i];
        }
        return $totalXP;
    }

}