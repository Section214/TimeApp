/*global jQuery, document*/
jQuery(document).ready(function ($) {
    'use strict';

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
