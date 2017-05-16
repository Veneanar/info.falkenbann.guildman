/**
 * returns a wow loadable tabmenu
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */
define(['Ajax', 'Core', 'Dictionary', 'EventHandler', 'WoltLabSuite/Core/Ui/TabMenu/Simple'], function (Ajax, Core, Dictionary, EventHandler, TabMenuSimple) {
    "use strict";
    /**
	 * @param       {Element}       element         button array
	 * @param       {Object}        options         options and settings
	 * @constructor
	 */
    function TabMenuLoadable(container, options) { this.init(container, options); }
    Core.inherit(TabMenuLoadable, TabMenuSimple, {
        init: function (container, options) {
            this._container = container;
            this._containers = new Dictionary();
            this._isLegacy = null;
            this._store = null;
            this._tabs = new Dictionary();
            this._options = Core.extend({
                ajax: {
                    actionName: 'getTabContent',
                    interfaceName: 'wcf\\data\\ITabContentAction',
                },
            }, options);
            if (TabMenuLoadable._super.prototype.validate.call(this)) {
                TabMenuLoadable._super.prototype.init.call(this);
            }
        },
        select: function(name, tab, disableEvent) {
            TabMenuLoadable._super.prototype.select.call(this, name, tab, disableEvent);
            var activeContentName = elData(this.getActiveTab(), 'name');
            console.log("acn: " + activeContentName)
            var activeContentElement = this._containers.get(activeContentName)
            activeContentElement.classList.add('active');
            activeContentElement.classList.remove('hidden');
            if (activeContentElement.innerHTML === "") {
                this._request = Ajax.api(this, this._getParameters(activeContentName, elData(this._container, 'objectID')));
            }
        },
        _getParameters: function (contentname, objectid) {
            return {
                objectIDs: [objectid],
                parameters: {
                    data: {
                        contentName: contentname,
                    }
                }
            };
        },
        _ajaxSuccess: function (data) {
            if (data.returnValues.status) {
                var contentElement = this._containers.get(data.returnValues.contentName);
                contentElement.innerHTML = data.returnValues.template;
            }
        },
        _ajaxSetup: function() {
            return {
                data: this._options.ajax,
            };
        },
        /**
		 * Selects the first tab containing an element with class `formError`.
		 */
        _selectErroneousTabs: function () {
            _tabMenus.forEach(function (tabMenu) {
                var foundError = false;
                tabMenu.getContainers().forEach(function (container) {
                    if (!foundError && elByClass('formError', container).length) {
                        foundError = true;

                        tabMenu.select(container.id);
                    }
                });
            });
        }
    });
    return TabMenuLoadable;
});