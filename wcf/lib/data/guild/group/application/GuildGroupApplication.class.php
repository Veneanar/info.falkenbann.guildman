<?php
namespace wcf\data\guild\group\application;
use wcf\system\WCF;
use wcf\system\WCFACP;
use wcf\data\DatabaseObject;
use wcf\data\article\Article;
use wcf\system\request\LinkHandler;
use wcf\system\request\IRouteController;
use wcf\data\guild\group\GuildGroup;
use wcf\data\user\User;
use wcf\data\wow\character\WowCharacter;
use wcf\data\wow\character\WowCharacterList;
use wcf\system\bbcode\SimpleMessageParser;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\data\guild\group\application\field\ViewableApplicationField;
use wcf\data\guild\group\application\field\ViewableApplicationFieldList;


/**
 * Represents a Gildgroup
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 * @property integer		$appID			    PRIMARY KEY
 * @property string		    $appName			Apllication Name
 * @property string		    $appTitle			Apllication Titek
 * @property string		    $appDescription   	Beschreibungstext
 * @property integer		$appArticleID	    Artikel über die Bewerbung
 * @property integer		$appGroupID		    Gruppe für die Bewerbung
 * @property integer		$appForumID         Forum in der die Bewrbungen gepostet werden
 * @property integer		$requireUser        Der Bewerber muss eingelogt sein j/n
 * @property integer        $active             Die Bewerbung ist aktiv
 *
 */
class GuildGroupApplication extends DatabaseObject implements IRouteController {
	/**
     * {@inheritDoc}
     */
	protected static $databaseTableName = 'gman_application';
	/**
     * {@inheritDoc}
     */
	protected static $databaseTableIndexName = 'appID';

    /**
     * temporary application member object
     * @var GuildGroup
     */
    protected $groupObject = null;

    /**
     * application article
     * @var Article
     */
    protected $applicationArticle = null;

    /**
     * Fields
     * @var ViewableApplicationField[]
     */
    protected $fieldList = [];

    public static function getForGroup($groupID) {
        $sql = "SELECT	*
			    FROM		wcf".WCF_N."_gman_application
			    WHERE		appGroupID LIKE ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute([$groupID]);
		$row = $statement->fetchArray();
		if (!$row) $row = [];
		return new GuildGroupApplication(null, $row);
    }

    /**
     * get Application for User wcf1_gman_guild_group_application
     * @param User $user
     * @param Integer $grouID
     * @return null|GuildGroupApplication
     */
    public static function getApplicationFromUser(User $user, $groupID) {
        $userCharList = new WowCharacterList();
        $userCharList->getConditionBuilder()->add("userID = ?", [$user->getObjectID()]);
        $userCharList->readObjectIDs();

        $conditions = new PreparedStatementConditionBuilder();
        $conditions->add("groupID = ?", [$groupID]);
        $conditions->add("characterID IN (?)", [$userCharList->getObjectIDs()]);
        $sql = "SELECT	characterID, groupID
			FROM	wcf".WCF_N."_gman_guild_group_application
			".$conditions;
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute($conditions->getParameters());
        $row = $statement->fetchArray();
        return new GuildGroupApplication(null, $row, null);
    }

    /**
     * Summary of getArticle
     * @return null|Article
     */
    public function getArticle() {
        if ($this->appArticleID==0) return null;
        if ($this->applicationArticle===null) {
            $this->applicationArticle = new Article($this->appArticleID);
        }
        return $this->applicationArticle;
    }

    public function getGroup() {
        if ($this->guildGroup===null) {
            $this->guildGroup = new GuildGroup($this->groupID);
        }
        return $this->guildGroup;
    }
	/**
     * @inheritDoc
     */
	public function getTitle() {
		return $this->groupName;
	}

    public function getFields() {
        if (empty($this->fieldList)) {
            $fieldList = new ViewableApplicationFieldList();
            $this->fieldList = $fieldList->getApplicationFields($this->appID);
        }
        return $this->fieldList;
    }

    public function renderForm(array $assignedVariables) {
        $formTemplate = '';
        $tFields = $this->getFields();
        foreach ($tFields as $field) {
            $formTemplate .= $field->fieldTemplate;
        }
        return WCF::getTPL()->fetch($formTemplate, 'wcf', $assignedVariables);
    }
}