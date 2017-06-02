<?php
namespace wcf\data\guild\group\application;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\system\cache\runtime\GuildRuntimeChache;
use wcf\system\WCF;

/**
 * Executes Gildenbewerbung-related actions.
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */

class GuildGroupApplicationAction extends AbstractDatabaseObjectAction {
	/**
	 * {@inheritDoc}
	 */
	public static $baseClass = GuildGroupApplicationEditor::class;
	/**
	 * {@inheritDoc}
	 */
	protected $permissionsUpdate = array('admin.gman.canEditGroups');
	/**
	 * {@inheritDoc}
	 */
	protected $permissionsCreate = array('admin.gman.canAddGroups');
	/**
	 * {@inheritDoc}
	 */
	protected $permissionsDelete = array('admin.gman.canDeleteGroups');
	/**
	 * {@inheritDoc}
	 */
	protected $requireACP = array('update', 'delete', 'removeField', 'removeAction');
	/**
	 * {@inheritDoc}
	 */
	protected $allowGuestAccess = array();

    /**
     * Validation of field delete
     */
    public function validateRemoveField() {
        try {
            parent::validateUpdate();
        }
        catch (UserInputException $exception) {
            $this->parameters['fake'] = true;
        }
        $this->readInteger('removeID', false);
    }

    /**
     * remove one field via AJAX
     */
    public function removeField() {
        if (!isset($this->parameters['fake'])) {
            $object = $this->getSingleObject();
            $sql = "DELETE FROM wcf".WCF_N."_gman_field_to_application
                WHERE appID = ?
                AND fieldID = ?";
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute([$object->getObjectID(), $this->parameters['removeID']]);
        }
    }

    /**
     * remove all Fields from an Application
     */
    public function removeFields() {
        $object = $this->getSingleObject();
        $sql = "DELETE FROM wcf".WCF_N."_gman_field_to_application
                WHERE appID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([$object->getObjectID()]);
    }

    /**
     * remvoe all Actions from an Application
     */
    public function removeActions() {
        $object = $this->getSingleObject();
        $sql = "DELETE FROM wcf".WCF_N."_gman_action_to_application
                WHERE appID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([$object->getObjectID()]);
    }

    /**
     * Validation of Action delete
     */
    public function validateRemoveAction() {
        $this->validateRemoveField;
    }

    /**
     * deletes one Action via AJAX
     */
    public function removeAction() {
        if (!isset($this->parameters['fake'])) {
                $object = $this->getSingleObject();
                $sql = "DELETE FROM wcf".WCF_N."_gman_action_to_application
                WHERE appID = ?
                AND actionID = ?";
                $statement = WCF::getDB()->prepareStatement($sql);
                $statement->execute([$object->getObjectID(), $this->parameters['removeID']]);
        }
    }

    /**
     * Insert or updates an array of fields
     */
    public function upsertFields() {
        $object = $this->getSingleObject();
        $sql = "INSERT INTO wcf".WCF_N."_gman_field_to_application
                        (fieldID, appID, fieldRequierd, fieldOrder, fieldPermission)
                VALUES  (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    fieldRequierd = VALUES(fieldRequierd),
                    fieldOrder = VALUES(fieldOrder),
                    fieldPermission = VALUES(fieldPermission)
                ";
        WCF::getDB()->beginTransaction();
        $statement = WCF::getDB()->prepareStatement($sql);
        foreach ($this->parameters['fields'] as $field) {
                $statement->execute([
                    $field['fieldID'],
                    $object->getObjectID(),
                    $field['fieldRequierd'],
                    $field['fieldOrder'],
                    $field['fieldPermission'],
                ]);
            }
        WCF::getDB()->commitTransaction();
    }

    /**
     * insert or updates an array of actions
     */
    public function upsertActions() {
        $object = $this->getSingleObject();
        $sql = "INSERT INTO wcf".WCF_N."_gman_action_to_application
                        (actionID, appID, actionVariable, actionTrigger)
                VALUES  (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    actionVariable = VALUES(actionVariable),
                    actionTrigger = VALUES(actionTrigger)
                ";
        WCF::getDB()->beginTransaction();
        $statement = WCF::getDB()->prepareStatement($sql);
        foreach ($this->parameters['actions'] as $field) {
            $statement->execute([
                $field['actionID'],
                $object->getObjectID(),
                $field['actionTrigger'],
                $field['actionVariable'],
            ]);
        }
        WCF::getDB()->commitTransaction();
    }
}