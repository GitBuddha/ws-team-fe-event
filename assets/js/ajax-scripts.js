jQuery(document).ready(function($) {

    var datePickers = [
        '#event-start-date-time',
        '#event-end-date-time',
        '#tt-start-date',
        '#tt-end-date'
    ];

    jQuery.each(datePickers, function (index, value) {
        jQuery(value).datepicker.language['en'] = {
            days: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
            daysShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
            daysMin: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
            months: ['January','February','March','April','May','June', 'July','August','September','October','November','December'],
            monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            today: 'Today',
            clear: 'Clear',
            dateFormat: 'yyyy-mm-dd',
            firstDay: 0
        };
        jQuery(value).datepicker({
            dateFormat: 'yyyy-mm-dd',
            language: 'en',
        });
    });

    // Ajax Get Form for Existing User
    jQuery('body').on('click', '#wst-user-type__existing', function () {
        // Run Loader
        jQuery('#wst-user-registration__section').empty().html('<div class="loader"></div>');
        jQuery.ajax({
            type: 'POST',
            url: wst_ajax.url,
            data: {
                action    : 'get_registration_form',
                user_type : 'existing'
            },
            dataType: "json",
            success: function( data ){
                console.log(data.content);
                if (data.status) {
                    jQuery('#wst-user-registration__section').empty().html(data.content);
                }
            }
        });
    });

    // Ajax Get Form for New User
    jQuery('body').on('click', '#wst-user-type__new', function () {
        // Run Loader
        jQuery('#wst-user-registration__section').empty().html('<div class="loader"></div>');
        jQuery.ajax({
            type: 'POST',
            url: wst_ajax.url,
            data: {
                action    : 'get_registration_form',
                user_type : 'new'
            },
            dataType: "json",
            success: function( data ){
                console.log(data.content);
                if (data.status) {
                    jQuery('#wst-user-registration__section').empty().html(data.content);
                }
            }
        });
    });

    // Ajax Add extra row for Ticket Type
    jQuery('body').on('click', '.add_new_row', function () {
        var prn = jQuery("#wst-ticket-type__section").children();
        var extraRowID = prn.last().attr('data-extra');

        // Run Loader
        jQuery('#wst-ticket-type__section').append('<div class="loader"></div>');
        jQuery.ajax({
            type: 'POST',
            url: wst_ajax.url,
            data: {
                action       : 'get_extra_tt',
                extra_row_id : extraRowID
            },
            dataType: "json",
            success: function( data ){
                if( data.status ) {
                    // Remove Loader
                    jQuery('.loader').remove();
                    jQuery('#wst-ticket-type__section').append(data.content);
                }
            }
        });
    });

    // Ajax Delete Row
    jQuery('body').on('click', '.delete_new_row', function () {
        jQuery(this).parent().parent().remove();
    });

    var files;
    jQuery('input[type=file]').on('change', function(){
        files = this.files;
    });

    /**
     * Submit form
     */
    jQuery('#wst-event-form').submit(function (event) {
        event.stopPropagation();
        event.preventDefault();

        var data = new FormData();
        jQuery.each( files, function( key, value ){
            data.append( key, value );
        });

        var form_data = jQuery(this).serialize();
        var event_description = tinyMCE.activeEditor.getContent();

        data.append( 'action', 'front_end_create_events' );
        data.append( 'form_data', form_data );
        data.append( 'event_description', event_description );

        jQuery.ajax({
            type  : 'POST',
            url   : wst_ajax.url,
            data  : data,
            cache : false,
            dataType: "json",
            processData : false,
            contentType : false,
            success: function( data ){
                if ( data.status ) {
                    jQuery(this).trigger('reset');
                    jQuery('#wst-success-backgroud').removeClass('wst-hidden');
                    jQuery('#wst-success-popup').removeClass('wst-hidden');
                    setTimeout( reloadPage, 2000 );
                } else {
                    jQuery('#wst-success-backgroud').removeClass('wst-hidden');
                    jQuery('#wst-success-popup').removeClass('wst-hidden');
                    jQuery('#wst-success-popup').children('h2').empty().html(data.content);
                }
            }
        });

    });

    function reloadPage() {
        location.reload();
    }

    jQuery('body').on('click', '.wst-close', function () {
        jQuery('#wst-success-backgroud').addClass('wst-hidden');
        jQuery('#wst-success-popup').addClass('wst-hidden');
    });

});