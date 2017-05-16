<?php
namespace wcf\data\wow\spell;
use wcf\data\DatabaseObjectDecorator;
use wcf\data\DatabaseObject;
use wcf\util\JSON;
use wcf\util\StringUtil;
use wcf\system\WCF;

/**
 * Represents an artifact spell
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */
class ArtifactSpell extends DatabaseObjectDecorator {
	/**
     * {@inheritDoc}
     */
	public static $baseClass = WowSpell::class;

    /**
     * contains override data for description
     * @var array
     */
    public $replaceArray = [];

	/**
	 * Create an artifact spell
	 * @param DatabaseObject $object
	 * @param integer $rank
	 */
	public function __construct(DatabaseObject $object, $rank = 0) {
		parent::__construct($object);
		$sql = "SELECT	*
				FROM		wcf".WCF_N."_gman_wow_artifacttraits
				WHERE		spellID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([$this->getObjectID()]);
		$row = $statement->fetchArray();
        // enforce data type 'array'
		if ($row === false) $row = [];
        $this->object->data = array_merge($this->object->data, $row);
        $this->rank = $rank;
        $this->replaceArray = JSON::decode($row['overrideData']);
    }
    /**
     * Get description based on rank
     * @return string
     */
    public function getDescription() {
        $returnValue = '';
        if (!empty($this->replaceArray)) {
            StringUtil::replaceIgnoreCase(StringUtil::formatNumeric($this->overridePattern), StringUtil::formatNumeric($this->replaceArray[$this->rank]), $this->description);
        }
        else {
            $returnValue = $this->description;
        }
        return $returnValue;
    }

    public static function getSpellFromArtifact($artifactID, $rank = 0) {
		$sql = "SELECT	spellID, artifactRank
				FROM	wcf".WCF_N."_gman_wow_artifacttraits
				WHERE	artifactID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([$artifactID]);
        $retVal = [];
        while ($row = $statement->fetchArray()) {
            $retVal[$row["artifactRank"]] =  $row["spellID"];
        }
        if (empty($retVal)) return 0;
        return isset($retVal[$rank]) ? $retVal[$rank] : $retVal[0];
    }
}