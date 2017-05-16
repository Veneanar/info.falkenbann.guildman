<?php
namespace wcf\acp\form;
use wcf\form\AbstractForm;
use wcf\data\wow\character\WowCharacter;
use wcf\data\guild\Guild;
use wcf\data\guild\GuildAction;
use wcf\data\article\Article;
use wcf\data\page\PageList;
use wcf\data\page\Page;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\UserInputException;
use wcf\data\article\ArticleList;
use wcf\data\media\Media;
use wcf\data\media\ViewableMediaList;
use wcf\system\WCF;
use wcf\util\ArrayUtil;
use wcf\util\StringUtil;
use wcf\util\DateUtil;
use wcf\system\request\RouteHandler;
use wcf\system\wow\bnetUpdate;
use wcf\system\exception\NamedUserException;
use wcf\system\cache\runtime\GuildRuntimeChache;
use wcf\data\user\group\UserGroupList;
use wcf\data\user\group\UserGroup;

/**
 * ACP Gildenverwaltung
 * @author	    Veneanar Falkenbann
 * @copyright	2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	    GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	    info.falkenbann.guildman
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
     * Force start page
     * @var	boolean
     */
    private $forceStart = false;

	/**
     * publication date (ISO 8601)
     * @var	string
     */
	public $birthday = '';

	/**
     * group ID assigned to WCF group
     * @var	integer
     */
	public $wcfGroupID = 0;

	/**
     * group assigned to WCF group
     * @var	UserGroup;
     */
	public $groupWcf = null;
	/**
     * publication date object
     * @var	\DateTime
     */
	public $birthdayObj;

    /**
	 * Articel ID
	 * @var	integer
	 */
    private $articleID = 0;

    /**
	 * Logo Media ID
     * @var	integer[]
	 */
    private $imageID = [];

    /**
	 * Page ID
	 * @var	integer
	 */
    private $pageID = 0;
	/**
     * images
     * @var	Media[]
     */
    private $images = [];

	/**
	 * @inheritDoc
	 */
	public function readParameters() {
		parent::readParameters();
        $this->guild = GuildRuntimeChache::getInstance()->getCachedObject();
        if ($this->guild->name == null) {
            if (GMAN_MAIN_GUILDNAME == '') {
                throw new NamedUserException(WCF::getLanguage()->get('wcf.acp.notice.gman.noguild'));
            }
            if (GMAN_MAIN_HOMEREALM == '') {
                throw new NamedUserException(WCF::getLanguage()->get('wcf.acp.notice.gman.norealm'));
            }
            if (GMAN_BNET_KEY == '') {
                throw new NamedUserException(WCF::getLanguage()->get('wcf.acp.notice.gman.nokey'));
            }
            bnetUpdate::updateRealms();
            $this->forceStart = true;
        }
        $guildLeader = $this->guild->getLeader();
        if ($guildLeader === null) {
            throw new NamedUserException(WCF::getLanguage()->get('wcf.acp.notice.gman.nodata'));
        }
        $objectAction = new GuildAction([$this->guild], 'update' ,[
            'data' => [
                'leaderID' => $this->guild->leaderID,
                ]
            ]);
        $objectAction->executeAction();


	}

	/**
     * @inheritDoc
     */
    public function readFormParameters() {
		parent::readFormParameters();
            if (isset($_POST['birthdayDate'])) {
                $this->birthday = $_POST['birthdayDate'];
                $this->birthdayObj = \DateTime::createFromFormat('Y-m-d', $this->birthday);
            }
            if (WCF::getSession()->getPermission('admin.content.cms.canUseMedia')) {
                //echo "ja: <pre>"; var_dump($_POST['imageID']); echo "</pre>";
                if (isset($_POST['imageID']) && is_array($_POST['imageID'])) $this->imageID = ArrayUtil::toIntegerArray($_POST['imageID']);
                //echo "ja: <pre>"; var_dump($this->imageID); echo "</pre>";
                $this->readImages();
                //echo "after <pre>"; var_dump($this->imageID); echo "</pre>"; die;
            }
            if (isset($_POST['articleID'])) $this->articleID = intval($_POST['articleID']);
            if (isset($_POST['pageID'])) $this->pageID = intval($_POST['pageID']);
            if (isset($_POST['wcfGroupID']))  $this->wcfGroupID = intval($_POST['wcfGroupID']);

    }

	/**
     * Reads the box images.
     */
	protected function readImages() {
		if (!empty($this->imageID)) {
			$mediaList = new ViewableMediaList();
			$mediaList->setObjectIDs($this->imageID);
			$mediaList->readObjects();

			foreach ($this->imageID as $languageID => $imageID) {
				$image = $mediaList->search($imageID);
				if ($image !== null && $image->isImage) {
					$this->images[$languageID] = $image;
				}
			}
		}
	}

    /**
	 * @inheritDoc
	 */
	public function validate() {
		parent::validate();
		// article date
		if (empty($this->birthday)) {
			throw new UserInputException('birthdayDate');
		}
		if (!$this->birthdayObj) {
			throw new UserInputException('birthdayDate', 'invalid');
		}
        if ($this->articleID > 0) {
            $artcile = new Article($this->articleID);
            if ($artcile===null) {
                throw new UserInputException('articleID', 'notFound');
            }
        }
        if ($this->pageID > 0) {
            $page = new Page($this->pageID);
            if ($page===null) {
                throw new UserInputException('pageID', 'notFound');
            }
        }
		if ($this->wcfGroupID > 0) {
            $this->groupWcf = new UserGroup($this->wcfGroupID);
            if ($this->groupWcf->getObjectID()==0) throw new UserInputException('wcfGroupID');
		}
    }

	/**
	 * @inheritDoc
	 */
	public function save() {
		AbstractForm::save();
        $data = [
			'data' => [
				'articleID' => $this->articleID > 0 ? $this->articleID : null,
				'pageID'    => $this->pageID > 0 ? $this->pageID : null ,
				'birthday'  => $this->birthdayObj->getTimestamp(),
				'logoID'    => $this->imageID[0] > 0 ? $this->imageID[0]: null,
                'wcfGroupID'  => $this->wcfGroupID,
			]
		];
		$this->objectAction = new GuildAction([$this->guild], 'update', $data);
		$this->objectAction->executeAction();
        $this->saved();
		WCF::getTPL()->assign('success', true);
	}



	/**
	 * @inheritDoc
	 */
	public function readData() {
		parent::readData();
        if ($this->forceStart) return;
		if (empty($_POST)) {
			$this->articleID = $this->guild->articleID;
			$this->pageID = $this->guild->pageID;
			$this->imageID = [$this->guild->logoID];
			$dateTime = DateUtil::getDateTimeByTimestamp($this->guild->birthday);
			$dateTime->setTimezone(WCF::getUser()->getTimeZone());
			$this->birthday = $dateTime->format('c');
            $this->wcfGroupID = $this->guild->wcfGroupID;
            $this->readImages();
        }
	}





	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();
        if ($this->forceStart) {
            WCF::getTPL()->assign([
			    'action'    => 'new',
        ]);
        }
        else {
            $articleList = new ArticleList;
            $articleList->readObjects();
            $articles = $articleList->getObjects();

            $pageList = new PageList;
            $pageList->readObjects();
            $pages = $pageList->getObjects();
            //echo "<pre>"; var_dump($pages); echo "</pre>"; die;

            $wcfGroupList = new UserGroupList();
            $wcfGroupList->readObjects();
            $wcfGroups = $wcfGroupList->getObjects();

            WCF::getTPL()->assign([
			    'action'    => 'edit',
			    'articles'  => $articles,
                'pages'     => $pages,
			    'imageID'   => $this->imageID,
			    'images'    => $this->images,
                'guild'     => $this->guild,
                'birthday'  => $this->birthday,
                'articleID' => $this->articleID,
                'pageID'    =>  $this->pageID,
                'wcfGroupID'        => $this->wcfGroupID,
                'wcfGroups'         => $wcfGroups,
		    ]);
        }
	}
}
