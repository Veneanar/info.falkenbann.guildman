/**
 * Provides uodate function for a character.
 * 
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 * @module	WoltLabSuite/GMan/Ui/WowTooltip
 */
define(['Ajax', 'Core', 'Dom/Util'], function (Ajax, Core, DomUtil) {
    "use strict";
    /**
	 * @param       {Element}       elements        button array
	 * @param       {Object}        options         options and settings
	 * @constructor
	 */
    function UpdateData(elements, options) { this.init(elements, options); }
    UpdateData.prototype = {
        /**
		 * Initializes the buttons
		 *  
		 * @param       {Element}       elements        button array
		 * @param       {Object}        options         options and settings
		 */
        init: function (elements, options) {
            this._elements = elements;
            for (var i = 0, length = this._elements.length; i < length; i++) {
                this._elements[i].addEventListener('click', this._click.bind(this));
            }
            this._options = Core.extend({
                ajax: {
                    actionName: 'updateData',
                    className: 'wcf\\data\\wow\\character\\WowCharacterAction'
                },
                noResultPlaceholder: '',
            }, options);

        },
        _click: function (event) {
            this._request = Ajax.api(this, this._getParameters(event));
        },
        _ajaxSuccess: function (data) {
            location.reload();
        },
        _ajaxSetup: function () {
            return {
                data: this._options.ajax,
            };
        },
        _getParameters: function (event) {
            this._activeElement = event.currentTarget;
            var charID = elData(this._activeElement, 'character-id');
            return {
                objectIDs: [charID],
                force: true
            };
        }
    };
    return UpdateData;
});
