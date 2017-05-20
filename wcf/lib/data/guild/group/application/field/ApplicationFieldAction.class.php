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

class ApplicationFieldAction extends AbstractDatabaseObjectAction {
	/**
     * {@inheritDoc}
     */
	public static $baseClass = ApplicationFieldEditor::class;
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
	protected $requireACP = array('update', 'delete', 'getLiElement');
	/**
     * {@inheritDoc}
     */
	protected $allowGuestAccess = array();

    public function validateGetLiElement() {
        parent::validateUpdate();
        parent::readInteger('position', false);
    }

    public function getLiElement() {
       $object = parent::getSingleObject();
       $object->setOrder($this->parameters['position']);
       return WCF::getTPL()->fetch('_applicationField', 'wcf', ['field' => $object, 'noli' => 1]);
    }

}