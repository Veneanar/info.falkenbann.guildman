/**
 * checks if a wow charcter exists
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */
define(['Core', 'WoltLabSuite/GMan/Ui/Validation/Input'], function (Core, UiValidInput) {
    "use strict";

    /**
	 * @param       {RealmElement}       element         input element
	 * @param       {CharElement}       element         input element
	 * @param       {Object=}       options         search options and settings
	 * @constructor
	 */
    function UiCharacterValidInput(element, realmElement, enableElement, options) { this.init(element, realmElement, enableElement, options); }
    Core.inherit(UiCharacterValidInput, UiValidInput, {
        init: function (element, realmElement, enableElement, options) {
            this._realmElement = realmElement;
            this._enableElement = enableElement;
            if (!(this._realmElement instanceof Element)) {
                throw new TypeError("Expected a valid DOM element.");
            }
            else if (this._realmElement.type !== 'select-one') {
                if (this._realmElement.type !== 'select-multiple') {
                    throw new Error('Expected an input[type="select"].');
                }
            }
            options = Core.extend({
                ajax: {
                    className: 'wcf\\data\\wow\\character\\WowCharacterAction',
                    parameters: {
                    }
                }
            }, options);
            UiCharacterValidInput._super.prototype.init.call(this, element, options);
        },
        _getParameters: function (value) {
            this._value = value;
            return {
                parameters: {
                    data: {
                        characterName: value,
                        realmSlug: this._realmElement.value,
                    }
                }
            };
        },
        _finish: function (status, message, objectID) {
            if (status == 'ok') {
                if (typeof this._enableElement != 'undefined') {
                    this._enableElement.disabled = '';
                    this._enableElement.setAttribute('data-charname', this._value);
                    this._enableElement.setAttribute('data-realmslug', this._realmElement.value);
                }

            }
            else {
                if (typeof this._enableElement != 'undefined') {
                    this._enableElement.disabled = 'disabled'
                }
            }
        },
    });
    return UiCharacterValidInput;
});
