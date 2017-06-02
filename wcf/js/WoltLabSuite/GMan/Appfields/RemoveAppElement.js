

define(['Ajax', 'Dom/ChangeListener', 'Core'], function (Ajax, DomChangeListener, Core) {
    "use strict";
    /**
	 * @param       {string}        identifier      classname
	 * @param       {Object}        options         options and settings
	 * @constructor
	 */
    function RemoveAppElement(identifier, appID, options) { this.init(identifier, appID, options); }
    RemoveAppElement.prototype = {
        setup: function (appID, identifier) {
            this._setupcomplete = true;
            this._elements = null;
            this._appID = appID;
            this._identifier = identifier;
            DomChangeListener.add('WoltLabSuite/GMan/Appfields/RemoveAppElement', this.init.bind(this));
        },
        /**
		 * Initializes the buttons
		 *  
		 * @param       {string}        identifier      classname
		 * @param       {Object}        options         options and settings
		 */
        init: function (identifier, appID, options) {
            if (!(this._setupcomplete)) this.setup(appID, identifier);
            console.log("init removebtn for " + this._identifier + " (" + this._appID + ")");
            this._elements = document.getElementsByClassName(this._identifier);
            var element;
            while (this._elements.length) {
                element = this._elements[0];
                element.classList.remove(this._identifier);
                console.log("Hallo new button!")
                element.addEventListener('click', this._click.bind(this));
            }
            this._options = Core.extend({
                ajax: {
                    className: 'wcf\\data\\guild\\group\\application\\GuildGroupApplicationAction',
                },
            }, options);
        },
        _click: function (event) {
            if (this._appID > 0) {
                console.log("query")
                Ajax.api(this, {
                    objectIDs: [this._appID],
                    parameters: {
                        removeID: event.currentTarget.getAttribute('data-id')
                    }
                });
            }
            console.log("Hallo remove!")
            var currentLi = event.currentTarget.closest('.liMoveable');
            currentLi.parentNode.removeChild(currentLi);
        },
        _ajaxSetup: function () {
            return {
                data: this._options.ajax,
            };
        },
        _ajaxSuccess: function (data) {

        }
    };
    return RemoveAppElement;
});
