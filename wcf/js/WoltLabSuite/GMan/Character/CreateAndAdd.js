/**
 * Provides uodate function for a character.
 * 
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 * @module	WoltLabSuite/GMan/Ui/WowTooltip
 */
define(['Ajax', 'Core', 'WoltLabSuite/GMan/Character/Create'], function (Ajax, Core, CreateChar) {
    "use strict";
    /**
	 * @param       {Element}       elements        button array
	 * @param       {Object}        options         options and settings
	 * @constructor
	 */
    function CreateCharAndAdd(elements, userAdd, listElement, appID, options) { this.init(elements, userAdd, listElement, appID, options); }
    Core.inherit(CreateCharAndAdd, CreateChar, {
        /**
		 * Initializes the buttons
		 *  
		 * @param       {Element}       elements        button array
		 * @param       {Object}        options         options and settings
		 */
        init: function (elements, userAdd, listElement, appID, options) {
            this._listElement = listElement;
            this._appID = appID;
            CreateCharAndAdd._super.prototype.init.call(this, elements, userAdd, options);
        },
        _ajaxSuccess: function (data) {
            if (data.returnValues.status) {
                this._message = data.returnValues.message;
                var li = document.createElement("li");
                li.innerHTML = data.returnValues.template;
                this._listElement.appendChild(li);
            }
            else {
                this._message = Language.get("wcf.page.gman.dialog.addchar.failed");
            }
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
                        appID: this._appID,
                        userAdd: this._userAdd,
                    }
                }
            };
        },
    });
    return CreateCharAndAdd;
});
