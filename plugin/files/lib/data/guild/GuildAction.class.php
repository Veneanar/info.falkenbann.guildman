<?php
namespace wcf\data\guild;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\wow\character\WowCharacter;
use wcf\data\wow\character\WowCharacterAction;
use wcf\data\wow\character\WowCharacterList;
use wcf\system\WCF;
use wcf\system\wow\bnetAPI;

/**
 * Executes Gildenbewerbung-related actions.
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */

class GuildAction extends AbstractDatabaseObjectAction {
	/**
	 * {@inheritDoc}
	 */
	public static $baseClass = GuildEditor::class;
	/**
	 * {@inheritDoc}
	 */
	protected $permissionsUpdate = array('admin.gman.canChangeGuild');
	/**
	 * {@inheritDoc}
	 */
	protected $permissionsCreate = array();
	/**
	 * {@inheritDoc}
	 */
	protected $permissionsDelete = array();
	/**
	 * {@inheritDoc}
	 */
	protected $requireACP = array('updateGuild');
	/**
	 * {@inheritDoc}
	 */
	protected $allowGuestAccess = array();

    /**
     * While there is no need for an ajax object, we read it manually
     */
    protected function validate() {
        $this->setObjects([new Guild()]);
    }

    public function validateUpdateGuild() {
        $this->validate();
        parent::validateUpdate();
    }

    public function updateGuild() {
        bnetAPI::updateGuild();
        if (isset($this->parameters['updateType'])) {
            if ($this->parameters['updateType']=='member') bnetAPI::updateGuildMemberList();
            if ($this->parameters['updateType']=='gacms') bnetAPI::updateGuildMemberList();
        }
    }

    public function updateRanks() {
        $guildMember = new WowCharacterList();
        $guildMember->getConditionBuilder()->add("inGuild = 1");
        $guildMember->readObjects();
        $memberList = $guildMember->getObjects();

        /**
         * @var     $member     WowCharacter
         */
        foreach ($memberList as $member) {
            $objectAction = new WowCharacterAction([$member], 'changeRank', [
                    'rank' => $member->guildRank,
                    ]);
            $objectAction->executeAction;
       }
    }
}
