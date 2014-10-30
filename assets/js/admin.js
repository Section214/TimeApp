/*global jQuery, document, timeapp_vars, alert*/
jQuery(document).ready(function ($) {
    'use strict';

    jQuery(function () {
        if (jQuery('#_timeapp_rider').length > 0) {
            jQuery('form').attr('enctype', 'multipart/form-data');
        }
    });

    jQuery('.colorbox').colorbox({
        inline: true,
        href: "#timeapp-pdf-preview",
        maxWidth: "650px",
        maxHeight: "75%",
        closeButton: false
    });

    var dateFormat = 'mm/dd/yy';

    jQuery('.timeapp-datetime').datetimepicker({
        timeFormat: 'h:mm tt'
    });

    jQuery('.timeapp-select2').select2();

    jQuery('#_timeapp_agent').select2({
        placeholder: timeapp_vars.select_agent
    });

    jQuery('#_timeapp_purchaser').select2({
        placeholder: timeapp_vars.select_purchaser
    });

    jQuery('.post-type-play .timeapp-save').click(function () {
        var startDate, endDate, eventType, purchaser, artist, errMessage;

        startDate = jQuery("input[name='_timeapp_start_date']").val();
        endDate = jQuery("input[name='_timeapp_end_date']").val();
        eventType = jQuery("select[name='_timeapp_type'] option:selected").val();
        purchaser = jQuery("select[name='_timeapp_purchaser'] option:selected").val();
        artist = jQuery("select[name='_timeapp_artist'] option:selected").val();

        if (startDate === '' || endDate === '' || eventType === '' || purchaser === '' || artist === '') {
            errMessage = timeapp_vars.required_fields;
            alert(errMessage);
        }
    });

    jQuery('.post-type-purchaser .timeapp-save').click(function () {
        var firstName, lastName, email, address, city, state, errMessage;

        firstName = jQuery("input[name='_timeapp_first_name']").val();
        lastName = jQuery("input[name='_timeapp_last_name']").val();
        email = jQuery("input[name='_timeapp_email']").val();
        address = jQuery("input[name='_timeapp_address']").val();
        city = jQuery("input[name='_timeapp_city']").val();
        state = jQuery("select[name='_timeapp_state'] option:selected").val();

        if (firstName === '' || lastName === '' || email === '' || address === '' || city === '' || state === '') {
            errMessage = timeapp_vars.required_fields;
            alert(errMessage);
        }
    });

    jQuery('.post-type-artist .timeapp-save').click(function () {
        var signerName, artistEmail, taxID, errMessage;

        signerName = jQuery("input[name='_timeapp_signer_name']").val();
        artistEmail = jQuery("input[name='_timeapp_artist_email']").val();
        taxID = jQuery("input[name='_timeapp_tax_id']").val();

        if (signerName === '' || artistEmail === '' || taxID === '') {
            errMessage = timeapp_vars.required_fields;
            alert(errMessage);
        }
    });

    jQuery("input[name='_timeapp_bonus']").change(function () {
        if (jQuery(this).is(':checked')) {
            jQuery("input[name='_timeapp_bonus_details']").closest('p').css('display', 'block');
        } else {
            jQuery("input[name='_timeapp_bonus_details']").closest('p').css('display', 'none');
        }
    });
    
    jQuery("input[name='_timeapp_deposit']").change(function () {
        if (jQuery(this).is(':checked')) {
            jQuery("#timeapp-deposits").css('display', 'block');
        } else {
            jQuery("#timeapp-deposits").css('display', 'none');
        }
    });

    jQuery("input[name='_timeapp_production']").change(function () {
        if (jQuery(this).is(':checked')) {
            jQuery("input[name='_timeapp_production_cost']").closest('p').css('display', 'none');
        } else {
            jQuery("input[name='_timeapp_production_cost']").closest('p').css('display', 'block');
        }
    });

    jQuery("input[name='_timeapp_split_comm']").change(function () {
        if (jQuery(this).is(':checked')) {
            jQuery("input[name='_timeapp_split_perc']").closest('div').css('display', 'block');
        } else {
            jQuery("input[name='_timeapp_split_perc']").closest('div').css('display', 'none');
        }
    });
});
