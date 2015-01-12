/**
 * @fileOverview Queue Bootstrap alerts with Javascript
 *               <p>License MIT
 *               <br>Copyright 2014 Kristoffer Lundén
 *               <br>GitHub: https://github.com/kribblo/bootstrap.alerts-queue
 *               </p>
 * @version 1.0
 * @author Kristoffer Lundén
 * @requires jQuery
 *
 */

/*global jQuery, define*/

/**
 * The jQuery library
 *
 * @name jQuery
 * @class
 * @see {@link http://jquery.com jQuery}
 */

/**
 * jQuery plugin namespace
 *
 * @name jQuery.fn
 * @namespace
 * @see {@link http://learn.jquery.com/plugins/ jQuery plugins}
 */

(function(factory) {
	'use strict';
	if (typeof define === 'function' && define.amd) {
		define(['jquery'], factory);
	} else {
		factory(jQuery);
	}
}(function(jQuery) {
	'use strict';

	/**
	 *
	 * Adds a warning alert to a container. Will call bootstrapAlertsQueue to initialize if not already done, so can be used directly.
	 *
	 * @param text The text in the alert
	 * @param options See bootstrapAlertsQueue
	 * @returns {jQuery} The chained jQuery element
	 */
	jQuery.fn.addWarningAlert = function(text, options) {
		return this.each(function() {
			jQuery(this).addAlert(text, jQuery.extend({}, options, {type: 'alert-warning'}));
		});
	};

	/**
	 *
	 * Adds an info alert to a container. Will call bootstrapAlertsQueue to initialize if not already done, so can be used directly.
	 *
	 * @param text The text in the alert
	 * @param options See bootstrapAlertsQueue
	 * @returns {jQuery} The chained jQuery element
	 */
	jQuery.fn.addInfoAlert = function(text, options) {
		return this.each(function() {
			jQuery(this).addAlert(text, jQuery.extend({}, options, {type: 'alert-info'}));
		});
	};

	/**
	 *
	 * Adds a danger alert to a container. Will call bootstrapAlertsQueue to initialize if not already done, so can be used directly.
	 *
	 * @param text The text in the alert
	 * @param options See bootstrapAlertsQueue
	 * @returns {jQuery} The chained jQuery element
	 */
	jQuery.fn.addDangerAlert = function(text, options) {
		return this.each(function() {
			jQuery(this).addAlert(text, jQuery.extend({}, options, {type: 'alert-danger'}));
		});
	};

	/**
	 *
	 * Adds a success alert to a container. Will call bootstrapAlertsQueue to initialize if not already done, so can be used directly.
	 *
	 * @param text The text in the alert
	 * @param options See bootstrapAlertsQueue
	 * @returns {jQuery} The chained jQuery element
	 */
	jQuery.fn.addSuccessAlert = function(text, options) {
		return this.each(function() {
			jQuery(this).addAlert(text, jQuery.extend({}, options, {type: 'alert-success'}));
		});
	};

	/**
	 * Adds an alert to a container. Will call bootstrapAlertsQueue to initialize if not already done, so can be used directly.
	 *
	 * @methodOf jQuery.fn
	 *
	 * @param text The text in the alert
	 * @param options See bootstrapAlertsQueue
	 * @returns {jQuery} The chained jQuery element
	 */
	jQuery.fn.addAlert = function(text, options) {
		return this.each(function() {
			var $this = jQuery(this);
			var $holder = $this.children('.alerts-holder');
			if ($holder.length === 0) {
				$this.bootstrapAlertsQueue(options);
				$holder = $this.children('.alerts-holder');
			}
			options = jQuery.extend($holder.data(), options);

			var $alert = createAlert(text, options);

			if (options.closeButtonText) {
				addCloseButton($alert, options);
			}

			$holder.append($alert);
            $alert.fadeIn('slow');

			if (options.timeout > 0) {
				setTimeout(function() {
					$alert.fadeOut("slow", function () {
                        $(this).remove();
                    });
				}, options.timeout);
			}
		});
	};

	/**
	 * Clear all alerts
	 *
	 * @returns {jQuery} The chained jQuery element
	 */
	jQuery.fn.clearAlerts = function() {
		jQuery(this).children('.alerts-holder').remove();
	};

	/**
	 * Setup an alerts container element with default options.
	 *
	 * @param options
	 * @param options.type The class that defines which type of alert: alert-info, alert-success, alert-warning, alert-danger - or your own self-defined class
	 * @param options.holderTop Top padding (padding-top) of the holder element if created automatically, defaults to '10px'
	 * @param options.distance Distance (as margin-bottom) between each alert, defaults to '10px'
	 * @param options.width Width behaviour of each alert as uniform (all sized as current largest), natural (each has their own width) or a CSS value
	 * @param options.timeout Time before automatic fadeout, or falsy for only manually closed
	 * @param options.closeButtonText Text inside the close button, set to falsy to disable close button
	 * @returns {jQuery} The chained jQuery element
	 */
	jQuery.fn.bootstrapAlertsQueue = function(options) {
		var defaultOptions = {
			type: 'alert-info',
			timeout: undefined,
			closeButtonText: '&times;'
		};

		return this.each(function() {
			var $this = jQuery(this);
			options = jQuery.extend(defaultOptions, options);

			var $holder = ensureAlertsHolder($this, options);
			$holder.data(options);

			function ensureAlertsHolder($parent, options) {
				var $holder = $parent.children('.alerts-holder');
				options = jQuery.extend(defaultOptions, options);
				var holderTop = 0;
				var holderLeft = 0;

				if ($this.css('position') === 'static') {
					holderTop = $this.offset().top;
					holderLeft = $this.offset().left;
				}

				if ($holder.length === 0) {
					$holder = jQuery('<div class="alerts-holder"></div>')
                        .prependTo($parent);
				}
				return $holder;
			}
		});
	};

	function createAlert(text, options) {
		var $alert = jQuery('<div class="alert"></div>');
		$alert.text(text);
		var type = options.type;
		$alert.addClass(type);
		$alert.addClass("hide");
		return $alert;
	}

	function addCloseButton($alert, options) {
		$alert.addClass('alert-dismissable');
		$alert.prepend('<button type="button" class="close" data-dismiss="alert">' + options.closeButtonText + '</button>');
	}
}));
