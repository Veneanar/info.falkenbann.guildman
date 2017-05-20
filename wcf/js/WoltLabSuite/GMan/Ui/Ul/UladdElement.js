/**
 * Provides uodate function for a character.
 * 
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 * @module	WoltLabSuite/GMan/Ui/WowTooltip
 */
define(['Ajax', 'Core'], function (Ajax, Core) {
    "use strict";
    /**
	 * @param       {Element}       elements        button array
	 * @param       {Object}        options         options and settings
	 * @constructor
	 */
    function UladdElement(eventElement, sourceElement, listElement, options) { this.init(eventElement, sourceElement, listElement, options); }
    UladdElement.prototype = {
        /**
		 * Initializes the buttons
		 *  
		 * @param       {Element}       elements        button array
		 * @param       {Object}        options         options and settings
		 */
        init: function (eventElement, sourceElement, listElement, options) {
            this._listElement= listElement;
            this._sourceElement = sourceElement;
            this._eventElement = eventElement;
            this._eventElement.addEventListener('click', this._click.bind(this));
            console.log("Gebunden")
            this._options = Core.extend({
                ajax: {
                    actionName: 'getLiElement',
                    className: '',
                },
                noResultPlaceholder: '',
            }, options);

        },
        _ajaxSetup: function () {
            return {
                data: this._options.ajax,
            };
        },
        _click: function (event) {
            this._request = Ajax.api(this, this._getParameters(event));
        },
        _ajaxSuccess: function (data) {
            var li = document.createElement("li");
            li.innerHTML = data.returnValues;
            li.className = "liMoveable";
            this._listElement.appendChild(li);
        },
        _getParameters: function (event) {
            var licount = this._listElement.childNodes.length;
            var objectID = this._sourceElement.options[this._sourceElement.selectedIndex].value
            return {
                objectIDs: [objectID],
                parameters: {
                    position: licount,
                }
            };
        },
    };
    return UladdElement;
});
