<?php
namespace wcf\system\cache\runtime;
use wcf\data\guild\Guild;
/**
 * Runtime cache implementation for the guild
 *
 * @author	    Veneanar Falkenbann
 * @copyright	2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	    GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	    info.falkenbann.guildman
 *
 * @method	Guild		getCachedObjects()
 * @method	Guild		getObject()
 * @method	Guild		getObjects()
 */
class GuildRuntimeChache extends AbstractRuntimeCache {
	/**
     * @inheritDoc
     */
	protected $listClassName = '';

	protected $object = null;

	/**
     * @inheritDoc
     */
	public function cacheObjectID($objectID = 1) {
		$this->cacheObjectIDs([]);
	}

	/**
     * @inheritDoc
     */
	public function cacheObjectIDs(array $objectIDs = []) {
        $this->fetchObjects();
	}

	/**
     * Fetches the objects for the pending object ids.
     */
	protected function fetchObjects() {
        if ($this->object===null) {
            $this->object = new Guild();
        }
	}

	/**
     * @inheritDoc
     */
	public function getCachedObjects() {
		return $this->getCachedObject();
	}

	/**
     * @inheritDoc
     */
	public function getCachedObject() {
        $this->fetchObjects();
		return $this->object;
	}

	/**
     * @inheritDoc
     */
	public function getObject($objectID = 1) {
        $this->object = new Guild();
		return $this->object;
	}

	/**
     * Returns a database object list object to fetch cached objects.
     *
     * @return	DatabaseObjectList|null
     */
	protected function getObjectList() {
		return null;
	}

	/**
     * @inheritDoc
     */
	public function getObjects(array $objectIDs = []) {
		return $this->getObject;
	}

	/**
     * @inheritDoc
     */
	public function removeObject($objectID = 1) {
		unset($this->object);
	}

	/**
     * @inheritDoc
     */
	public function removeObjects(array $objectIDs = []) {
        $this->removeObject();
	}

}