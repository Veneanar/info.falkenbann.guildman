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
define(['Core', 'WoltLabSuite/Core/Ui/Search/Input'], function (Core, UiSearchInput) {
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
