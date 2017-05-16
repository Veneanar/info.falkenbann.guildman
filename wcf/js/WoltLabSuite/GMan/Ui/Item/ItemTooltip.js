/**
 * returns a wow tooltip
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */
define(['Core', 'Dom/ChangeListener', 'WoltLabSuite/GMan/Ui/WowTooltip'], function (Core, DomChangeListener, WowTooltip) {
    "use strict";

    /**
	 * @param       {RealmElement}       element         input element
	 * @param       {CharElement}       element         input element
	 * @param       {Object=}       options         search options and settings
	 * @constructor
	 */
    function ItemTooltip(tooltipClass, options) { this.init(tooltipClass, options); }
    Core.inherit(ItemTooltip, WowTooltip, {

        setup: function () {
            DomChangeListener.add('WoltLabSuite/GMan/Ui/Item/ItemTooltip', this.init.bind(this));
            window.addEventListener('scroll', this._mouseLeave.bind(this));
            ItemTooltip._super.prototype.setup.call(this);
        },
        init: function (tooltipClass, options) {
            if (!(this._tooltip)) this.setup();
            //console.log("Elements found:" + elements.length + "Searched for: " + tooltipClass)
            //console.log(JSON.stringify(elements));
            options = Core.extend({
                ajax: {
                    className: 'wcf\\data\\wow\\item\\ViewableWowItemAction',
                    parameters: {
                    }
                }
            }, options);
            ItemTooltip._super.prototype.init.call(this, tooltipClass, options);
        },
        _getParameters: function (event) {
            this._activeElement = event.currentTarget;
            var id = elData(this._activeElement, 'itemid');
            var context = elData(this._activeElement, 'context');
            var bonus = JSON.parse(elData(this._activeElement, 'bonus'));
            var gems = JSON.parse(elData(this._activeElement, 'gemlist'));
            var set = JSON.parse( elData(this._activeElement, 'setlist'));
            var enchant = elData(this._activeElement, 'enchant');
            var transmog = elData(this._activeElement, 'transmog');
            var isartifact = elData(this._activeElement, 'isartifact');
            var itemlevel = elData(this._activeElement, 'itemlevel');
            return {
                parameters: {
                    data: {
                        itemID: id,
                        itemContext: context,
                        itemBonuslist: bonus,
                        itemEnchant: enchant,
                        itemGems: gems,
                        itemSet: set,
                        itemTransmog: transmog,
                        isArtifact: isartifact,
                        itemLevel: itemlevel

                    }
                }
            };
        },
    });

    return ItemTooltip;
});
