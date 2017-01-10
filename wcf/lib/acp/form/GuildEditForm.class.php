<?php
namespace wcf\acp\form;
use wcf\form\AbstractForm;
use wcf\data\guild\Guild;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\UserInputException;
use wcf\data\article\ArticleList;
use wcf\system\WCF;
use wcf\system\request\RouteHandler;
use wcf\system\wow\bnetAPI;
use wcf\system\exception\NamedUserException;

/**
 * ACP Gildenverwaltung
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */

class GuildEditForm extends AbstractForm {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.gman.guildedit';

	/**
	 * @inheritDoc
	 */
	public $neededPermissions = ['admin.gman.canChangeGuild'];

	/**
     * name of the template for the called page
     * @var	string
     */
	public $templateName = 'guildEdit';

    /**
	 * Guild object
	 * @var	Guild
	 */
    private $guild = null;

    /**
	 * Birtday
	 * @var	integer
	 */
    private $birthday = 0;

    /**
	 * Articel ID
	 * @var	integer
	 */
     private $articleID = 0;

    /**
	 * Logo Media ID 
     * @var	integer
	 */
     private $logoID = 0;

    /**
	 * Page ID
	 * @var	integer
	 */
     private $pageID = 0;


	/**
	 * @inheritDoc
	 */
	public function readParameters() {


		parent::readParameters();
        $this->guild = new Guild();
        if ($this->guild->name == null) {
            if (GMAN_MAIN_GUILDNAME == '') {
                throw new NamedUserException(WCF::getLanguage()->get('wcf.acp.notice.gman.noguild'));
            }
            if (GMAN_MAIN_HOMEREALM == '') {
                throw new NamedUserException(WCF::getLanguage()->get('wcf.acp.notice.gman.norealm'));
            }
            bnetAPI::updateRealms();
            bnetAPI::updateGuild();
            $this->guild = new Guild();
        }
	}

	/**
     * @inheritDoc
     */
    public function readFormParameters() {
		parent::readFormParameters();
            $this->guildLeader = isset($_POST['guildLeader']) ? $_POST['guildLeader'] : "";
            $this->comment = isset($_POST['admin_comment']) ? $_POST['admin_comment'] : "";
	}

    /**
	 * @inheritDoc
	 */
	public function validate() {
		parent::validate();

    }

	/**
	 * @inheritDoc
	 */
	public function save() {
		AbstractForm::save();
        $this->saved();
		WCF::getTPL()->assign('success', true);
	}



	/**
	 * @inheritDoc
	 */
	public function readData() {
		parent::readData();
	}

	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();

        $articleList = new ArticleList;
        $articleList->readObjects();

        WCF::getTPL()->assign([
			'action' => 'edit',
			'articles' => $articleList->getObjects(),
            'guild'     => $this->guild,
		]);

	}
}
