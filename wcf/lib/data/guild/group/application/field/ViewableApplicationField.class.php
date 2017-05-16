<?php
namespace wcf\data\guild\group\application\field;
use wcf\data\DatabaseObjectDecorator;
use wcf\data\DatabaseObject;
use wcf\system\WCF;
use wcf\system\exception\SystemException;


/**
 * Represents a Apllication Field
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 * @property integer		$fieldID			    PRIMARY KEY
 * @property string		    $fieldName			    Apllication Name
 * @property string		    $fieldTitle			    Apllication Titel
 * @property string		    $fieldDescription	   	Beschreibungstext
 * @property string		    $fieldTemplate	        Artikel über die Bewerbung
 * @property integer		$fieldType		        Gruppe für die Bewerbung
 * @property string		    $fieldRead              Forum in der die Bewrbungen gepostet werden
 * @property string		    $fieldValidation        Der Bewerber muss eingelogt sein j/n
 * @property string		    $fieldRender	        Artikel über die Bewerbung
 * @property integer		$fieldRequierd		    Gruppe für die Bewerbung
 * @property integer		$fieldOrder	            Gruppe für die Bewerbung
 *
 */
class ViewableApplicationField extends DatabaseObjectDecorator {
    /**
     * @inheritDoc
     */
	protected static $baseClass = ApplicationField::class;

	/** @noinspection PhpMissingParentConstructorInspection */
	/**
     * Creates a new DatabaseObjectDecorator object.
     *
     * @param	DatabaseObject		$object
     * @throws  SystemException
     */
	public function __construct(DatabaseObject $object, $appID = 0) {
        parent::__construct($object);
        if ($appID > 0) {
            $sql = "SELECT	fieldRequierd, fieldOrder, fieldPermission
			    FROM		wcf".WCF_N."_gman_field_to_application
			    WHERE		fieldID = ?
                AND         appID = ?";
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute([$object->getObjectID(), $appID]);
            $row = $statement->fetchArray();
            if ($row === false) throw new SystemException('Base application not specified');
            if (isset($row['bnetData'])) {
                $this->object->data = array_replace($this->object->data, $row);
            }
        }
    }
}