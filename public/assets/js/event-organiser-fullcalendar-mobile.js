//(function ($) {

//})(jQuery);

/*
 var EOAjaxFront = {
 "adminajax":"http:\/\/westernusc.org\/wp-admin\/admin-ajax.php",
 "locale":{
 "locale":"en",
 "isrtl":false,
 "monthNames":["January","February","March","April","May","June","July","August","September","October","November","December"],
 "monthAbbrev":["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
 "dayNames":["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"],
 "dayAbbrev":["Sun","Mon","Tue","Wed","Thu","Fri","Sat"],
 "ShowMore":"Show More",
 "ShowLess":"Show Less",
 "today":"today",
 "day":"day",
 "week":"week",
 "month":"month",
 "gotodate":"go to date",
 "cat":"View all categories",
 "venue":"View all venues",
 "tag":"View all tags",
 "nextText":">"
 ,"prevText":"<"
 }
 };
 var eventorganiser = {
 "ajaxurl":"http:\/\/westernusc.org\/wp-admin\/admin-ajax.php",
 "calendars":[
 {
 "headerleft":"prev,next today",
 "headercenter":"title",
 "headerright":"category",
 "defaultview":"month",
 "event-category":"",
 "event_category":"",
 "event-venue":"",
 "event_venue":"",
 "timeformat":"h:mm tt",
 "axisformat":"h:mm tt",
 "tooltip":true,
 "weekends":true,
 "mintime":"0",
 "maxtime":"24",
 "alldayslot":true,
 "alldaytext":"All Day",
 "columnformatmonth":"ddd",
 "columnformatweek":"ddd M\/d",
 "columnformatday":"dddd M\/d",
 "titleformatmonth":"MMMM yyyy",
 "titleformatweek":"MMM d[ yyyy]{ '&#8212;'[ MMM] d, yyyy}",
 "titleformatday":"dddd, MMM d, yyyy",
 "year":false,
 "month":false,
 "date":false,
 "users_events":false,
 "event_occurrence__in":[],
 "theme":true,
 "isrtl":false,
 "timeformatphp":"g:i a",
 "axisformatphp":"g:i a",
 "columnformatdayphp":"l n\/j",
 "columnformatweekphp":"D n\/j",
 "columnformatmonthphp":"D",
 "titleformatmonthphp":"F Y",
 "titleformatdayphp":"l, M j, Y",
 "titleformatweekphp":"M j[ Y]{ '&#8212;'[ M] j, Y}"
 }
 ],
 "widget_calendars":[],
 "fullcal":{
 "firstDay":1,
 "venues":[
 {
 "term_id":"560",
 "name":"Univeristy Community Centre",
 "slug":"univeristy-community-centre",
 "term_group":"0",
 "term_taxonomy_id":"560",
 "taxonomy":"event-venue",
 "description":"",
 "parent":"0",
 "count":"1",
 "venue_address":"",
 "venue_city":"",
 "venue_state":"",
 "venue_postcode":"",
 "venue_country":"",
 "venue_lat":"0.000000",
 "venue_lng":"0.000000"
 }
 ],
 "categories":{
 "570":{
 "term_id":"570",
 "name":"Clubs",
 "slug":"clubs-2",
 "term_group":"0",
 "term_taxonomy_id":"570",
 "taxonomy":"event-category",
 "description":"",
 "parent":"0",
 "count":"0",
 "color":"#cb87cd"
 },
 "566":{
 "term_id":"566",
 "name":"Faculty Councils",
 "slug":"faculty-councils",
 "term_group":"0",
 "term_taxonomy_id":"566",
 "taxonomy":"event-category",
 "description":"",
 "parent":"0",
 "count":"0",
 "color":"#f078d2"
 },
 "562":{
 "term_id":"562",
 "name":"USC",
 "slug":"usc-2",
 "term_group":"0",
 "term_taxonomy_id":"562",
 "taxonomy":"event-category",
 "description":"",
 "parent":"0",
 "count":"1",
 "color":"#5A4099"
 },
 "567":{
 "term_id":"567",
 "name":"Western",
 "slug":"western-2",
 "term_group":"0",
 "term_taxonomy_id":"567",
 "taxonomy":"event-category",
 "description":"",
 "parent":"0",
 "count":"0",
 "color":"#e99d12"
 }
 },
 "tags":[
 {
 "term_id":"565",
 "name":"Fun",
 "slug":"fun",
 "term_group":"0",
 "term_taxonomy_id":"565",
 "taxonomy":"event-tag",
 "description":"",
 "parent":"0",
 "count":"1"
 }
 ]
 },
 "map":[]
 };
 */
var eventorganiser = window.eventorganiser || {};
var EOAjaxFront = EOAjaxFront || {};
var AjaxEvents = AjaxEvents || {};


var AjaxFullCalendarList = (function ( options, AjaxEvents, eventorganiser) {

    //var $ = jQuery;
    var _eo_fullcalendar;
    var _list_container;
    var _list_div;

    var _calendar_name = '';
    var id = options.id;

    /* http://stackoverflow.com/questions/4793604/how-to-do-insert-after-in-javascript-without-using-a-library */
    var _insert_after = function (newNode, referenceNode) {
        referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
    };

    var _create_list_container = (function () {

        //var html_string = '<div class="eo_fullcalendar--list"></div>';
        //remove existing lists.
        if( typeof( _list_div ) !== "undefined" )
            _list_div.parentNode.removeChild(_list_div);


        var fragment = document.createDocumentFragment();

        _list_div = document.createElement("div");
        _list_div.id = id;
        //the nonce will always be the same, right?  so no reason to keep the <div> and only remove the <li>s
        _list_div.setAttribute( "data-nonce", options.nonce );


        _list_container =  document.createElement("ul");
        _list_div.appendChild( _list_container );
        fragment.appendChild(_list_div);

        _insert_after(fragment, _eo_fullcalendar);
    });

    var _create_list_item = (function( event ) {

        var fragment = document.createDocumentFragment();
        var list_item = document.createElement('li');

        var list_item_array = [ "title", "start", "host" ];
        var list_item_array_length = list_item_array.length;

        for (var i = 0; i < list_item_array_length; i++) {


            if( typeof( list_item_array[i] ) !== "undefined" ) {

                temp = document.createElement("p");
                temp.innerHTML = event[list_item_array[i]];
                list_item.appendChild(temp);
            }
        }

        list_item.style.color = event.color;

        fragment.appendChild(list_item);

        _list_container.appendChild(fragment);

    });

    var _ajax_update_wordpress_transient_cache = (function( AjaxEvents ) {

        /*var ajax_update_wordpress_transient_cache = function( options ) {

            // Assign handlers immediately after making the request,
            // and remember the jqxhr object for this request
         var jqxhr = jQuery.post(
         options.ajax_url,
         {
         action:         "update_wordpress_transient_cache",
         attr_id:        options.attr_id,
         nonce:          jQuery("#" + options.attr_id).data("nonce"),
         transient_name: options.transient_name,

         start:          options.start,
         end:            options.end,
         calendars:      options.calendars,
         limit:          options.limit
         },

                function( data ) {
                */
        var ajax_options = {};

        ajax_options.ajax_url = options.ajax_url;
        ajax_options.attr_id = options.id;

        var days = document.querySelectorAll( '.fc-day:not(.fc-other-month)' );

        ajax_options.start = days[0].getAttribute('data-date');
        ajax_options.end = days[days.length - 1].getAttribute('data-date');
        ajax_options.calendars = 0;
        ajax_options.limit = 0;


        console.log('ajax_url: ' + ajax_options.ajax_url);
        console.log('attr_id: ' + ajax_options.attr_id);
        console.log('start: ' + ajax_options.start);
        console.log('end: ' + ajax_options.end);
        console.log('calendars: ' + ajax_options.calendars); //look for the categories in the EOAjaxFront object
        console.log('limit: ' + ajax_options.limit); //limit of 0 or less will be ignored

        console.log(_get_calendars_as_string( eventorganiser ));

        /*options.ajax_url,

            transient_name: options.transient_name,

            start:          options.start,
            end:            options.end,
            calendars:      options.calendars,
            limit:          options.limit
            */


        AjaxEvents.ajax_update_wordpress_transient_cache( ajax_options );

    });

    var _get_calendars_as_string = (function ( eventorganiser ) {

        var categories = eventorganiser.fullcal.categories;
        var calendar_names = [];

        for (var i = 0; i < categories.length; i--) {

            console.log(categories[i].name);
            //do stuff
        }

        return 1;


    });

    var run_once_per_calendar = function( calendar_name ) {

        if( _calendar_name !== calendar_name ) {

            _eo_fullcalendar = document.querySelector('.eo-fullcalendar');

            _create_list_container();

            _calendar_name = calendar_name;

            _ajax_update_wordpress_transient_cache( AjaxEvents );
        }
    }

    var run_each_event = function( event ) {

        _create_list_item( event );
    }

    return {
        run_once_per_calendar: run_once_per_calendar,
        run_each_event: run_each_event
    };

})(options, AjaxEvents, eventorganiser );


//var AjaxEvents;// =

/*
 jQuery('.eo-fullcalendar').on('click', function() {

 jQuery(this).hide();
 });
 */

window.wp.hooks.addFilter( 'eventorganiser.fullcalendar_render_event', function( bool, event, element, view ){

    console.log(bool);
    //console.log(event);
    //console.log(element);
    //console.log(view);

    //pass in the title of the calendar. if it changes, we update.
    AjaxFullCalendarList.run_once_per_calendar( document.querySelector('.fc-header-title').innerHTML );
    AjaxFullCalendarList.run_each_event( event );

    return bool;
}, 4 );


/*
 so, we need a run_once function


 Run_once removes .whatever_name_of_list

 Run_once adds .whatever name of list

 Run_once adds listeners to click buttons
 Run_once adds listeners to events so that they can open and close.


 Run_once sets title. (for now)  or day or whatever.



 */

/*
 and we need a Run_for_each_event function.

 Run_for_each_event generates event template and adds it to list.

 */
