/*global $, jQuery, document, window, wp, _wpMediaViewsL10n, file_frame, timeapp_vars, alert, moment*/
jQuery(document).ready(function ($) {
    'use strict';

    // Setup color picker
    if ($('.timeapp-color-picker').length) {
        $('.timeapp-color-picker').wpColorPicker();
    }

    // Setup uploaders
    if ($('.timeapp_settings_upload_button').length) {
        $('body').on('click', '.timeapp_settings_upload_button', function (e) {
            e.preventDefault();

            var button = $(this);

            window.formfield = $(this).parent().prev();

            // If the media frame already exists, reopen it
            if (file_frame) {
                file_frame.open();
                return;
            }

            // Create the media frame
            file_frame = wp.media.frames.file_frame = wp.media({
                frame: 'post',
                state: 'insert',
                title: button.data('uploader_title'),
                button: {
                    text: button.data('uploader_button_text')
                },
                multiple: false
            });

            file_frame.on('menu:render:default', function (view) {
                // Store our views in an object
                var views = {};

                // Unset default menu items
                view.unset('library-separator');
                view.unset('gallery');
                view.unset('featured-image');
                view.unset('embed');

                // Initialize the views in our object
                view.set(views);
            });

            // Run a callback on select
            file_frame.on('insert', function () {
                var selection = file_frame.state().get('selection');

                selection.each(function (attachment, index) {
                    attachment = attachment.toJSON();
                    window.formfield.val(attachment.url);
                });
            });

            // Open the modal
            file_frame.open();
        });

        var file_frame;
        window.formfield = '';
    }

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
        href: '#timeapp-pdf-preview',
        maxWidth: '650px',
        maxHeight: '75%',
        closeButton: false
    });

    var dateFormat = 'mm/dd/yy';

    $('.timeapp-datetime').datetimepicker({
        timeFormat: 'h:mm tt',
        stepMinute: 15,
        firstDay: 0,
        dateFormat: 'mm/dd/yy',
        dayNamesMin: [
            'Su',
            'Mo',
            'Tu',
            'We',
            'Th',
            'Fr',
            'Sa'
        ]
    });

    $('.timeapp-select2').select2();

    $('#_timeapp_agent').select2({
        placeholder: timeapp_vars.select_agent
    });

    $('#_timeapp_purchaser').select2({
        placeholder: timeapp_vars.select_purchaser
    });

    $('#_timeapp_start_date').change(function (e) {
        var endDate = $('#_timeapp_end_date').val();

        if(endDate === '') {
            var startDate = new Date($(this).val());

            endDate = moment(startDate).add(1, 'days').format('MM/DD/YY h:mm a');
            $('#_timeapp_end_date').val(endDate);
        }
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
        //eventType = $("select[name='_timeapp_type'] option:selected").val();
        purchaser = $("select[name='_timeapp_purchaser'] option:selected").val();
        artist = $("select[name='_timeapp_artist'] option:selected").val();

        if (startDate === '' || endDate === '' || purchaser === '' || artist === '') {
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

    $("input[name='_timeapp_enable_additional_emails']").change(function () {
        if ($(this).is(':checked')) {
            $(".timeapp-additional-emails").css('display', 'block');
        } else {
            $(".timeapp-additional-emails").css('display', 'none');
        }
    });

    $("input[name='_timeapp_signatory']").change(function () {
        if ($(this).is(':checked')) {
            $("#contract_signatory").css('display', 'block');
        } else {
            $("#contract_signatory").css('display', 'none');
        }
    });

    if ($("input[name='_timeapp_enable_additional_emails']").is(':checked')) {
        $(".timeapp-additional-emails").css('display', 'block');
    } else {
        $(".timeapp-additional-emails").css('display', 'none');
    }

    if ($("input[name='_timeapp_signatory']").is(':checked')) {
        $("#contract_signatory").css('display', 'block');
    } else {
        $("#contract_signatory").css('display', 'none');
    }

    $("input[name='_timeapp_alt_mailing']").change(function () {
        if ($(this).is(':checked')) {
            $("#mailing_address").css('display', 'block');
        } else {
            $("#mailing_address").css('display', 'none');
        }
    });

    if ($("input[name='_timeapp_alt_mailing']").is(':checked')) {
        $("#mailing_address").css('display', 'block');
    } else {
        $("#mailing_address").css('display', 'none');
    }

    $('.timeapp-contract-log-toggle').click(function () {
        if ($('.timeapp-contract-log').is(':visible')) {
            $('.timeapp-contract-log').css('display', 'none');
        } else {
            $('.timeapp-contract-log').css('display', 'block');
        }
    });
});
