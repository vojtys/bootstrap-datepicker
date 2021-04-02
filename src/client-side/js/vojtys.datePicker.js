/**
 * Client-side script for DatePicker
 *
 *
 * @author Vojtech Sedlacek (sedlacekvojtech@gmail.com))
 */
(function($, window, document, location, navigator) {

    /* jshint laxbreak: true, expr: true */
    "use strict";

    // init objects
    var Vojtys = window.Vojtys || {};
    Vojtys.Forms = Vojtys.Forms || {};

    $.fn.vojtysFormsDatepicker = function () {
        return this.each(function() {
            var $this = $(this);
            // merge settings with defaults
            var defaults = $.extend({}, $.fn.vojtysFormsDatepicker.defaults, {language: $this.data('locale')});
            var settings = $.extend({}, defaults, $this.data('settings'));
            // init datepicker object
            if (!$this.data('vojtys-forms-datepicker')) {
                $this.data('vojtys-forms-datepicker', (new Vojtys.Forms.Datepicker($this, settings)));
            }
        });
    };

    Vojtys.Forms.Datepicker = function($element, options) {
        if (options.inline) {
            $element = $element.find('div');
        }
        // init eternicode/bootstrap-datepicker
        var dp = $element.datepicker(options);
        if (options.inline) {
            $element.on("changeDate", function(event) {
                $element.prev().val($element.datepicker('getFormattedDate'));
            });
            $element.datepicker('update', $element.prev().val());
        }
    };

    /**
     * Autoloading date picker plugin
     */
    Vojtys.Forms.Datepicker.load = function ()
    {
        $('[data-vojtys-forms-datepicker]').vojtysFormsDatepicker();
    };

    /**
     * Default settings
     */
    $.fn.vojtysFormsDatepicker.defaults = {
        language: 'cs',
    };

    // Autoload plugin
    Vojtys.Forms.Datepicker.load();

    // Assign data to DOM
    window.Vojtys = Vojtys;

    // Return object
    return Vojtys;

    // Immediately invoke function with default parameters
})(jQuery, window, document, location, navigator);
