<?php
namespace wcf\data\guild\group\application\field;
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

class ViewableApplicationFieldAction extends ApplicationFieldAction {

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
	protected $requireACP = array('update', 'delete', 'save');

	/**
     * {@inheritDoc}
     */
	protected $allowGuestAccess = array();

    public function validateDelete() {
        parent::validateDelete();
        parent::readInteger('appID', true);
    }

    public function delete() {
        $sql = "DELETE FROM wcf".WCF_N."_gman_field_to_application
                WHERE
                    appID = ?
                AND
                    fieldID = ?;";
        WCF::getDB()->beginTransaction();
        $statement = WCF::getDB()->prepareStatement($sql);
        foreach ($this->objects as $object) {
            $statement->execute([
                $this->parameters['appID'],
                $object->fieldID,
            ]);
        }
        WCF::getDB()->commitTransaction();
    }

    public function validateSave() {
        parent::validateUpdate();
        parent::readInteger('appID', true);
        parent::readInteger('fieldPermission', true);
        parent::readInteger('fieldRequierd', true);
        parent::readInteger('fieldOrder', true);
    }

    public function save() {
        $object = parent::getSingleObject();
        $sql = "INSERT INTO wcf".WCF_N."_gman_field_to_application
                    (fieldID, appID, fieldRequierd, fieldOrder, fieldPermission)
                VALUES
                    (?,?,?,?,?);";
        WCF::getDB()->beginTransaction();
        $statement = WCF::getDB()->prepareStatement($sql);
        foreach ($this->objects as $object) {
            $statement->execute([
                $object->fieldID,
                $this->parameters['appID'],
                $this->parameters['fieldRequierd'],
                $this->parameters['fieldOrder'],
                $this->parameters['fieldPermission'],
                ]);
        }
        WCF::getDB()->commitTransaction();
    }
}