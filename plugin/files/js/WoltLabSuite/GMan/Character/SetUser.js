define(['Ajax', 'Core', 'Dom/Util'], function (Ajax, Core, DomUtil) { 
    "use strict";
    /**
	 * @param       {Element}       element         button array
	 * @param       {Object}        options         options and settings
	 * @constructor
	 */
    function SetCharToUser(elements, options) { this.init(elements, options); }
    SetCharToUser.prototype = {
        /**
		 * Initializes the buttons
		 * 
		 * @param       {Element}       element         button array
		 * @param       {Object}        options         options and settings
		 */
        init: function (elements, options) {

            this._elements = elements;
            console.log("elemente: " + this._elements)
            for (var i = 0, length = this._elements.length; i < length; i++) {
                console.log("element " + i + ": " + this._elements[i].name)
                this._elements[i].addEventListener('click', this._click.bind(this));
            }
            this._options = Core.extend({
                ajax: {
                    actionName: 'setUser',
                    className: 'wcf\\data\\wow\\character\\WowCharacterAction'
                },
                noResultPlaceholder: '',
            }, options);

        },
        _click: function (event) {
            Ajax.api(this, {
                objectIDs: [event.currentTarget.getAttribute('data-char-id')],
                parameters: {
                    userID: event.currentTarget.getAttribute('data-user-id')
                }
            });
        },
        _ajaxSuccess: function (data) {
            location.reload();
        },

        _ajaxSetup: function() {
            return {
                data: this._options.ajax,
            };
        }
    };
    return SetCharToUser;
});
