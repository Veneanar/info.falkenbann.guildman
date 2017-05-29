<?php
namespace wcf\data\guild\group\application\field;
use wcf\system\WCF;
use wcf\data\DatabaseObject;
use wcf\data\article\Article;



/**
 * Represents a Apllication Field
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 * @property integer		$fieldID			    PRIMARY KEY
 * @property string		    $fieldName			    Feld Name
 * @property string		    $fieldTitle			    Feld Titel
 * @property string		    $fieldDescription	   	Beschreibungstext
 * @property string		    $fieldTemplate	        Template für das Formular fieldRenderTemplate
 * @property string		    $fieldRenderTemplate	Template für die App Seite
 * @property integer		$fieldType		        Typ
 * @property string		    $fieldRead              optional: Quellcode z. Auslesen
 * @property string		    $fieldValidation        optional: Quellcode z. Validieren
 * @property string		    $fieldRender	        optional: Quellcode z. Rendern
 *
 */
class ApplicationField extends DatabaseObject {
	/**
     * {@inheritDoc}
     */
	protected static $databaseTableName = 'gman_application_field';
	/**
     * {@inheritDoc}
     */
	protected static $databaseTableIndexName = 'fieldID';
    /**
     * Summary of $value
     * @var mixed
     */
    protected $value = null;

    public $orderNo = 0;

	/**
     * returns the field description
     * @return string
     */
	public function getDescription() {
        if (strpos($this->fieldTitle, '.page.') > 1) {
            return WCF::getLanguage()->get($this->fieldTitle);
        }
        else {
            return $this->fieldTitle;
        }
	}

	/**
     * returns the field title
     * @return string
     */
	public function getTitle() {
        if (strpos($this->fieldTitle, '.page.') > 1) {
            return WCF::getLanguage()->get($this->fieldTitle);
        }
        else {
            return $this->fieldTitle;
        }
	}

    public function setOrder($order) {
        $this->orderNo = $order;
    }
    public function getOrder() {
        return $this->orderNo;
    }
}