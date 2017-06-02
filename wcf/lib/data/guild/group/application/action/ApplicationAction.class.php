<?php
namespace wcf\data\guild\group\application\action;
use wcf\system\WCF;
use wcf\data\DatabaseObject;
use wcf\data\article\Article;
use wcf\system\exception\UserInputException;
use wcf\system\html\input\HtmlInputProcessor;
use wcf\system\bbcode\BBCodeHandler;
use wcf\system\bbcode\SimpleMessageParser;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\util\ArrayUtil;
use wcf\util\StringUtil;
use wcf\util\MessageUtil;


/**
 * Represents a Apllication Action
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 * @property integer		$actionID			    PRIMARY KEY
 * @property string		    $actionName			    action Name
 * @property string		    $actionTitle		    action Titel
 * @property string		    $actionDescription	   	Beschreibungstext
 * @property integer		$actionType		        Typ der aktion
 * @property integer	    $hasVariable            hat eine Variable 0/1
 *
 *
 */
class ApplicationAction extends DatabaseObject {
	/**
     * {@inheritDoc}
     */
	protected static $databaseTableName = 'gman_application_action';
	/**
     * {@inheritDoc}
     */
	protected static $databaseTableIndexName = 'actionID';
    /**
     * application article
     * @var Article
     */
    protected $applicationArticle = null;
    /**
     * Summary of $value
     * @var mixed
     */
    protected $value = null;

    protected function sendNotifictaion($title, $text, array $reciever) {

    }

    protected function sendConversation($title, $text, array $reciever) {

    }


	/**
     * returns the field title
	 * @return string
	 */
	public function getTitle() {
        if (strpos($this->actionTitle, '.acp.') > 1) {
            return WCF::getLanguage()->get($this->actionTitle);
        }
        else {
            return $this->actionTitle;
        }
	}

	public function getDescription() {
        if (strpos($this->actionDescription, '.acp.') > 1) {
            return WCF::getLanguage()->get($this->actionDescription);
        }
        else {
            return $this->actionDescription;
        }
	}

}