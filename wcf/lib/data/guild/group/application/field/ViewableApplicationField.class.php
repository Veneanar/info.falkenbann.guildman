<?php
namespace wcf\data\guild\group\application\field;
use wcf\data\DatabaseObjectDecorator;
use wcf\data\DatabaseObject;
use wcf\system\WCF;
use wcf\system\exception\SystemException;
use wcf\system\exception\UserInputException;
use wcf\system\html\input\HtmlInputProcessor;
use wcf\system\bbcode\BBCodeHandler;
use wcf\system\bbcode\SimpleMessageParser;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\data\wow\character\WowCharacter;
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
 * @property integer		$fieldID			    parent: Feld ID
 * @property string		    $fieldName			    parent: Feld Name
 * @property string		    $fieldTitle			    parent: Feld Titel
 * @property string		    $fieldDescription	   	parent: Beschreibungstext
 * @property string		    $fieldTemplate	        parent: Template für das Formular fieldRenderTemplate
 * @property string		    $fieldRenderTemplate	parent: Template für die App Seite
 * @property integer		$fieldType		        parent: Typ
 * @property string		    $fieldRead              parent: optional: Quellcode z. Auslesen
 * @property string		    $fieldValidation        parent: optional: Quellcode z. Validieren
 * @property string		    $fieldRender	        parent: optional: Quellcode z. Rendern
 * @property integer		$fieldRequierd		    ist das Feld nötig
 * @property integer		$fieldOrder	            Sortierreihenfolge
 *
 *
 */
class ViewableApplicationField extends DatabaseObjectDecorator {
    /**
     * @inheritDoc
     */
	protected static $baseClass = ApplicationField::class;

	/** @noinspection PhpMissingParentConstructorInspection */
	/**
     * Creates a new DatabaseObjectDecorator object.
     *
     * @param	DatabaseObject		$object
     * @throws  SystemException
     */
	public function __construct(DatabaseObject $object, $appID = 0) {
        parent::__construct($object);
        if ($appID > 0) {
            $sql = "SELECT	fieldRequierd, fieldOrder, fieldPermission
			    FROM		wcf".WCF_N."_gman_field_to_application
			    WHERE		fieldID = ?
                AND         appID = ?";
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute([$object->getObjectID(), $appID]);
            $row = $statement->fetchArray();
            if ($row === false) throw new SystemException('Base field not specified');
        }
    }

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
                    // date
                    case 2:
                        $this->value = StringUtil::trim($_REQUEST[$this->fieldName]);
                        break;
                    // text
                    case 3:
                        $this->value = StringUtil::trim(MessageUtil::stripCrap($_REQUEST[$this->fieldName]));
                        break;
                    // bnet
                    case 4:
                        $this->value = StringUtil::trim(MessageUtil::stripCrap($_REQUEST[$this->fieldName]));
                        break;
                    // role
                    case 5:
                        $this->value = intval($_REQUEST[$this->fieldName]);
                        break;
                    case 6:
                    // string
                        $this->value = StringUtil::trim(MessageUtil::stripCrap($_REQUEST[$this->fieldName]));
                        break;
                    case 7:
                    case 8:
                    case 9:
                    case 10:
                        // integer
                        $this->value = intval($_REQUEST[$this->fieldName]);
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
            throw new UserInputException($this->fieldName, 'empty');
        }
        if (!empty($this->fieldValidation)) {
            eval($this->fieldValidation);
        }
        else {
            switch ($this->fieldType) {
                // 0-1
                case 1:
                    if ($this->value != 1 && $this->fieldRequierd) {
                        throw new UserInputException($this->fieldName, 'noValidSelection');
                    }
                    if ($this->value > 1) $this->value = 1;
                    if ($this->value < 0) $this->value = 0;
                    break;
                // date
                case 2:
                    // if empty skip check
                    if (!empty($this->value)) {
                        if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $this->value, $match)) {
                            throw new UserInputException($this->fieldName, 'noValidSelection');
                        }
                        if (!checkdate(intval($match[2]), intval($match[3]), intval($match[1]))) {
                            throw new UserInputException($this->fieldName, 'noValidSelection');
                        }
                    }
                    else {
                        // set fallback value
                        $this->value = '0000-00-00';
                    }
                    break;
                // text
                case 3:
                    if (!empty($this->value)) $this->value = $this->validateText($this->fieldName, $this->value);
                    break;
                case 4:
                    // bnet
                    if (!empty($this->value)) {
                        if (!preg_match('/^\D.{2,11}#\d{4,6}$/', $this->value, $match)) {
                            throw new UserInputException($this->fieldName, 'noValidSelection');
                        }
                    }
                case 5:
                    // role
                    if (!empty($this->value)) {
                        if ($this->value < 1 or $this->value > 4) {
                            throw new UserInputException($this->fieldName, 'noValidSelection');
                        }
                    }
                case 8:
                    // friend
                    if (!empty($this->value)) {
                        $checkChar = new WowCharacter($this->value);
                        if ($checkChar->characterID==0) {
                            throw new UserInputException($this->fieldName, 'noValidSelection');
                        }
                    }
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

}