/**
 * checks if an input is valid 
 * @author	Veneanar Falkenbann
 * @copyright	2017  2017 Sylvanas Garde - sylvanasgarde.com - distributed by falkenbann.info
 * @license	GNU General Public License <http://opensource.org/licenses/gpl-license.php>
 * @package	info.falkenbann.guildman
 *
 */
define(['Ajax', 'Core', 'EventKey', 'Dom/Util'], function (Ajax, Core, EventKey, DomUtil) {
	"use strict";
	
	/**
	 * @param       {Element}       element         target input[type="text"]
	 * @param       {Object}        options         validation options and settings
	 * @constructor
	 */
	function UiValidationInput(element, options) { this.init(element, options); }
	UiValidationInput.prototype = {
		/**
		 * Initializes the validation input field.
		 * 
		 * @param       {Element}       element         target input[type="text"]
		 * @param       {Object}        options         search options and settings
		 */
		init: function(element, options) {
			this._element = element;
			if (!(this._element instanceof Element)) {
				throw new TypeError("Expected a valid DOM element.");
			}
			else if (this._element.nodeName !== 'INPUT' || (this._element.type !== 'search' && this._element.type !== 'text')) {
				throw new Error('Expected an input[type="text"].');
			}
			
			this._activeItem = null;
			this._lastValue = '';
			this._list = null;
			this._request = null;
			this._timerDelay = null;
			
			this._options = Core.extend({
				ajax: {
				    actionName: 'getValidateResult',
					className: '',
					interfaceName: 'wcf\\data\\IValidateAction',
				},
				callbackDropdownInit: null,
				callbackSelect: null,
				delay: 1000,
				minLength: 3,
				noResultPlaceholder: '',
				preventSubmit: true
               
			}, options);
			
			// disable auto-complete as it collides with the suggestion dropdown
			elAttr(this._element, 'autocomplete', 'off');
			this._setStatus('empty');
			this._element.addEventListener('keydown', this._keydown.bind(this));
			this._element.addEventListener('keyup', this._keyup.bind(this));
		},

	
		/**
		 * Handles the 'keydown' event.
		 * 
		 * @param       {Event}         event           event object
		 * @protected
		 */
		_keydown: function(event) {
			if (this._options.preventSubmit) {
				if (EventKey.Enter(event)) {
					event.preventDefault();
				}
			}
			
			if (EventKey.ArrowUp(event) || EventKey.ArrowDown(event) || EventKey.Escape(event)) {
				event.preventDefault();
			}
			this._setStatus('empty');
		},
		/**
		 * Handles the 'keyup' event, provides keyboard navigation and executes search queries.
		 * 
		 * @param       {Event}         event           event object
		 * @protected
		 */
		_keyup: function(event) {
			var value = this._element.value.trim();
			console.log("keydown!")

			if (this._options.delay) {
				if (this._timerDelay !== null) {
					window.clearTimeout(this._timerDelay);
				}
				
				this._timerDelay = window.setTimeout((function() {
				    this._validate(value);
				}).bind(this), this._options.delay);
			}
			else {
			    this._validate(value);
			}
		},

		/**
		 * Queries the server with the provided search string.
		 * 
		 * @param       {string}        value   search string
		 * @protected
		 */
		_validate: function (value) {
		    console.log("try req!")
		    if (value.length < this._options.minLength) return;
            console.log("request!")
			if (this._request) {
				this._request.abortPrevious();
			}
			this._setStatus('wait');
			this._request = Ajax.api(this, this._getParameters(value));
		},
		
		_setStatus: function (status, message, objectID) {
		    message = message || '';
		    objectID = objectID || 0;
		    var statusLabel = document.getElementById(this._element.id + 'status');
		    if (statusLabel == null) {
		        var div = document.createElement("div");
		        var parent = this._element.parentNode;
		        parent.insertBefore(div, this._element);
		        div.appendChild(this._element);
		        statusLabel = document.createElement('label');
		        statusLabel.setAttribute('for', this._element.id);
		        statusLabel.setAttribute('id', this._element.id + 'status');
		        div.appendChild(statusLabel);
		    }
		    if (status == 'failed') statusLabel.innerHTML = '<span class="icon icon32 fa-times jsTooltip" style="color:red" title="' + message + '" data-tooltip="' + message + '"></span>';
		    if (status == 'error') statusLabel.innerHTML = '<span class="icon icon32 fa-exclamation-triangle style="color:red" jsTooltip" title="' + message + '""  data-tooltip="' + message + '"></span>';
		    if (status == 'ok') statusLabel.innerHTML = '<span class="icon icon32 fa-check-square-o jsTooltip" style="color:green" title="' + message + '"" data-tooltip="' + message + '"></span>';
		    if (status == 'wait') statusLabel.innerHTML = '<span class="icon icon32 fa-spinner jsTooltip" title="' + message + '"" data-tooltip="' + message + '"></span>';
    	    if (status == 'empty') statusLabel.innerHTML = '';
    	    this._element.setAttribute('data-validation', status);
    	    this._element.setAttribute('data-objectID', status);
    	    this._finish(status, message, objectID);
        },
		/**
		 * Returns additional AJAX parameters.
		 * 
		 * @param       {string}        value   search string
		 * @return      {Object}        additional AJAX parameters
		 * @protected
		 */
		_getParameters: function (value) {
		    this._value = value;
			return {
				parameters: {
					data: {
						validationString: value
					}
				}
			};
		},
		/**
		 * Handles successful AJAX requests.
		 * 
		 * @param       {Object}        data    response data
		 * @protected
		 */
		_ajaxSuccess: function (data) {
		    console.log(data.returnValues)
		    if (data.returnValues) {
		        if (data.returnValues.status == 1) {
		            data.returnValues.objectID = data.returnValues.objectID || 0;
		            this._setStatus('ok', '' ,data.returnValues.objectID);
		        }
		        else {
		            this._setStatus('failed', data.returnValues.msg);
                }
		    }
		    else {
		        this._setStatus('error', data.returnValues.msg);
		    }
		},
	    /**
		 * Handles failed AJAX requests.
		 * 
		 * @param       {Object}        data    response data
		 * @protected
		 */
		_ajaxFailure: function (data) {
		    console.log(data)
		    this._setStatus('error', data.returnValues.errorMessage);
		},
		/**
		 * Handles an empty result set, return a boolean false to hide the dropdown.
		 * 
		 * @protected
		 */
		_handleEmptyResult: function() {
		    this._setStatus('error', 'No server response');
		},
		
		_finish: function (status, message, objectID) {

		},
	
		_ajaxSetup: function() {
			return {
			    data: this._options.ajax,
			    silent: true
			};
		}
	};
	
	return UiValidationInput;
});
