/*global jQuery, document*/
jQuery(document).ready(function ($) {
    'use strict';

    if( jQuery('.timeapp-datetime').length ) {
        var dateFormat = 'mm/dd/yy';

        jQuery('.timeapp-datetime').datetimepicker({
            timeFormat: 'h:mm tt',
            showTime: false
        });
    }
    jQuery('.timeapp-datetime').clearable();

    jQuery('.post-type-purchaser .timeapp-save').click(function () {
        var firstName, lastName, email, address, city, state, errMessage;

        firstName = jQuery("input[name='_timeapp_first_name']").val();
        lastName = jQuery("input[name='_timeapp_last_name']").val();
        email = jQuery("input[name='_timeapp_email']").val();
        city = jQuery("input[name='_timeapp_city']").val();
        state = jQuery("select[name='_timeapp_state'] option:selected").val();
        
        if( firstName === '' || lastName === '' || email === '' || city === '' || state === '' ) {
            errMessage = timeapp_vars.required_fields;
            alert(errMessage);
        }
    });
        
    jQuery('.post-type-artist .timeapp-save').click(function () {
        var signerName, artistEmail, taxID, errMessage;

        signerName = jQuery("input[name='_timeapp_signer_name']").val();
        artistEmail = jQuery("input[name='_timeapp_artist_email']").val();
        taxID = jQuery("input[name='_timeapp_tax_id']").val();

        if( signerName === '' || artistEmail === '' || taxID === '' ) {
            errMessage = timeapp_vars.required_fields;
            alert(errMessage);
        }
    });
});
