<?php
namespace wcf\data\guild\group\application\action;
use wcf\data\DatabaseObjectDecorator;
use wcf\data\DatabaseObject;
use wcf\system\WCF;
use wcf\system\exception\SystemException;


/**
 * Represents a Apllication Action
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 * @property integer		$actionID			    PRIMARY KEY
 * @property string		    $actionName			    action Name
 * @property string		    $actionTitle		    action Titel
 * @property string		    $actionDescription	   	Beschreibungstext
 * @property integer		$actionType		        Typ der aktion
 * @property string		    $actionWork             Aktion
 * @property integer		$actionOrder	        Typ der aktion
 * @property string		    $actionVariable         Variablen
 * @property integer		$actionTrigger	        Typ der aktion
 *
 */
class ViewableApplicationAction extends DatabaseObjectDecorator {
    /**
     * @inheritDoc
     */
	protected static $baseClass = ApplicationAction::class;

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