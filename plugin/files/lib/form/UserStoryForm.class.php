<?php
namespace wcf\form;
use wcf\data\wow\character\WowCharacter;
use wcf\data\wow\character\WowCharacterAction;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\UserInputException;
use wcf\system\exception\IllegalLinkException;
use wcf\system\html\input\HtmlInputProcessor;
use wcf\system\WCF;

/**
 * Shows the signature edit form.
 *
 * @author	Alexander Ebert
 * @copyright	2001-2017 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	WoltLabSuite\Core\Form
 */
class UserStoryForm extends MessageForm {
	/**
	 * @inheritDoc
	 */
	public $disallowedBBCodesPermission = 'user.signature.disallowedBBCodes';

	/**
	 * @inheritDoc
	 */
	public $loginRequired = true;

	/**
	 * @inheritDoc
	 */
	public $messageObjectType = 'com.woltlab.wcf.box.content';

	/**
	 * @inheritDoc
	 */
	public $showSignatureSetting = false;

	/**
	 * parsed signature cache
	 * @var	string
	 */
	public $charStory = '';

    /**
     * wow charObject
     * @var WowCharacter
     */
    public $charObject = null;

    /**
     * $chacracterID
     * @var integer
     */
    public $chacracterID = 0;

	/**
	 * @inheritDoc
	 */
	public $templateName = 'charStoryEdit';

	/**
     * @inheritDoc
     */
	public function readFormParameters() {
		parent::readFormParameters();
        if (isset($_REQUEST['id'])) $this->characterID = intval($_REQUEST['id']);
		$this->charObject = new WowCharacter($this->characterID);
        if ($this->charObject===null) {
			throw new IllegalLinkException();
		}
		if (isset($_POST['text']))   $this->text  = $_POST['text'];
	}

	/**
	 * @inheritDoc
	 */
	public function validate() {
		AbstractForm::validate();
		$this->validateText();
	}

	/**
	 * @inheritDoc
	 */
	public function readData() {
		parent::readData();
        if (isset($_REQUEST['id'])) $this->characterID = intval($_REQUEST['id']);
		$this->charObject = new WowCharacter($this->characterID);
        if ($this->charObject===null) {
			throw new IllegalLinkException();
		}
		// default values
		if (empty($_POST)) {
			$this->text = $this->charObject->getChartext();
		}

	}

	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();
		WCF::getTPL()->assign([
			'text'          => $this->text,
            'charObject'    => $this->charObject,
		]);
	}

	/**
	 * @inheritDoc
	 */
	public function save() {
		parent::save();
        $action = new WowCharacterAction([$this->charObject], 'update', [
            'data' => [
                'charText' => $this->htmlInputProcessor->getHtml(),
            ]]);
        $action->executeAction();

		$this->saved();
		// show success message
		WCF::getTPL()->assign('success', true);
	}
}
