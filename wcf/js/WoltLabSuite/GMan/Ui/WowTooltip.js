/**
 * Provides enhanced tooltips.
 * 
 * @author	Alexander Ebert
 * @copyright	2001-2017 WoltLab GmbH
 * @extended by Veneanar Falkenbann
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 * @module	WoltLabSuite/GMan/Ui/WowTooltip
 */
define(['Ajax', 'Core', 'Language', 'Environment', 'Ui/Alignment'], function (Ajax, Core, Language, Environment, UiAlignment) {
    "use strict";
    /**
	 * @param       {Elements}       elements       wow tooltip elements
	 * @param       {Object}        options         tooltip options and settings
	 * @constructor
	 */
    function WowTooltip(tooltipClass, options) { this.init(tooltipClass, options); }
    WowTooltip.prototype = {
        /**
		 * Initializes tooltip elements.
		 */

        setup: function () {
            if (Environment.platform() !== 'desktop') return;

            this._tooltip = elCreate('div');
            elAttr(this._tooltip, 'id', 'wowTooltip');
            this._tooltip.classList.add('balloonTooltip');
            this._tooltip.addEventListener('transitionend', function () {
                if (this._tooltip != null && !this._tooltip.classList.contains('active')) {
                    // reset back to the upper left corner, prevent it from staying outside
                    // the viewport if the body overflow was previously hidden
                    this._tooltip.style.removeProperty('top');
                    this._tooltip.style.removeProperty('left');
                }
            });
            this._text = elCreate('span');
            elAttr(this._text, 'id', 'wowTooltipText');
            this._tooltip.appendChild(this._text);
            document.body.appendChild(this._tooltip);
            this._elements = null;
        },
        init: function (tooltipClass, options) {
            this._elementClass = tooltipClass || this._elementClass;
            this._elements = elByClass(this._elementClass);
            if (this._elements.length == 0) return;
            var element;
            while (this._elements.length) {
                element = this._elements[0]; 
                element.classList.remove(this._elementClass);

                element.addEventListener('mouseenter', this._mouseEnter.bind(this));
                element.addEventListener('mouseleave', this._mouseLeave.bind(this));
                element.addEventListener(WCF_CLICK_EVENT, this._mouseLeave.bind(this));
            }
            this._options = Core.extend({
                ajax: {
                    actionName: 'getTooltip',
                    className: '',
                    interfaceName: 'wcf\\data\\ITooltipAction',
                },
                callbackDropdownInit: null,
                callbackSelect: null,
                delay: 50,
                noResultPlaceholder: '',
                preventSubmit: true
            }, options);
        },

        /**
		 * Displays the tooltip on mouse enter.
		 * 
		 * @param	{Event}         event	event object
		 */
        _mouseEnter: function (event) {
            this._request = Ajax.api(this, this._getParameters(event));
            this._tooltip.style.removeProperty('top');
            this._tooltip.style.removeProperty('left');
            this._tooltip.classList.add('active');
            UiAlignment.set(this._tooltip, this._activeElement, {
                horizontal: 'center',
                verticalOffset: 4,
                vertical: 'top',
                pointer: false
            });
        },

        /**
		 * Hides the tooltip once the mouse leaves the element.
		 */
        _mouseLeave: function () {
            this._tooltip.classList.remove('active');
        },

        _setTooltipText: function (text) {
            this._text.innerHTML = text
        },
        /**
         * Handles successful AJAX requests.
         * 
         * @param       {Object}        data    response data
         * @protected
         */
        _ajaxSuccess: function (data) {
            if (data.returnValues.success) {
                this._setTooltipText(data.returnValues.template)
            }
            else {
                this._setTooltipText(Language.get("wcf.global.gman.datafailed"));
            }
        },
		
        _ajaxBeforeSend: function() {
            setTimeout(function() {
                if (!!this._tooltip.innerHTML){
                    this._setTooltipTex(Language.get("wcf.global.gman.dataloading"));
                }
            }, 50);
        
        },
        /**
         * Handles failed AJAX requests.
         * 
         * @param       {Object}        data    response data
         * @protected
         */
        _ajaxFailure: function (data) {
            console.log(data)
            this._setTooltipText(Language.get("wcf.global.gman.serverfailed"));
        },
        /**
         * Handles an empty result set, return a boolean false to hide the dropdown.
         * 
         * @protected
         */
        _handleEmptyResult: function() {
            this._setTooltipText(Language.get("wcf.global.gman.serverfailed"));
        },
		
	
        _ajaxSetup: function() {
            return {
                data: this._options.ajax,
                silent: true
            };
        }
    };
    return WowTooltip;
});
