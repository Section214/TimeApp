/*global $, document, timeapp_vars, alert*/
jQuery(document).ready(function ($) {
    'use strict';

    $('.post-type-play #title').attr('disabled', 'disabled');
    $('.post-type-play #title').attr('readonly', 'readonly');
    
    if ($('.post-type-play #title').val() === '') {
        $('.post-type-play #title').val(timeapp_vars.title_placeholder);
    }

    $(function () {
        if ($('#_timeapp_rider').length > 0) {
            $('form').attr('enctype', 'multipart/form-data');
        }
    });

    $('.post-type-play input').change(function () {
        $('.colorbox').css('display', 'none');
    });

    $('.post-type-play select').change(function () {
        $('.colorbox').css('display', 'none');
    });

    $('.post-type-play textarea').change(function () {
        $('.colorbox').css('display', 'none');
    });

    $('.colorbox').colorbox({
        inline: true,
        href: "#timeapp-pdf-preview",
        maxWidth: "650px",
        maxHeight: "75%",
        closeButton: false
    });

    var dateFormat = 'mm/dd/yy';

    $('.timeapp-datetime').datetimepicker({
        timeFormat: 'h:mm tt'
    });

    $('.timeapp-select2').select2();

    $('#_timeapp_agent').select2({
        placeholder: timeapp_vars.select_agent
    });

    $('#_timeapp_purchaser').select2({
        placeholder: timeapp_vars.select_purchaser
    });

    $('.post-type-agent .timeapp-save').click(function (e) {
        e.preventDefault();
        $('#publish').click();
        $('.colorbox').css('display', 'inline-block');
    });

    $('.post-type-play .timeapp-save').click(function (e) {
        var startDate, endDate, eventType, purchaser, artist, errMessage;

        startDate = $("input[name='_timeapp_start_date']").val();
        endDate = $("input[name='_timeapp_end_date']").val();
        eventType = $("select[name='_timeapp_type'] option:selected").val();
        purchaser = $("select[name='_timeapp_purchaser'] option:selected").val();
        artist = $("select[name='_timeapp_artist'] option:selected").val();

        if (startDate === '' || endDate === '' || eventType === '' || purchaser === '' || artist === '') {
            errMessage = timeapp_vars.required_fields;
            alert(errMessage);
        }
        
        e.preventDefault();
        $('#publish').click();
    });

    $('.post-type-purchaser .timeapp-save').click(function (e) {
        var firstName, lastName, email, address, city, state, zip, errMessage;

        firstName = $("input[name='_timeapp_first_name']").val();
        lastName = $("input[name='_timeapp_last_name']").val();
        email = $("input[name='_timeapp_email']").val();
        address = $("input[name='_timeapp_address']").val();
        city = $("input[name='_timeapp_city']").val();
        state = $("select[name='_timeapp_state'] option:selected").val();
        zip = $("input[name='_timeapp_zip']").val();

        if (firstName === '' || lastName === '' || email === '' || address === '' || city === '' || state === '' || zip === '') {
            errMessage = timeapp_vars.required_fields;
            alert(errMessage);
        }
        
        e.preventDefault();
        $('#publish').click();
    });

    $('.post-type-artist .timeapp-save').click(function (e) {
        var signerName, artistEmail, taxID, errMessage;

        signerName = $("input[name='_timeapp_signer_name']").val();
        artistEmail = $("input[name='_timeapp_artist_email']").val();
        taxID = $("input[name='_timeapp_tax_id']").val();

        if (signerName === '' || artistEmail === '' || taxID === '') {
            errMessage = timeapp_vars.required_fields;
            alert(errMessage);
        }
        
        e.preventDefault();
        $('#publish').click();
    });

    $("input[name='_timeapp_bonus']").change(function () {
        if ($(this).is(':checked')) {
            $("input[name='_timeapp_bonus_details']").closest('p').css('display', 'block');
        } else {
            $("input[name='_timeapp_bonus_details']").closest('p').css('display', 'none');
        }
    });

    $("input[name='_timeapp_deposit']").change(function () {
        if ($(this).is(':checked')) {
            $("#timeapp-deposits").css('display', 'block');
        } else {
            $("#timeapp-deposits").css('display', 'none');
        }
    });

    $("input[name='_timeapp_production']").change(function () {
        if ($(this).is(':checked')) {
            $("input[name='_timeapp_production_cost']").closest('p').css('display', 'none');
        } else {
            $("input[name='_timeapp_production_cost']").closest('p').css('display', 'block');
        }
    });

    $("input[name='_timeapp_split_comm']").change(function () {
        if ($(this).is(':checked')) {
            $("input[name='_timeapp_split_perc']").closest('div').css('display', 'block');
        } else {
            $("input[name='_timeapp_split_perc']").closest('div').css('display', 'none');
        }
    });

    $("input[name='_timeapp_signatory']").change(function () {
        if ($(this).is(':checked')) {
            $("#contract_signatory").css('display', 'block');
        } else {
            $("#contract_signatory").css('display', 'none');
        }
    });

    if ($("input[name='_timeapp_signatory']").is(':checked')) {
        $("#contract_signatory").css('display', 'block');
    } else {
        $("#contract_signatory").css('display', 'none');
    }

    $('.timeapp-contract-log-toggle').click(function () {
        if ($('.timeapp-contract-log').is(':visible')) {
            $('.timeapp-contract-log').css('display', 'none');
        } else {
            $('.timeapp-contract-log').css('display', 'block');
        }
    });
});
