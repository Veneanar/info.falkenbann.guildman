<?php
namespace wcf\data\wow\character;
use wcf\data\user\avatar\IUserAvatar;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Represents a default WoW Charackter Avatar
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */
class DefaultCharacterAvatar implements IUserAvatar {
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
     * constructs the default Avatar
     *
     *  @var IUserAvatar
     */
    public function __construct($type = "avatar") {
        $this->type = $type;
	}

	/**
     * @inheritDoc
     */
	public function getURL($size = null) {
		return WCF::getPath().'images/wow/avatars/0.0-'.$this->type.'.jpg';
	}

	/**
     * @inheritDoc
     */
	public function getImageTag($size = null) {
		if ($size === null) $size = $this->size;

		return '<img src="'.StringUtil::encodeHTML($this->getURL($size)).'" style="width: '.$this->getWidth().'px; height: '.$this->getHeight().'px" alt="" class="userAvatarImage">';
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

	/**
     * @inheritDoc
     */
	public function getCropImageTag($size = null) {
		return '';
	}
}
