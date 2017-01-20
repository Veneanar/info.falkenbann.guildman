<?php
namespace wcf\data\guild\group;
use wcf\data\wow\character\WowCharacterList;
use wcf\data\DatabaseObjectList;
/**
 * Represents a list of Gildenbewerbungs.
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */
class GuildGroupList extends DatabaseObjectList {
	/**
	 * {@inheritDoc}
	 */
	public static $baseClass = GuildGroup::class;
    public static function getMemberList($groupID) {
        $memberList = new WowCharacterList();
        $memberList->getConditionBuilder()->add("wcf".WCF_N."_gman_wow_chars.charID = charID");
        $memberList->getConditionBuilder()->add("wcf".WCF_N."_gman_char_to_group.groupID = ?", [$groupID]);
        $memberList->readObjects();
        return $memberList->getObjects();
    }
    public static function getMemberListRank($rank) {
        $memberList = new WowCharacterList();
        $memberList->getConditionBuilder()->add("gameRank = ?", [$rank]);
        $memberList->readObjects();
        return $memberList->getObjects();
    }
}
