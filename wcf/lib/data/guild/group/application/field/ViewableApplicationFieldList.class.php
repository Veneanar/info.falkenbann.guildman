<?php
namespace wcf\data\guild\group\application\field;
use wcf\data\DatabaseObjectList;
/**
 * Represents a list of application fields.
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */
class ViewableApplicationFieldList extends DatabaseObjectList {
	/**
     * @inheritDoc
     */
	public $decoratorClassName = ViewableApplicationField::class;
    /**
     * @inheritDoc
     */
	public $sqlOrderBy = 'field_to_application.fieldOrder';

    /**
	 * @inheritDoc
	 */
	public function __construct() {
		parent::__construct();
		if (!empty($this->sqlSelects)) $this->sqlSelects .= ',';
		$this->sqlSelects .= "field_to_application.*";
		$this->sqlJoins .= " LEFT JOIN wcf".WCF_N."_gman_field_to_application field_to_application ON (gman_application_field.fieldID = field_to_application.fieldID)";
    }

    public function getApplicationFields($appID) {
        $this->conditionBuilder()->add("WHERE field_to_application.appID = ?", [$appID]);
        $this->readObjects();
        return $this->getObjects();
    }

}
