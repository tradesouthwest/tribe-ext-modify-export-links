/* #inStructions.texmod-instruct */
        
jQuery(window).load(function() {
    jQuery(".ical-button.ical-button-ins").on('click', function() {
        jQuery('#inStructions').show();
    });
    jQuery(".ical-button.ical-button-rmv").on('click', function() {
        jQuery("#inStructions").hide();
    });
    })();
    