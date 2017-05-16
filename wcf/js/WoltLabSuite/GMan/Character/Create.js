/**
 * Provides uodate function for a character.
 * 
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 * @module	WoltLabSuite/GMan/Ui/WowTooltip
 */
define(['Ajax', 'Core', 'Language', 'Dom/Util', 'Ui/Dialog'], function (Ajax, Core, Language, DomUtil, UiDialog) {
    "use strict";
    /**
	 * @param       {Element}       elements        button array
	 * @param       {Object}        options         options and settings
	 * @constructor
	 */
    function CreateChar(elements, userAdd, options) { this.init(elements, userAdd, options); }
    CreateChar.prototype = {
        /**
		 * Initializes the buttons
		 *  
		 * @param       {Element}       elements        button array
		 * @param       {Object}        options         options and settings
		 */
        init: function (elements, userAdd, options) {
            this._userAdd = userAdd || 0;
            this._message = '';
            this._content = null;
            this._elements = elements;
            this._active = false;
            for (var i = 0, length = this._elements.length; i < length; i++) {
                this._elements[i].addEventListener('click', this._click.bind(this));
            }
            this._options = Core.extend({
                ajax: {
                    actionName: 'create',
                    className: 'wcf\\data\\wow\\character\\WowCharacterAction'
                },
                noResultPlaceholder: '',
            }, options);

        },
        _click: function (event) {
            this._request = Ajax.api(this, this._getParameters(event));
        },
        _ajaxSuccess: function (data) {
            if (data.returnValues.status) {
                this._message = data.returnValues.message;
            }
            else {
                this._message = Language.get("wcf.page.gman.dialog.addchar.failed");
            }
            this.show();
        },
        _ajaxSetup: function () {
            return {
                data: this._options.ajax,
            };
        },
        _ajaxFailure: function (data) {
            this._message = Language.get("wcf.page.gman.dialog.addchar.failed");
            this.show();
        },
        _getParameters: function (event) {
            this._activeElement = event.currentTarget;
            var charactername = elData(this._activeElement, 'charname');
            var realmslug = elData(this._activeElement, 'realmslug');
            return {
                parameters: {
                    data: {
                        characterName: charactername,
                        realmSlug: realmslug,
                        isAjax: true,
                        userAdd: this._userAdd,
                    }
                }
            };
        },

        show: function (options) {
            this._options = Core.extend({
                cancel: null,
            }, options);

            if (this._content === null) {
                this._createDialog();
            }
            this._content.innerHTML = this._message;
            UiDialog.open(this);
        },

        /**
		 * Creates the dialogue DOM elements.
		 */
        _createDialog: function () {
            var cancelButton = null;
            var dialog = null;
            var formSubmit = null;

            dialog = elCreate("div");
            elAttr(dialog, "id", "gmanCharadd");

            this._content = elCreate("div");
            elAttr(this._content, "id", "gmanCharaddContent");
            this._content.classList.add("section");
            dialog.appendChild(this._content);

            cancelButton = elCreate("button");
            cancelButton.textContent = Language.get("wcf.global.button.close");
            cancelButton.addEventListener(WCF_CLICK_EVENT, function () {
                UiDialog.close("gmanCharadd");
            });

            formSubmit = elCreate("div");
            formSubmit.classList.add("formSubmit");
            dialog.appendChild(formSubmit);

            formSubmit.appendChild(cancelButton);
            document.body.appendChild(dialog);
        },
        _dialogSetup: function () {
            return {
                id: "gmanCharadd",
                options: {
                    title: Language.get("wcf.page.gman.dialog.addchar.title")
                }
            };
        },
    };
    return CreateChar;
});
