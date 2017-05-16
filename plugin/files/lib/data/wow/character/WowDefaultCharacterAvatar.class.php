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
class WowDefaultCharacterAvatar implements IUserAvatar {
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
     * avatar race
     * @var	string
     */
    private $race = 0;

    /**
     * avatar gender
     * @var	string
     */
    private $gender = 0;

	/**
     * constructs the default Avatar
     *
     *  @param  string      $type   avatar oder inset
     *  @param  integer     $race   char's race
     *  @param  integer     $gender char's gender
     *  @var IUserAvatar
     */
    public function __construct($race = 0, $gender = 0, $type = "avatar") {
        $this->type = $type;
        $this->race = $race;
        $this->gender = $gender;
        //$this->size = $this->type == "avatar" ? 84 : 230;
	}
	/**
     * Returns the physical location of this avatar.
     *
     * @param	integer		$type
     * @return	string
     */
	public function getLocation($type = "avatar") {
		return WCF_DIR . 'images/wow/' . $this->race ."-". $this->gender . "-". $this->type . ".jpg";
	}
	/**
     * @inheritDoc
     */
	public function getURL($size = null) {
        return WCF::getPath() . 'images/wow/' . $this->race ."-". $this->gender . "-". $this->type . ".jpg";
	}

    public function getAlt() {
        return WCF::getPath().'images/wow/0-0-'.$this->type.'.jpg';
    }
	/**
     * @inheritDoc
     */
	public function getImageTag($size = null) {
        if ($size===null) {
            return '<img src="'.StringUtil::encodeHTML($this->getURL($size)).'" style="width: '.$this->getWidth().'px; height: '.$this->getHeight().'px" alt="unsized call" class="userAvatarImage">';
        } else {
            $sizeWidth = $this->type == "avatar" ? $size : round($size * 1.98, 1);
            return '<img src="'.StringUtil::encodeHTML($this->getURL($size)).'" style="width: '.$sizeWidth.'px; height: '.$size.'px" alt="sized call" class="userAvatarImage">';
        }

		// return '<img src="'.StringUtil::encodeHTML($this->getURL($size)).'" style="width: '.$this->getWidth().'px; height: '.$this->getHeight().'px" alt="" class="userAvatarImage">';
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
