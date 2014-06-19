<?php
/**
 * Created by PhpStorm.
 * User: Paul
 * Date: 18/06/14
 * Time: 11:05 AM
 */

class WP_AJAX {

    /**
     * Instance of this class.
     *
     * @since    0.9.2
     *
     * @var      object
     */
    protected static $instance = null;

    private function __construct() {

        add_action("wp_ajax_return_to_or_remove_from_calendar", array( $this, "return_to_or_remove_from_calendar" ) );
        add_action("wp_ajax_nopriv_return_to_or_remove_from_calendar", array( $this, "login_please") );

        add_action("wp_ajax_get_events", array( $this, "get_events" ) );
        add_action("wp_ajax_nopriv_get_events", array( $this, "get_events") );
    }

    /**
     * Return an instance of this class.
     *
     * @since     0.9.2
     *
     * @return    object    A single instance of this class.
     */
    public static function get_instance() {

        // If the single instance hasn't been set, set it now.
        if ( null == self::$instance ) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Adds the WordPress Ajax Library to the frontend.
     * http://code.tutsplus.com/tutorials/a-primer-on-ajax-in-the-wordpress-frontend-actually-doing-it--wp-27073
     *
     * Kind of hack-y method, it looks like to me, but -- hey -- found it online.
     *
    public function add_ajax_library() {

    $html = '<script type="text/javascript">';
    $html .= 'var ajaxurl = "' . admin_url( 'admin-ajax.php' ) . '"';
    $html .= '</script>';

    echo $html;

    } // end add_ajax_library

    /**
     * This function right here is executed when, in the "manage events" menu, someone
     * clicks the "Remove From/Return To Calendar" buttons.
     * The event is then either removed from or returned to 'test_fbevents'
     * Based on tutorial here:
     * http://wp.smashingmagazine.com/2011/10/18/how-to-use-ajax-in-wordpress/
     *
     * @since   0.4.0
     *
     * @return 		returns an encoded JSON string
     */
    public function return_to_or_remove_from_calendar() {

        $button_id = $_POST['button_id'];

        if ( !wp_verify_nonce( $_POST['nonce'], $button_id . "_nonce")) {
            exit("No naughty business please");
        }

        global $wpdb;

        //we want the id, the name, the host, and the start time
        $eid = 		$_POST['eid'];
        $name = 		$_POST['name'];
        //$start_time =   date_i18n( 'Y-m-d H:i:s', $_POST['start_time'], true ); //convert the unix timestamp to a string that SQL understands
        $response = 	false;

        if($button_id === 'remove_event_button') {

            $response = DB_API::insert_on_duplicate_key_update(
                $eid,
                array(
                    'removed' =>    1,
                    'name' =>       $name
                ));

        }
        if($button_id === 'display_event_button') {
            $fbevent_exists_unmodified_in_db = DB_API::get_unmodified_event_count_by_eid( $eid );

            $response = ( $fbevent_exists_unmodified_in_db ) ?
                DB_API::delete_fbevent( $eid ) :
                DB_API::update_fbevent( $eid, array( 'removed' => 0) );

        }

        if($response === false) {
            $result['success'] = false;
            $result['response'] = $response;
        }
        else {
            $result['success'] = true;
            $result['name'] = $name;
            $result['response'] = $response;
        }

        //this is meant to decide what to do whether the call was made from a browser, or if JS is enabled.
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode($result);
        }
        else {
            header("Location: ".$_SERVER["HTTP_REFERER"]);
        }

        die();
    }

    /**
     * Does (a bit more than) what it says on the box.  gets all facebook and db events (and then merges them)
     *
     * @since    0.5.0
     */
    public function get_events() {

        $attr_id = $_POST['attr_id'];

        if ( !wp_verify_nonce( $_POST['nonce'], $attr_id . "_nonce")) {
            exit("No naughty business please");
        }

        $whitelist = ( isset($_POST['whitelist']) ) ? $_POST['whitelist'] : false;
        $remove_events = ( isset($_POST['remove_events']) ) ? $_POST['remove_events'] : false;

        $response = $this->call_api();

        $response = $this->facebook_urls($response);

        if($whitelist)
            $response['events'] = DB_API::whitelist_array_items($response['events']);

        $response = $this->merge_fb_and_db_events($response);

        if($remove_events)
            $response = $this->remove_removed_events($response);

        //javascript CANNOT understand dates
        $response = $this->date_strings_to_timestamps($response);

        $result['response'] = $response;
        $result['success'] = ( $response === false ) ? false : true;

        echo json_encode($result);
        die();

    }

    /**
     * Bare-bones method that rejects non-logged-in users.  Used for all ajax methods.
     *
     * @since   0.4.0
     *
     * @return 		echoes a string telling non-logged in users to log in.
     */
    public function login_please() {
        echo "Log in before you do that, you cheeky monkey.";
        die();
    }

    /** NON AJAX STUFF  **/

    /**
     * Function generates a facebook URL based on a facebook eid.
     * Events in the array are given a new key ('url'): their Facebook page
     *
     * @param array $event_array  an array of events (from Facebook)
     *
     * @since    0.9.0
     *
     * @return array mixed  an array of events with Facebook urls added
     */
    public function facebook_urls( array $event_array ) {

        $total = $event_array['total'];
        $events = $event_array['events'];

        for( $i = 0; $i < $total; $i++ ) {

            $events[$i]['url'] = 'http://facebook.com/' . $events[$i]['eid'] . "/";
        }

        $event_array['events'] = $events;

        return $event_array;
    }

    /**
     * @param array $event_array  an array of events (from Facebook)
     *
     * @since    0.9.0
     *
     * @return array mixed  an array of events with Facebook urls added
     */
    public function date_strings_to_timestamps( array $event_array ) {

        $total = $event_array['total'];
        $events = $event_array['events'];

        for( $i = 0; $i < $total; $i++ ) {

            foreach($events[$i] as $key => $value) {

                //if it starts with
                if( strpos($key, "start_time") === 0 ) {
                    $events[$i][$key] = strtotime($events[$i][$key]);
                }
            }
        }

        $event_array['events'] = $events;

        /*
        echo "<pre>";
        var_dump($event_array['events'] );
        echo "</pre>";

        die();
         */

        return $event_array;
    }

    /**
     * Super handy function merges our DB event data with FB events.
     * Any conflicting values have their keys suffixed with "_fb".
     * ie, Facebook's "host" becomes "host_fb" if I have a "host" of my own.
     *
     * @param array $event_array
     *
     * @since    0.5.1
     *
     * @return array
     */
    public function merge_fb_and_db_events( array $event_array ) {

        $all_db_events_mysql = DB_API::get_fbevents();

        if( ! empty($all_db_events_mysql) ) {

            $all_db_events = array();
            $all_db_events['eid'] = array();

            foreach( $all_db_events_mysql as &$db_event ) {

                //hacky fun way changes an stdClass into an array
                array_push( $all_db_events, json_decode(json_encode($db_event), true) );
                //create an indexed 'ied' array containing only eids
                array_push( $all_db_events['eid'], $db_event->eid);

            }
            unset( $db_event );
            unset( $all_db_events_mysql );

            $total = $event_array['total'];
            for($i = 0; $i < $total; $i++) {

                //array search (should) return the index of db_events
                $db_event_index = array_search( $event_array['events'][$i]['eid'], $all_db_events['eid'] );

                if( $db_event_index !== false ) {

                    $all_db_event_array_keys = array_keys( $all_db_events[$db_event_index] );

                    //for every key in the db_event, overwrite the value in the original event
                    foreach( $all_db_event_array_keys as &$key ) {

                        //if the modifiable fields are not EMPTY -- WE CAN'T F*CKING PUT 'NULL' INTO UPDATE STATEMENTS
                        if( ! empty( $all_db_events[$db_event_index][$key] ) && $all_db_events[$db_event_index][$key] !== "0000-00-00 00:00:00" ) {

                            //if the key doesn't exist in the old value, just put it in
                            if( ! isset( $event_array['events'][$i][$key] ) )
                                $event_array['events'][$i][$key] = $all_db_events[$db_event_index][$key];

                            //if the modifiable fields are not equal to the current data
                            else if ( $event_array['events'][$i][$key] !== $all_db_events[$db_event_index][$key] ) {
                                $event_array['events'][$i][$key . "_fb"] = $event_array['events'][$i][$key];
                                $event_array['events'][$i][$key] = $all_db_events[$db_event_index][$key];
                            }
                        }
                    }

                    unset($all_db_event_array_keys);
                }
                unset($key);
            }

        }

        $sort_criteria =
            array('start_time' => array(SORT_DESC, SORT_STRING),
            );

        $event_array['events'] = $this->multisort($event_array['events'], $sort_criteria, true);

        return $event_array;
    }

    /**
     * Function takes merged event array and purges if of events flagged "removed"
     *
     * @param array $event_array  an array of events (from Facebook)
     *
     * @since    0.5.0
     *
     * @return array mixed  an array of events wherein those flagged for removal are removed
     */
    public function remove_removed_events( array $event_array ) {

        $total = $event_array['total'];
        $events = $event_array['events'];

        for( $i = 0; $i < $total; $i++ ) {

            if( isset($events[$i]['removed']) && intval( $events[$i]['removed']) === 1 )
                unset( $events[$i] );
        }

        $event_array['events'] = array_values( $events );
        $event_array['total'] = count( $events );

        return $event_array;
    }

    /**
     * Multisort function sorts two-dimensional arrays on specific keys.
     * Ripped off the PHP reference page from one of the comments.
     * http://www.php.net/manual/en/function.array-multisort.php#114076
     *
     * @author Robert C
     * C probably short for "Champ"
     *
     * @param $data                 the array to be sorted
     * @param $sortCriteria         array of selected keys and how to sort them
     * @param bool $caseInSensitive whether or not to sort stings by case
     *
     * @since    0.5.0
     *
     * @return bool|mixed           returns your array sorted by whatever the eff you asked for
     */
    public function multisort($data, $sortCriteria, $caseInSensitive = true)
    {
        if( !is_array($data) || !is_array($sortCriteria))
            return false;
        $args = array();
        $i = 0;
        foreach($sortCriteria as $sortColumn => $sortAttributes)
        {
            $colList = array();
            foreach ($data as $key => $row)
            {
                $convertToLower = $caseInSensitive && (in_array(SORT_STRING, $sortAttributes) || in_array(SORT_REGULAR, $sortAttributes));
                $rowData = $convertToLower ? strtolower($row[$sortColumn]) : $row[$sortColumn];
                $colLists[$sortColumn][$key] = $rowData;
            }
            $args[] = &$colLists[$sortColumn];

            foreach($sortAttributes as $sortAttribute)
            {
                $tmp[$i] = $sortAttribute;
                $args[] = &$tmp[$i];
                $i++;
            }
        }
        $args[] = &$data;
        call_user_func_array('array_multisort', $args);
        return end($args);
    }

    /**
     * Calls some page which calls our Facebook events api
     *
     * @since    0.2.0
     *
     * @return array       at this point, return open Facebook events as an indexed array
     */
    public function call_api() {

        //the url where to get Facebook events
        $ch = curl_init('testwestern.com/api/events/events/2014-04-01');

        curl_setopt( $ch, CURLOPT_HEADER, false ); //TRUE to include the header in the output.
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true ); //TRUE to return transfer as a string instead of outputting it out directly.
        //curl_setopt($ch, CURLOPT_FRESH_CONNECT, true); //TRUE to force the use of a new connection instead of a cached one.

        $returnedString = curl_exec( $ch );
        curl_close( $ch );

        // Define the errors.
        /* $constants = get_defined_constants(true);

        /*$json_errors = array();
        foreach ($constants["json"] as $name => $value) {
            if (!strncmp($name, "JSON_ERROR_", 11)) {
                $json_errors[$value] = $name;
            }
        }

        echo '<h1>';
        echo 'Last error: ', $json_errors[json_last_error()], PHP_EOL, PHP_EOL;
        echo '</h1>';
        die;
        */

        return json_decode( $returnedString, true );
    }


}