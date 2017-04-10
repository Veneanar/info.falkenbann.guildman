/**
 * Provides suggestions for users, optionally supporting groups.
 * 
 * @author	Alexander Ebert
 * @copyright	2001-2016 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @module	WoltLabSuite/Core/Ui/User/Search/Input
 * @see         module:WoltLabSuite/Core/Ui/Search/Input
 */
define(['Core', 'WoltLabSuite/Core/Ui/Search/Input'], function (Core, UiSearchInput) {
    "use strict";

    /**
	 * @param       {Element}       element         input element
	 * @param       {Object=}       options         search options and settings
	 * @constructor
	 */
    function UiGuildGroupSearchInput(element, options) { this.init(element, options); }
    Core.inherit(UiGuildGroupSearchInput, UiSearchInput, {
        init: function (element, options) {

            options = Core.extend({
                ajax: {
                    className: 'wcf\\data\\guild\\group\\GuildGroupAction',
                    parameters: {
                    }
                }
            }, options);

            UiGuildGroupSearchInput._super.prototype.init.call(this, element, options);
        },

        _createListItem: function (item) {
            var listItem = UiGuildGroupSearchInput._super.prototype._createListItem.call(this, item);
            elData(listItem, 'type', item.type);

            var box = elCreate('div');
            box.className = 'box16';
            box.innerHTML = item.icon;
            box.appendChild(listItem.children[0]);
            listItem.appendChild(box);
            return listItem;
        }
    });

    return UiGuildGroupSearchInput;
});
