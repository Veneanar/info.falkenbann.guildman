<?php
namespace wcf\page;
use wcf\data\wow\character\WowCharacter;
use wcf\data\wow\character\slot\CharacterSlotList;
use wcf\data\guild\Guild;
use wcf\data\wow\character\WowCharacterList;
use wcf\system\exception\IllegalLinkException;
use wcf\page\AbstractPage;
use wcf\system\WCF;
use wcf\data\user\User;
use wcf\data\user\UserProfile;
use wcf\system\cache\runtime\GuildRuntimeChache;
use wcf\util\StringUtil;
use wcf\system\menu\user\profile\UserProfileMenu;
use wcf\data\guild\bosskill\CharBosskillList;

/**
 * Shows an armory
 *
 * @author	    Veneanar Falkenbann
 * @copyright	2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	    GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	    info.falkenbann.guildman
 *
 */
class ArmoryCharPage extends AbstractPage {
	/**
     * @inheritDoc
     */
	public $activeMenuItem = 'wcf.menu.link.gman.armoryview';

	/**
     * name of the template for the called page
     * @var	string
     */
	public $templateName = 'armoryChar';

    /**
     * wow char ID
     * @var integer
     */
    public $characterID = 0;
	/**
     * guild Object
     * @var	WoWCharacter
     */
    public $char = null;

	/**
     * owner Object
     * @var	UserProfile
     */
    public $user = null;

    /**
     * list of confirmed twinks
     * @var WoWCharacter[]
     */
    public $twinks = [];

	/**
     * needed modules to view this page
     * @var	string[]
     */
	public $neededModules = [];

	/**
     * needed permissions to view this page
     * @var	string[]
     */
	public $neededPermissions = [];

    /**
     * main guild
     * @var Guild
     */
    public $guild = null;




	/**
     * @inheritDoc
     */
	public function readParameters() {
		parent::readParameters();
        if (isset($_REQUEST['id'])) $this->characterID = intval($_REQUEST['id']);
        $this->char = new WowCharacter($this->characterID);
        if ($this->char->characterID == 0) {
            throw new IllegalLinkException();
        }
        $twinkList = new WowCharacterList();
        if ($this->char->userID > 0) {
            $this->user = new UserProfile($this->char->getOwner());
            $twinkList->getConditionBuilder()->add("userID = ? AND characterID <> ?", [$this->char->userID, $this->char->characterID]);
            $twinkList->sqlOrderBy ="c_level DESC";
        }
        else {
            //userID = 0 AND
            $twinkList->getConditionBuilder()->add("characterID <> ? AND accountID LIKE ? AND accountID <> ''", [$this->char->characterID, $this->char->accountID]);
            $twinkList->sqlOrderBy ="c_level DESC";
        }
        $twinkList->readObjects();
        $this->twinks = $twinkList->getObjects();



    }

	/**
     * @inheritDoc
     */
	public function readData() {
		parent::readData();
        $this->guild = GuildRuntimeChache::getInstance()->getCachedObject();
    }


	/**
     * @inheritDoc
     */
	public function assignVariables() {
		parent::assignVariables();
        // echo "<pre> Count: " . $this->char->getCharacterStatistics()->getMaincategories()[0]['name']; echo "</pre>"; die();
        //echo "outside: <pre>";var_dump($this->char->getEquip()->getItem('offHand')); echo "</pre>"; die();
        //echo $this->char->getEquip()->getItem('head')->getIcon()->getImageTag(36); die();
        $zone = [];
        foreach ($this->guild->getStatisticZoneIDs() as $zoneID) {
            $bosskillList = new CharBosskillList();
            $bosskillList->getConditionBuilder()->add("characterID = ?", [$this->characterID]);
            $bosskillList->getConditionBuilder()->add("zoneID = ?", [$zoneID]);
            $bosskillList->readObjects();
            $bosses = [];
            $bosseIDs = [];
            foreach ($bosskillList->getObjects() as $bosskill) {
                if (!in_array($bosskill->bossID, $bosseIDs)) {
                    $bosses[$bosskill->bossID] = [
                        'boss' => $bosskill->GetBoss(),
                        'modes' => [ [
                            'difficulty' => $bosskill->difficulty,
                            'killDate'   => $bosskill->killDate,
                            'quantity'   => $bosskill->quantity,
                            'icon'       => WCF::getPath()  . 'images/wow/difficulty_'. substr($bosskill->difficulty, strrpos($bosskill->difficulty, '.') +1) . ".png",
                            'lastupdate' => $bosskill->lastupdate,
                            ] ]
                        ];
                    $bosseIDs[] = $bosskill->bossID;
                }
                else {
                    $bosses[$bosskill->bossID]['modes'][] = [
                            'difficulty' => $bosskill->difficulty,
                            'killDate'   => $bosskill->killDate,
                            'quantity'   => $bosskill->quantity,
                            'icon'       => WCF::getPath() . 'images/wow/difficulty_'. substr($bosskill->difficulty, strrpos($bosskill->difficulty, '.') +1) . ".png",
                            'lastupdate' => $bosskill->lastupdate,
                            ];
                }
            }
            $zone[] = [
                'id' => $zoneID,
                'name' => WCF::getLanguage()->get('wcf.global.gman.zone.'. $zoneID),
                'bosses' => $bosses
                ];
        }
        $slotlist = new CharacterSlotList();
        $slotlist->readObjects();
        //WCF::getTPL()->append('specialStyles', '<link rel="stylesheet" type="text/css" href="'.RELATIVE_WCF_DIR.'style/chartist/chartist.css" />');
        WCF::getTPL()->assign([
            'guild' => $this->guild,
			'viewChar' => $this->char,
            'twinks' => $this->twinks,
            'user' => $this->user,
            'zoneList' => $zone,
            'slotList' => $slotlist->getObjects(),
		]);
	}
}
