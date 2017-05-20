<?php
namespace wcf\data\guild\group\application\field;
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
 * Represents a Apllication Field
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 * @property integer		$fieldID			    PRIMARY KEY
 * @property string		    $fieldName			    Apllication Name
 * @property string		    $fieldTitle			    Apllication Titel
 * @property string		    $fieldDescription	   	Beschreibungstext
 * @property string		    $fieldTemplate	        Artikel über die Bewerbung
 * @property integer		$fieldType		        Gruppe für die Bewerbung
 * @property string		    $fieldRead              Forum in der die Bewrbungen gepostet werden
 * @property string		    $fieldValidation        Der Bewerber muss eingelogt sein j/n
 * @property string		    $fieldRender	        Artikel über die Bewerbung
 *
 */
class ApplicationField extends DatabaseObject {
	/**
     * {@inheritDoc}
     */
	protected static $databaseTableName = 'gman_application_field';
	/**
     * {@inheritDoc}
     */
	protected static $databaseTableIndexName = 'fieldID';
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

    public $orderNo = 0;

    /**
     * reads the field value
     */
    public function readVar() {
        if (isset($_REQUEST[$this->fieldName])) {
            // eigener Reader? dann ausführen und beenden.
            if (!empty($this->fieldRead)) {
                eval($this->fieldRead);
            }
            else {
                switch ($this->fieldType) {
                    // integer
                    case 1:
                        $this->value = intval($_REQUEST[$this->fieldName]);
                        break;
                    // string
                    case 2:
                        $this->value = StringUtil::trim(MessageUtil::stripCrap($_REQUEST[$this->fieldName]));
                        break;
                    // text
                    case 3:
                        $this->value = StringUtil::trim(MessageUtil::stripCrap($_REQUEST[$this->fieldName]));
                        break;
                }
            }
        }
    }

    /**
     * Validates the field value
     * @throws UserInputException
     */
    public function validate() {
        if ($this->fieldRequierd && empty($this->value)) {
            throw new UserInputException($this->fieldName);
        }
        if (!empty($this->fieldValidation)) {
            eval($this->fieldValidation);
        }
        else {
            switch ($this->fieldType) {
                // text
                case 3:
                    $this->value = $this->validateText($this->fieldName, $this->value);
                    break;
            }
        }
    }

    /**
     * Validates the message text.
     */
	protected function validateText($varname, $text, $maxTextLength = 15000, $disallowedBBCodesPermission = 'user.signature.disallowedBBCodes') {
		if (empty($this->messageObjectType)) {
			throw new \RuntimeException("Expected non-empty message object type for '".get_class($this)."'");
		}

		if (empty($text)) {
			throw new UserInputException($varname);
		}

		if ($this->disallowedBBCodesPermission) {
			BBCodeHandler::getInstance()->setDisallowedBBCodes(explode(',', WCF::getSession()->getPermission($disallowedBBCodesPermission)));
		}

		$this->htmlInputProcessor = new HtmlInputProcessor();
		$this->htmlInputProcessor->process($this->text, 'com.woltlab.wcf.box.content', 0);

		// check text length
		if ($this->htmlInputProcessor->appearsToBeEmpty()) {
			throw new UserInputException($varname);
		}
		$message = $this->htmlInputProcessor->getTextContent();
		if ($maxTextLength != 0 && mb_strlen($message) > $maxTextLength) {
			throw new UserInputException($varname, 'tooLong');
		}

		$disallowedBBCodes = $this->htmlInputProcessor->validate();
		if (!empty($disallowedBBCodes)) {
			WCF::getTPL()->assign('disallowedBBCodes', $disallowedBBCodes);
			throw new UserInputException($varname, 'disallowedBBCodes');
		}

        return $this->htmlInputProcessor->getHtml();
	}

	/**
     * returns the field description
     * @return string
     */
	public function getDescription() {
        if (strpos($this->fieldTitle, '.page.') > 1) {
            return WCF::getLanguage()->get($this->fieldTitle);
        }
        else {
            return $this->fieldTitle;
        }
	}

	/**
     * returns the field title
     * @return string
     */
	public function getTitle() {
        if (strpos($this->fieldTitle, '.page.') > 1) {
            return WCF::getLanguage()->get($this->fieldTitle);
        }
        else {
            return $this->fieldTitle;
        }
	}

    public function setOrder($order) {
        $this->orderNo = $order;
    }
    public function getOrder() {
        return $this->orderNo;
    }
}