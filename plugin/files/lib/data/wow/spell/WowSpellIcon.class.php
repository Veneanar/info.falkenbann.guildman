<?php
namespace wcf\data\wow\spell;
use wcf\data\DatabaseObjectDecorator;
use wcf\data\DatabaseObject;
use wcf\data\user\avatar\IUserAvatar;
use wcf\util\StringUtil;
use wcf\system\WCF;
use wcf\system\wow\bnetIcon;

/**
 * Represents a WoW Icon
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */
class WowSpellIcon extends DatabaseObjectDecorator implements IUserAvatar {
	/**
     * {@inheritDoc}
     */
	public static $baseClass = WowSpell::class;

    /**
     * icon size
     * @var mixed
     */
    public $size = 36;

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
        return $this->icon.'.jpg';
    }

    /**
     * Returns the physical location of this icon.
     *
     * @param	integer		$type
     * @return	string
     */
	public function getLocation() {
        return WCF_DIR . 'images/wow/'.$this->size.'/'.$this->getFilename();
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
        if (!is_null($size) && $this->size != $size) $this->size= $size;
        if (!isset($this->urls[$size])) {
            $this->urls[$size] = WCF::getPath() . 'images/wow/'.$this->size.'/'.$this->getFilename();
            if (!file_exists($this->getLocation())) {
                if ($this->SAVE_ICONS_LOCAL) {
                    if (!bnetIcon::download($this->getFilename(), [$this->size])) $this->urls[$size] = bnetIcon::buildURL($this->getFilename(), $this->size);
                }
                else {
                    $this->urls[$size] = bnetIcon::buildURL($this->getFilename(), $this->size);
                }
            }
        }
        return $this->urls[$size];
    }


	/**
     * @inheritDoc
     */
	public function getImageTag($size=null) {
        if (!is_null($size) && $this->size != $size) $this->size= $size;
        return '<img src="'.StringUtil::encodeHTML($this->getURL($this->type)).'" style="width: '.$this->getWidth().'px; height: '.$this->getHeight().'px;" alt="'.$this->getSimpleTooltip().'" class="'.$this->getIconClass(false).'">';
    }



    public function getIconTag($size=36, $boxed=false, $framed=false) {
        if (!is_null($size) && $this->size != $size) $this->size= $size;
        return '<img src="'.StringUtil::encodeHTML($this->getURL($this->type)).'" style="width: '.$this->getWidth().'px; height: '.$this->getHeight().'px;" alt="'.$this->getSimpleTooltip().'" class="'.$this->getIconClass($framed).'">';
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
        return $this->size;
    }

	/**
     * @inheritDoc
     */
	public function getHeight() {
        return $this->size;
    }

}