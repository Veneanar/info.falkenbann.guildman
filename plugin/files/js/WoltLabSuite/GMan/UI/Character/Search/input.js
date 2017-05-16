/**
 * Provides suggestions for chars, optionally supporting groups.
 * 
 * checks if an input is valid 
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 * @see         module:WoltLabSuite/Core/Ui/Search/Input
 */
define(['Ajax', 'Core', 'Ui/SimpleDropdown', 'WoltLabSuite/Core/Ui/Search/Input'], function (Ajax, Core, UiSimpleDropdown, UiSearchInput) {
    "use strict";

    /**
	 * @param       {Element}       element         input element
	 * @param       {Object=}       options         search options and settings
	 * @constructor
	 */
    function UiCharacterSearchInput(element, options) { this.init(element, options); }
    Core.inherit(UiCharacterSearchInput, UiSearchInput, {
        init: function (element, options) {

            options = Core.extend({
                ajax: {
                    className: 'wcf\\data\\wow\\character\\WowCharacterAction',
                    parameters: {
                    }
                }
            }, options);

            UiCharacterSearchInput._super.prototype.init.call(this, element, options);
        },


        _search: function (value) {
            if (this._request) {
                this._request.abortPrevious();
            }
            var output = value.split(/[,]+/).pop();
            if (output.length > 2) {
                this._request = Ajax.api(this, this._getParameters(output.trim()));
            }
        },

        _setValue: function (value) {
            var commaIndex = this._element.value.lastIndexOf(",");
            if (commaIndex > 1) {
                this._element.value = this._element.value.substring(0, commaIndex) + ', ' + value;
            }
            else {
                this._element.value = value;
            }
        },
        /**
		 * Selects an item.
		 * 
		 * @param       {Element}       item    selected item
		 * @protected
		 */
        _selectItem: function (item) {
            if (this._options.callbackSelect && this._options.callbackSelect(item) === false) {
                this._setValue('');
            }
            else {
                this._setValue(elData(item, 'label'));
            }
            this._activeItem = null;
            UiSimpleDropdown.close(this._dropdownContainerId);
        },
        _createListItem: function (item) {
            var listItem = UiCharacterSearchInput._super.prototype._createListItem.call(this, item);
            elData(listItem, 'type', item.type);

            var box = elCreate('div');
            box.className = 'box16';
            box.innerHTML = item.icon;
            box.appendChild(listItem.children[0]);
            listItem.appendChild(box);
            return listItem;
        }
    });

    return UiCharacterSearchInput;
});
