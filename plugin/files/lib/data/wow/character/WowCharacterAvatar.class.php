<?php
namespace wcf\data\wow\character;
use wcf\data\DatabaseObjectDecorator;
use wcf\data\DatabaseObject;
use wcf\data\user\avatar\IUserAvatar;
use wcf\util\StringUtil;
use wcf\system\WCF;

/**
 * Represents a WoW Charackter Avatar
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */
class WowCharacterAvatar extends DatabaseObjectDecorator implements IUserAvatar {
	/**
     * {@inheritDoc}
     */
	public static $baseClass = WowCharacter::class;

	/**
	 * avaible avatar types
	 * @var	string[]
	 */
	public static $avatarTypes= ["avatar", "inset"];

	/**
     * avatar type
     * @var	string
     */
	private $type= "avatar";

	/**
	 * Returns the physical location of this avatar.
	 *
     * @param	integer		$type
	 * @return	string
	 */
	public function getLocation($type = "avatar") {
		return WCF_DIR . 'images/wow/avatars/' . $this->getFilename($type);
	}

    /**
     * constructs the default Avatar
     *
     *  @param  DatabaseObject $object
     *  @param  string   $type  avatar oder inset
     *  @var IUserAvatar
     */
    public function __construct(DatabaseObject $object, $type = "avatar") {
        parent::__construct($object);
        $this->type = $type;
	}

    // https://render-eu.worldofwarcraft.com/character/forscherliga/106/48723562-inset.jpg?alt=/wow/static/images/2d/inset/9-0.jpg
	/**
	 * Returns the file name of this avatar.
	 *
     * @param	integer		$type
	 * @return	string
	 */
	public function getFilename() {
		return basename($this->thumbnail, "-avatar.jpg") ."-". $this->type . ".jpg";
	}

	/**
	 * @inheritDoc
	 */
	public function getURL($size = NULL) {
		return WCF::getPath() . 'images/wow/avatars/' . $this->getFilename();
	}

    public function getAlt() {
        return WCF::getPath() . 'images/wow/avatars/' . $this->race . $this->gender . "-". $this->type . ".jpg";
    }

	/**
	 * @inheritDoc
	 */
	public function getImageTag($size = null) {
        if ($size===null) {
            return '<img src="'.StringUtil::encodeHTML($this->getURL($this->type)).'" style="width: '.$this->getWidth().'px; height: '.$this->getHeight().'px" alt="unsized call" class="userAvatarImage">';
        } else {
            $sizeWidth = $this->type == "avatar" ? $size : round($size * 1.98, 1);
            return '<img src="'.StringUtil::encodeHTML($this->getURL($this->type)).'" style="width: '.$sizeWidth.'px; height: '.$size.'px" alt="sized call" class="userAvatarImage">';
          }
    }

	/**
     * @inheritDoc
     */
	public function getCropImageTag($size = null) {
		return '';
	}

	/**
     * @inheritDoc
     */
	public function getWidth() {
		return $this->type == "avatar" ? 84 : 230;
	}

	/**
     * @inheritDoc
     */
	public function getHeight() {
		return $this->type == "avatar" ? 84 : 116;
	}

	/**
     * @inheritDoc
     */
	public function canCrop() {
		return false;
	}

}
