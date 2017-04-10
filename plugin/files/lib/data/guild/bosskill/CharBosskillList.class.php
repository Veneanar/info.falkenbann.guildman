<?php
namespace wcf\data\guild\bosskill;
use wcf\data\DatabaseObjectList;
use wcf\system\WCF;

/**
 * Represents a bosskill list
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */

class CharBosskillList extends DatabaseObjectList {
	/**
     * {@inheritDoc}
     */
	public $baseClass = CharBosskill::class;

    // SELECT	gman_char_bosskills.* FROM	wcf1_gman_char_bosskills gman_char_bosskills WHERE WHERE charID = ?
	/**
     * @inheritDoc
     */
	public function __construct() {
		parent::__construct();
		// fetch content data wcf1_gman_bosskills
		$this->sqlSelects .= "gman_bosskills.*";
		$this->sqlJoins .= " LEFT JOIN wcf".WCF_N."_gman_bosskills gman_bosskills ON (gman_bosskills.statID = gman_char_bosskills.statID)";
        $this->sqlOrderBy = "gman_bosskills.zoneID ASC, gman_char_bosskills.statID ASC";
	}
}