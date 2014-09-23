<?php
/**
 * Created by PhpStorm.
 * User: Paul
 * Date: 13/06/14
 * Time: 10:15 PM
 */

// Extend the class
class Manage_Facebook_Events extends AdminPageFramework {

    private $section_id = "modify";

    /**
     * Framework method that registers all of our custom fields
     * Needed the DateTime fields to modify the date of an event.
     *
     * @remark  this is a pre-defined framework method
     *
     * @since    0.6.0
     */
    public function start_Manage_Facebook_Events() { // start_{extended class name} - this method gets automatically triggered at the end of the class constructor.
        /*
         * Register custom field types.
         */

        /* 1. Include the file that defines the custom field type. */
        $aFiles = array(

            dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/admin-page-framework/third-party/date-time-custom-field-types/DateCustomFieldType.php',
            dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/admin-page-framework/third-party/date-time-custom-field-types/TimeCustomFieldType.php',
            dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/admin-page-framework/third-party/date-time-custom-field-types/DateTimeCustomFieldType.php',
        );

        foreach( $aFiles as $sFilePath )
            if ( file_exists( $sFilePath ) ) include_once( $sFilePath );

        /* 2. Instantiate the classes  */
        $sClassName = get_class( $this );
        new DateCustomFieldType( $sClassName );
        new TimeCustomFieldType( $sClassName );
        new DateTimeCustomFieldType( $sClassName );
    }

    /**
     * Framework method that generates a form
     * I've been fighting with this for quite a long time.  It does what it's designed to do
     * very easily, but changes you want to make to it are kind of tricky.
     * Story of all frameworks, I guess.
     * Anyway, the form generated by this method allows us to modify our FB events.
     *
     * @remark  this is a pre-defined framework method
     *
     * @since    0.9.0
     */
    public function setUp() {

        // Creates the root menu
        //$this->setRootMenuPage( 'Events' );    // specifies to which parent menu to add.
        $this->setRootMenuPageBySlug( 'edit.php?post_type=event' );

        // Adds the sub menus and the pages
        $this->addSubMenuItems(
            array(
                'title'    =>    'Manage Facebook Events',    // page and menu title
                'page_slug'    =>    'manage_facebook_events_page'     // page slug
            )
        );

        $this->addSettingSections(
            'manage_facebook_events_page',
            array(
                'section_id' => $this->section_id,
                'title' => 'Modify Facebook Events',
                'description' => 'Modify output of Facebook events on the calendar.',
            )
        );

        $this->addSettingFields(
            $this->section_id,
            array(
                'field_id'	=>	'eid',
                'type'	=>	'hidden',
                'attributes'	=>	array(
                    'class'     => $this->section_id . '_eid',
                    'fieldrow'	=>	array(
                        'style'	=>	'display: none; clear: none;',
                    ),
                ),
                //'show_title_column' => false,
            ),
            array(
                'field_id'	=>	'removed',
                'type'	=>	'hidden',
                'attributes'	=>	array(
                    'class'     => $this->section_id . '_removed',
                    'fieldrow'	=>	array(
                        'style'	=>	'display: none; clear: none;',
                    ),
                ),
            ),
            array(
                'field_id'	=>	'modified',
                'type'	=>	'hidden',
                'attributes'	=>	array(
                    'class'     => $this->section_id . '_modified',
                    'fieldrow'	=>	array(
                        'style'	=>	'display: none; clear: none;',
                    ),
                ),
            ),
            array(	// Read-only
                'field_id'	=>	'name',
                'title'	=>	'Event Name',
                'type'	=>	'text',
                'value'	=>	'',
                //'description'	=>	'Original Name'
                'attributes'	=>	array(
                    'class'     => $this->section_id . '_name',
                    'readonly'	=>	'ReadOnly',
                    // 'disabled'	=>	'Disabled',		// disabled can be specified like so
                ),

            ),
            array(	// Multiple text fields
                'field_id'	=>	'host',
                'title'	=>	'Event Host',
                'help'	=>	'Modify the host name.',
                'type'	=>	'text',
                //'default'	=>	'placeholder old host',
                'label'	=>	'Original Host:',
                'attributes'	=>	array(
                    'class'     => $this->section_id . '_host_fb',
                    'value'	=>	'',
                    'readonly'	=>	'ReadOnly',
                ),
                'delimiter'	=>	'&nbsp;&nbsp;&nbsp;&nbsp;',
                array(
                    'label'	=>	'New Host: ',
                    //'field_id' => 'host',
                    'attributes'	=>	array(
                        'class'     => $this->section_id . '_host',
                        'readonly'	=>	false,
                        'value'     =>  ''
                    )
                ),
                //'description'	=>	'Modify the host.'
            ),
            array(	// Multiple text fields
                'field_id'	=>	'location',
                'title'	=>	'Event Location',
                'help'	=>	'Modify the location of the event.',
                'type'	=>	'text',
                'label'	=>	'Original Location:',
                'attributes'	=>	array(
                    'class'     => $this->section_id . '_location_fb',
                    'value'	=>	'',
                    'readonly'	=>	'ReadOnly',
                ),
                'delimiter'	=>	'&nbsp;&nbsp;&nbsp;&nbsp;',
                array(
                    'label'	=>	'New Location: ',
                    'attributes'	=>	array(
                        'class'     => $this->section_id . '_location',
                        'readonly'	=>	false,
                        'value'     =>  ''
                    )
                ),
            ),
            array(	// Multiple date pickers
                'field_id'	=>	'start_time',
                'title'	=>	'Event Date',
                'help'	=>	'Modify the date of the event.',
                'type'  => 'date_time',
                'date_format'	=>	'yy-mm-dd',
                'time_format'	=> 'HH:mm',
                'label'	=>	'Original Date: ',
                'attributes'	=>	array(
                    'class'     => $this->section_id . '_start_time_fb',
                    'value'	=>	    '',
                    'readonly' =>   'ReadOnly',
                    'size' =>       '30',
                ),
                'delimiter'	=>	'&nbsp;&nbsp;&nbsp;&nbsp;',
                //'delimiter'	=>	'<br />',
                array(
                    'label'	=>	'New Date: ',
                    'attributes'	=>	array(
                        'class'     => $this->section_id . '_start_time',
                        'readonly'	=>	false,
                        'value'     =>  '',
                        'size' =>       '30',
                    )
                ),
            ),
            /**
             * @TODO: the old ticket value (if there is no new ticket) shows up in the new ticket slot
             * Everything seems to work okay, just that it doesn't do what you might expect.
             */
            array(	// Multiple text fields
                'field_id'	=>	'ticket_uri',
                'title'	=>	'Event Ticket URI',
                'help'	=>	'Add or update the ticket uri for an event.',
                'type'	=>	'text',
                'label'	=>	'Original Ticket URI: ',
                'attributes'	=>	array(
                    'class'     => $this->section_id . '_ticket_uri_fb',
                    'value'	=>	'',
                    'readonly'	=>	'ReadOnly',
                ),
                'delimiter'	=>	'&nbsp;&nbsp;&nbsp;&nbsp;',
                array(
                    'label'	=>	'New Ticket URI: ',
                    'attributes'	=>	array(
                        'class'     => $this->section_id . '_ticket_uri',
                        'readonly'	=>	false,
                        'value'     =>  ''
                    )
                ),
            ),
            array(
                'field_id'	=>	'modify_events_submit',
                'section_id' => 'modify',
                'type'	    =>	'submit',
                'title'		=>	'Modify Event',
                'label'     =>  'Modify Event',
                'attributes'	=>	array(
                    'class'	=>	'button button-primary',
                    'title'	=>	'Modify Event',
                    //'style'	=>	'background-color: #C1DCFA;',
                ),
                array(
                    'title'	=>	'Reset Modifications',
                    'label'	=>	'Reset Modifications',
                    'attributes'	=>	array(
                        'class'	=>	'button button-secondary',
                        'title'	=>	'Reset Modifications',
                        //'style'	=>	'background-color: #C8AEFF;',
                    ),
                ),
                'show_title_column' => false,
            )
        );

        //reset the default object caching value because so we don't unintentionally BUGGER UP SOMEONE ELSE'S PLUGIN
        \USC_FB_Events\WP_AJAX::get_instance()->turn_object_caching_back_on_for_the_next_poor_sod();
    }

    /**
     * Framework method that prints some HTML BEFORE the form generated by the ::addSettingFields method
     * Prints out the list and inputs for our filter.js plugin.  Events loaded in this table will be
     * able to be searched and filtered.
     *
     * @remark  this is a pre-defined framework method
     *
     * @param   string $sContent   whatever else is supposed to be on the page.  The title, for example.
     *
     * @since    0.9.0
     */
    public function content_foot_manage_facebook_events_page( $sContent ) {

        echo $sContent;  //this is the title of the page set in the ::addSubMenuItems method above.
        ?>

        <div id='filterjs__notice'></div>

        <?php
        $date_year = date('Y');

        //start and end for the current year
        $start = strtotime( $date_year . '-01-01');
        $end = $start + YEAR_IN_SECONDS - 1;

        //start and end for previous year
        $start_prev = $start - YEAR_IN_SECONDS;
        $end_prev = $start - 1;

        //start and end for next year
        $start_next = $start + YEAR_IN_SECONDS;
        $end_next = $start_next + YEAR_IN_SECONDS - 1;
        ?>
        <div class="title_row clearfix cf">
            <h3 class="title">Facebook Events for
                <span id="ajax__year" class="title__year" data-start="<?php echo $start; ?>" data-end="<?php echo $end; ?>"
                      data-year_in_seconds="<?php echo YEAR_IN_SECONDS; ?>">
                    <?php echo $date_year; ?></span>
            </h3>
            <div class="title__navigation_buttons">
                <?php
                $this->echo_button("<< Prev Year", "prev_year_button", 05, '', array( 'data-direction' => -1 ) );
                $this->echo_button("Next Year >>", "next_year_button", 10, '', array( 'data-direction' => +1 ) );
                ?>
            </div>
        </div>

        <div class="filterjs">
            <div class="filterjs__filter">
                <div class="filterjs__filter__search__wrapper">
                    <h4>Search with filter.js</h4>
                    <input type="text" id="search_box" class="searchbox" placeholder="Type here...."/>
                </div>
                <div class="filterjs__filter__checkbox__wrapper">
                    <h4>Filter by Status</h4>
                    <ul id="removed">
                        <li>
                            <input id="display" value="display" type="checkbox">
                            <span>Display</span>
                        </li>
                        <li>
                            <input id="modified" value="modified" type="checkbox">
                            <span>Modified</span>
                        </li>
                        <li>
                            <input id="removed" value="removed" type="checkbox">
                            <span>Removed</span>
                        </li>
                    </ul>
                </div>
                <span class="event_list__counter__container"><span class="event_list__counter">no events</span></span>
            </div>
            <div class="filterjs__list__wrapper">
                <div class="filterjs__loading filterjs__loading--list">
                    <img class="filterjs__loading__img" title="go mustangs!"
                         src="/wp-content/plugins/usc-fb-events/assets/horse.gif" alt="Loading" height="91" width="160">
                </div>
                <div class="filterjs__list__crop">
                    <div class="filterjs__list" id="event_list" data-nonce="<?php echo wp_create_nonce("event_list_nonce"); ?>"></div>
                </div>
            </div>
            <div class="clearfix cf"></div>
            <span class="event_list__counter__container"><span class="event_list__counter">no events</span></span>
        </div>

        <?php

        $this->echo_button("Remove Event", "remove_event_button", 25);
        $this->echo_button("Display Event", "display_event_button", 30);
        $this->echo_button("Modify Event", "modify_event_button", 35);

        echo "<hr>";


    }

    /**
     * Framework method that prints some HTML AFTER the form generated by the ::addSettingFields method
     * Prints a title and an error array right now.  Probably we don't need this in production.
     *
     * @remark  this is a pre-defined framework method
     *
     * @since    0.9.0
     *
    public function do_manage_facebook_events_page() {

    //this is the end of the form defined in ::addSettingFields
    ?>

    <h3 class="title">Values saved</h3>

    <?php

    echo $this->oDebug->getArray( get_option( 'Manage Facebook_Events' ) );

    }

    /**
     * Utility method that prints some javascript in the footer.
     * The reason it's here is because it directly relates to something on this page: the datepickers
     * The top datepicker shouldn't be accessible but we still want its value submitted when the form is submitted
     *
     * @remark  this is a pre-defined framework method
     *
     * @since    0.6.0
     */
    public function do_after_manage_facebook_events_page() {

        echo "
            <script>
			jQuery( document ).ready( function($){

				    $('.hasDatepicker').datepicker( 'disable' );

				    $('.hasDatepicker').on( 'update', function () {

                       $(this).datepicker( 'disable');
			           $(this).datepicker( 'setDate', new Date( parseInt( $(this).val() ) * 1000 ) );
			           $('.hasDatepicker').last().datepicker( 'enable' );

				    } );

				    $('#modify_event_submit').on('click', function() {
				        $('.hasDatepicker').datepicker( 'enable' );
				    });

			});
			</script>
		" . PHP_EOL;
    }

    /**
     * Heavyweight method that validates the Modify Events form and then acts on what it finds.
     * If successful, it will either update the values for a FB event in our database, or remove the modifications made.
     * If a URL fails to validate, it will return an error message to the screen and above the field itself
     *
     * @remark  this is a pre-defined framework method
     *
     * @param array $aInput       the values in the form
     * @param array $aOldInput    the values in the wp_options table before the new values overwrite them
     *
     * @since    0.9.0
     *
     * @return mixed        returns the values to the screen.  Nothing happens with them right now.
     */
    public function validation_manage_facebook_events_page( $aInput, $aOldInput ) {	// validation_{page slug}

        //$this->setSettingNotice( "</strong><p>Invalid URL: <strong></strong></p><a class='dismiss_notice' style='cursor:pointer;'>Dismiss</a>" );

        //turn off object caching because it's breaking our plugin
        \USC_FB_Events\WP_AJAX::get_instance()->turn_off_object_cache_so_our_bloody_plugin_works();

        $error_array = array();

        //@TODO: This is kind of a crude way of implementing the 'reset modifications' button
        $modify_events_submit_array = $aInput[$this->section_id]['modify_events_submit'];
        if( is_array( $modify_events_submit_array ) && isset( $modify_events_submit_array[1] )
            && isset( $aInput[$this->section_id]['eid'] ) ) {

            \USC_FB_Events\DB_API::delete_fbevent( $aInput[$this->section_id]['eid'] );

            $this->setSettingNotice( "</strong><p>Modifications made to <strong>" . $aInput[$this->section_id]['name'] . "</strong> have been reset.</p><a class='dismiss_notice' style='cursor:pointer;'>Dismiss</a>", 'updated' );

            return $aInput;
        }

        //~ROW VALUES
        $values = array(
            'eid' =>            null,
            'name' =>           null,
            'host' =>           array(), // values with an array are modifiable
            'location' =>       array(),
            'start_time' =>     array(),
            'ticket_uri' =>     array(),
        );

        //take the keys before 'modified' is added, as it doesn't belong in the loop
        $keys = array_keys($values);

        $values['modified'] = 0;

        foreach( $keys as &$key) {

            if( is_array($values[$key]) ) {

                //we only need the second element for this.
                $values[$key] = ( isset($aInput[$this->section_id][$key][1]) ) ? $aInput[$this->section_id][$key][1] : null;

                //validate URLs and set errors as needed
                if( $this->ends_with($key, "uri") || $this->ends_with($key, "url") )
                    $error_array = $this->validate_url($error_array, $key, $values[$key]);

                //if not null, then we are modifying an event of ours.
                //if modified is already true, don't reset it.
                $values['modified'] = ( $values['modified'] || ! empty($values[$key] ) ) ? 1 : 0;

            }
            else
                $values[$key] = ( isset($aInput[$this->section_id][$key]) ) ? $aInput[$this->section_id][$key] : null;
        }
        unset($key);

        $values['removed'] = ( isset($aInput[$this->section_id]['removed']) &&
            $aInput[$this->section_id]['removed'] === "removed" ) ? 1 : 0;

        /*
        echo "<pre>";
        var_dump($aInput);
        echo "///////////////////////";
        var_dump($values);
        echo "///////////////////////";
        var_dump($error_array);
        echo "</pre>";
        wp_die('dead');
        */

        if( ! empty($error_array)) {

            $this->setFieldErrors($error_array);
            $this->setSettingNotice( "</strong><p>Invalid URL: <strong>" . $values['ticket_uri'] . "</strong></p><a class='dismiss_notice' style='cursor:pointer;'>Dismiss</a>" );

            return $aInput;
        }

        //@TODO: This 'modifies' events even if there are no updated values present.
        if( isset($values['eid']) ) {

            \USC_FB_Events\DB_API::insert_on_duplicate_key_update(
                $values['eid'],
                array(
                    'removed' =>    $values['removed'],
                    'modified' =>   $values['modified'],
                    'name' =>       ( ! empty($values['name']) ) ? $values['name'] : "",
                    'host' =>       ( ! empty($values['host']) ) ? $values['host'] : "",
                    'location' =>   ( ! empty($values['location']) ) ? $values['location'] : "",
                    'start_time' => ( ! empty($values['start_time']) ) ? $values['start_time'] : "0000-00-00 00:00:00",
                    'ticket_uri' => ( ! empty($values['ticket_uri']) ) ? $values['ticket_uri'] : "",
                ));
        }




        $this->setSettingNotice( "</strong><p>Yes! The event <strong>" . $values['name'] . "</strong> has been updated.</p><a class='dismiss_notice' style='cursor:pointer;'>Dismiss</a>", 'updated' );

        return $aInput;
    }

    /**
     * Function returns true if the parameter 'haystack' ends with the parameter 'needle'
     * Ripped off of stackoverflow
     * http://stackoverflow.com/questions/834303/startswith-and-endswith-functions-in-php
     *
     * @author Salman A
     *
     * @param string $haystack  the string to search for the needle
     * @param string $needle    the string to look for
     *
     * @since    0.9.0
     *
     * @return bool         true if 'haystack' ends with 'needle'. else false.
     *
     */
    private function ends_with($haystack, $needle)
    {
        return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
    }

    /**
     * Function validates URLs.
     * If the URL fails to validate, the field id is saved in an error array along with an error message.
     * If the URL does validate, the array is returned unmodified
     *
     * @param array $error_array    an array of errors (may be empty)
     * @param string $field_id      the id of the input field with the URL
     * @param string $url           the url to be validated
     * @return array                an error array (may be empty)
     */
    private function validate_url( array $error_array, $field_id, $url ) {

        if( empty($url) )
            return $error_array;

        // 3. Check if the submitted value meets your criteria.
        if ( ! filter_var( $url, FILTER_VALIDATE_URL ) ) {

            $error_array[$field_id] = 'Not a valid URL: ' . $url;
        }

        return $error_array;
    }

    /**
     * Function is a wrapper for WordPress' 'submit_button' method
     * Generates a button.
     *
     * @param string $button_text   what the button will say
     * @param string $id            a unique id for the button
     * @param int $tab_index        order in the tab index
     * @param string $classes       additional classes (as strings)
     * @param array $data_array     additional data attributes
     *
     * @since    0.9.0
     */
    private function echo_button( $button_text, $id, $tab_index, $classes = "", array $data_array = array() ) {

        $button_text = esc_html( $button_text );
        $id = esc_attr( $id );
        $tab_index = ( is_numeric( $tab_index ) ) ? $tab_index : -1;
        $atts = array(
            'tabindex' 		=> $tab_index,
            'data-nonce' 	=> wp_create_nonce($id . "_nonce"),
            'class'			 => 'button ' . $classes,
        );

        if( !empty( $data_array ) ) {

            $prefix = 'data-';

            foreach( $data_array as $key => $val ) {

                //if the string starts with 'data-' (or some other prefix)
                $has_prefix = ( strpos($key, $prefix) === 0 );

                ( $has_prefix )
                    ? $atts[$key] = $val
                    : $atts[$prefix . $key] = $val;
            }
        }

        submit_button( $button_text, 'large', $id, false, $atts );
    }


}