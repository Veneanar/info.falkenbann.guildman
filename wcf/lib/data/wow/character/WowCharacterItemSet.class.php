<?php
namespace wcf\data\wow\character;
use wcf\data\DatabaseObject;
use wcf\data\wow\item\WowItem;
use wcf\data\wow\item\ViewableWowItem;
use wcf\data\wow\item\ViewableArtifact;
use wcf\util\JSON;
use wcf\system\WCF;

/**
 * Provides methods for WoW Charackter mit Items.
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */

class WowCharacterItemSet extends DatabaseObject {
	/**
     * {@inheritDoc}
     */
	protected static $databaseTableName = 'gman_character_equip';

	/**
     * {@inheritDoc}
     */
	protected static $databaseTableIndexName = 'characterID';

    /**
     * data store for json strings
     * @var WowItem[]
     */
    private $items = [];

    public function getItem($name) {
        if (!isset($this->items[$name])) {
            if (empty($this->data[$name])) {
                $this->items[$name] = new ViewableWowItem(new WowItem($name));
            }
            else {
                $t = JSON::decode($this->data[$name]);
                $enchant = 0;
                $transmog = 0;
                $bonus = isset($t['bonusLists']) ? $t['bonusLists'] : [];
                $context = isset($t['context']) ? $t['context'] : '';
                if (isset($t['tooltipParams']['enchant'])) $enchant = $t['tooltipParams']['enchant'];
                if (isset($t['tooltipParams']['transmogItem'])) $transmog = $t['tooltipParams']['transmogItem'];
                if (isset($t['artifactId']) && $t['artifactId'] > 0) {
                    $artifactTraits= isset($t['artifactTraits']) ? $t['artifactTraits'] : [];
                    $relics = isset($t['relics']) ? $t['relics'] : [];
                    $itemLevel = isset($t['itemLevel']) ? $t['itemLevel'] : 0;
                    //echo "<pre>"; var_dump($t); "</pre>"; die();
                    $this->items[$name] = new ViewableArtifact(new WowItem($t['id']), '', $bonus, $relics, $artifactTraits, $itemLevel, $enchant, $transmog, []);
                }
                else {
                    // Herausfinden warum die artefactwaffen nicht korrekt abgerufen werden!
                    $gems = [];
                    $set = [];
                    // max 3 sockets supportet
                    if (isset($t['tooltipParams']['gem0'])) $gems[] = $t['tooltipParams']['gem0'];
                    if (isset($t['tooltipParams']['gem1'])) $gems[] = $t['tooltipParams']['gem1'];
                    if (isset($t['tooltipParams']['gem2'])) $gems[] = $t['tooltipParams']['gem2'];
                    // check for setitems set
                    if (isset($t['tooltipParams']['set'])) $set = $t['tooltipParams']['set'];
                    $this->items[$name] = new ViewableWowItem(new WowItem($t['id']), $context, $bonus, $gems, $enchant, $transmog, $set);

                }
            }
        }
        return $this->items[$name];
    }

    /**
     * @inheritDoc
     */
    //public function __get($name) {
    //    if (isset($this->data[$name])) {
    //        if ($name != 'characterID' or $name != 'averageItemLevel' or $name != 'averageItemLevelEquipped') {
    //            if (!isset($this->items[$name])) {
    //                 $t = JSON::decode($this->data[$name]);
    //                //echo "intern: <pre>";var_dump($t); echo "</pre>";
    //                 $this->items[$name] = new WowItem($t['id'], isset($t['context']) ? $t['context'] : '' , isset($t['bonusLists']) ? $t['bonusLists'] : []);
    //                //echo "after: <pre>";var_dump($fuckyou); echo "</pre>";

    //            }
    //            return $this->items[$name];
    //        }
    //        return $this->data[$name];
    //    }
    //    else {
    //        return null;
    //    }
    //}

}