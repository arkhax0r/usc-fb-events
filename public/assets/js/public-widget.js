/**
 * Created by Paul on 29/07/14.
 */

jQuery(function ($) {
    /* You can safely use $ in this code block to reference jQuery */

    $(document).ready(function($) {

        ajax_get_events();
    });

    /**
     * Function sets up our list in the backend.
     * Gets the eids of removed events and merges them with the data from Facebook.
     *
     * @param events    a JSON list of Facebook Events
     *
     * @since   0.4.0
     */
    function ajax_get_events() {

        //console.log( ajax );

        // Assign handlers immediately after making the request,
        // and remember the jqxhr object for this request
        var jqxhr = $.post(
            ajax.url,
            {
                action:         "get_events",
                attr_id:        "event_list",
                nonce:          $("#event_list").data("nonce"),
                remove_events:  1
                //we don't need this column because it defaults to false.
                //whitelist: 0
            },

            function( data ) {

                //console.log(data);

                if(! data['success']) {
                    alert("Ack! Problems getting your removed events back from the database.")
                }

                var events = data['response']['events'];

                $.each(events, function(i, event){
                    event.id = i+1;

                    if( parseInt(event.removed) === 1 ) {
                        event.removed = "removed";
                    }
                    else
                    if ( parseInt(event.modified) === 1 ){
                        event.removed = "modified";
                    }
                    else
                        event.removed = "display";
                });


                $('.filterjs__loading').addClass('hidden');

                return_widget_html( events );

                //$('#event_list').trigger( "change" );

            }, "json");
        /*.fail(function() {
         alert( "error" );
         })
         .always(function() {
         alert( "finished" );
         });*/
    }

    function return_widget_html( events ) {

        console.log( "success" );

        /*

        var view = function( event ){

        //at this point we have ONE EVENT.  This sets up the loop.
        var html_string = "";

        var img_url = ( event.pic_big ) ? event.pic_big : "";
        var ticket_uri = ( event.ticket_uri ) ? event.ticket_uri : "";

        html_string += '<div class="events__box clearfix">';
        html_string +=  '<div class="flag">';
        html_string +=      '<div class="flag__image">';

        if(img_url) {
            html_string +=      '<img src="' + img_url + '">';
        }

        html_string +=      '</div><!--end of .flag__image-->';

        html_string +=      '<div class="flag__body">';
        html_string +=          '<h3 class="alpha" title="'
            + event.host + ": " + event.name + '">'
            + event.name + '</h3>';

        var date = new Date( parseInt(event.start_time) * 1000);

        html_string +=          '<p class="lede">' + date.toLocaleDateString() + ' | '
            + event.host + '</p>';

        html_string +=      '</div><!--end of .flag__body-->';

        html_string += '</div><!--end of .flag-->';

        if( ticket_uri ) {

            html_string += '<a href="' + ticket_uri + '" target="_blank">';
            html_string +=      '<div class="event__button" style="background:palevioletred;color:white;">Get Tickets</div>';
            html_string += '</a>';
        }

        html_string +=      '<a href="' + event.url + '" target="_blank">';
        html_string +=          '<span class="events__box__count">' + event.id + '</span>';
        html_string +=      '</a>';

        html_string += '</div><!--end of events__box-->';

        return html_string;
        }
        */
    }

});