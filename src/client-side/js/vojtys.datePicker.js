/*
* http://eternicode.github.io/bootstrap-datepicker/?markup=input&format=&weekStart=&startDate=&endDate=&startView=0&minViewMode=0&todayBtn=false&clearBtn=false&language=en&orientation=auto&multidate=&multidateSeparator=&keyboardNavigation=on&forceParse=on#sandbox
*/

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
            var settings = $.extend({}, $.fn.vojtysFormsDatepicker.defaults, $this.data('settings'));

            // init datepicker object
            if (!$this.data('vojtys-forms-datepicker')) {
                $this.data('vojtys-forms-datepicker', (new Vojtys.Forms.Datepicker($this, settings)));
            }
        });
    };

    Vojtys.Forms.Datepicker = function($element, options) {

        // init eternicode/bootstrap-datepicker
        $element.datepicker(options);
    }

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
