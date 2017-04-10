<?php
namespace wcf\data\wow\boss;
use wcf\data\DatabaseObjectDecorator;
use wcf\data\DatabaseObject;
use wcf\data\user\avatar\IUserAvatar;
use wcf\util\StringUtil;
use wcf\system\WCF;

/**
 * Represents a WoW Icon
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */
class WowBossIcon extends DatabaseObjectDecorator implements IUserAvatar {
	/**
     * {@inheritDoc}
     */
	public static $baseClass = WowBoss::class;

    /**
     * icon size
     * @var mixed
     */
    public $size = 128;

    /**
     * saved URls
     * @var array
     */
    public $urls = [];

    public $SAVE_ICONS_LOCAL = true;


    /**
     * returns the physical filen name
     * @return string
     */
    public function getFilename() {
        return 'ui-ej-boss-'. $this->urlSlug.'.png';
    }

    /**
     * Returns the physical location of this icon.
     *
     * @param	integer		$type
     * @return	string
     */
	public function getLocation() {
        return WCF_DIR . 'images/wow/encounter/'.$this->size.'/'.$this->getFilename();
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
	public function getURL($size=null) {
        return WCF::getPath() . 'images/wow/encounter/'.$this->getFilename();
    }


	/**
     * @inheritDoc
     */
	public function getImageTag($size=null) {
        if (!is_null($size) && $this->size != $size) $this->size= $size;
        return '<img src="'.StringUtil::encodeHTML($this->getURL($this->type)).'" style="width: '.$this->getWidth().'px; height: '.$this->getHeight().'px;" alt="'.$this->getTitle().'" class="'.$this->getIconClass(false).'">';
    }



    public function getIconTag($size=36, $boxed=false, $framed=false) {
        if (!is_null($size) && $this->size != $size) $this->size= $size;
        return '<img src="'.StringUtil::encodeHTML($this->getURL($this->type)).'" style="width: '.$this->getWidth().'px; height: '.$this->getHeight().'px;" alt="'.$this->getTitle().'" class="'.$this->getIconClass($framed).'">';
    }

    private function getIconClass($framed) {
        return 'item-icon';
    }

	/**
     * @inheritDoc
     */
	public function getCropImageTag($size=null) {
        return '';
    }

	/**
     * @inheritDoc
     */
	public function getWidth() {
        return 128;
    }

	/**
     * @inheritDoc
     */
	public function getHeight() {
        return 64;
    }

}