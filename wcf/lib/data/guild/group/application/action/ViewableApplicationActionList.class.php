<?php
namespace wcf\data\guild\group\application\action;
use wcf\data\DatabaseObjectList;
/**
 * Represents a list of application fields.
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 * @property string		    $fieldName			    Apllication Name
 * @property string		    $fieldTitle			    Apllication Titel
 * @property string		    $fieldDescription	   	Beschreibungstext
 * @property string		    $fieldTemplate	        Artikel über die Bewerbung
 * @property integer		$fieldType		        Gruppe für die Bewerbung
 * @property string		    $fieldRead              Forum in der die Bewrbungen gepostet werden
 * @property string		    $fieldValidation        Der Bewerber muss eingelogt sein j/n
 * @property string		    $fieldRender	        Artikel über die Bewerbung
 * @property integer		$fieldRequierd		    Gruppe für die Bewerbung
 * @property integer		$fieldOrder	            Order
 * @property integer        $fieldPermission        kann lesen: Type 1: Everyone, Type 2: Group, Type 3: GroupLeader
 *
 */
class ViewableApplicationActionList extends DatabaseObjectList {
	/**
     * @inheritDoc
     */
	public $decoratorClassName = ViewableApplicationAction::class;
    /**
     * @inheritDoc
     */
	public $sqlOrderBy = 'gman_application_field.actionOrder';

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
