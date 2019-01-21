<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * https://theeventscalendar.com/knowledgebase/tribe-settings-api/
 * https://theeventscalendar.com/knowledgebase/wordpress-post-meta-data/
 * Tribe__Events__Main::POSTTYPE
 * 
 */
//add_action( 'init', 'tribe_ext_remove_single_event_links' );
//add_action( 'init', 'tribe_ext_remove_after_footer_links' );
//remove default calendar export links 
function tribe_ext_remove_single_event_links() 
{

    remove_action( 'tribe_events_single_event_after_the_content',
        array( tribe( 'tec.iCal' ), 'single_event_links' )
    );
}
//and footer
function tribe_ext_remove_after_footer_links() 
{

    remove_filter( 'tribe_events_after_footer',
        array( tribe( 'tec.iCal' ), 'maybe_add_link' )
    );
}
//add support to query custom vars 
function texmod_add_query_vars_filter( $vars ) {
    $vars[] = "classname";
    return $vars;
}
//add_filter( 'query_vars', 'texmod_add_query_vars_filter' );

//Add the following if Outlook not saving correctly
//add_filter( 'tribe_ical_properties', 'tribe_ical_outlook_modify', 10, 2 );
function tribe_ical_outlook_modify( $content ) 
{

	$properties = preg_split ( '/$\R?^/m', $content );
	$searchValue = "X-WR-CALNAME";
	$fl_array = preg_grep('/^' . "$searchValue" . '.*/', $properties);
	$key = array_values($fl_array);
	$keynum = key($fl_array);
	unset($properties[$keynum]);
	$content = implode( "\n", $properties );
	return $content;
}

/**
 * Setup buttons to display below calendar
 * *******************************************
 * @uses shortcode: [tribe_ext_export_buttons]
 * *******************************************
 * Can be put in TEC Display after html option   
 * 
 * @param $page_slug string Retrieve current page slug from the queried object.
 * @param $event_id  string ID of post which is current day.
 * 
 * @return $gcaluri    url     External link to app.
 * @return $icaltec    url     External link to app.
 * @return $export_eml mailto: Internal link to Page with Shortcode.
 * /?post_type=tribe_events&amp;p=5019
 */	
function tribe_extention_button_footer_scripts($post)
{ 
    global $post, $current_user, $location, $dates, $page_name; //$event_id;

    // default strings
    $gmail_accnt = $page_cat = $child_page = $gcaluri = $gcaliPhone = $secret_key = $shareableLink= 
    $gcalSingleEvent = $PublicUrl = $secretAddress = $webdavGcal = $webicsGcal = '';
    $curent_user = wp_get_current_user();

    // if on the right page(s) passes
    if( is_archive('events') || tribe_is_month() && !is_tax() 
                             || tribe_is_event() && is_single() ) : 

        //attained values
        $btnstitle    = empty( get_option( 'tribe_ext_modify_options' )['tribe_ext_modify_cstitle_field'] ) 
                        ? '' : get_option( 'tribe_ext_modify_options' )['tribe_ext_modify_cstitle_field']; 
        $gcal_user    = empty( get_option( 'tribe_ext_modify_options' )['tribe_ext_modify_gcaluser_field'] ) 
                        ? 'saintsppschool' : get_option( 'tribe_ext_modify_options' )['tribe_ext_modify_gcaluser_field'];
        $maybe_single = ( tribe_is_event() && is_single() === false ) ? 'none' : 'block'; 
        $gmail_accnt  = esc_attr( $gcal_user .'@gmail.com' );
        $date_slug    = date('Y-m-d');   //shorthand to get url slug
        $permalink    = get_permalink( $post->ID );
        $long_slug    = 'events/' . $date_slug;
        $queried_objt = get_queried_object();   
        $term_slug    = $queried_objt->term_id;
        $page_slug    = ( empty( $term_slug) ) ? $date_slug : $term_slug;
        $icalfeed     = site_url() . '/?ical&posttype=tribe_events';
        $instructions = '#inStructions=show';
        $iconA         = plugin_dir_url( __FILE__ ) . 'ACalicon.png';
        //function values
        $page_id      = texmod_get_ID_by_slug( $page_name );
        $page_clean   = texmod_get_page_by_slug(sanitize_title_with_dashes( $page_name ) );
        $page_cat     = texmod_get_parent_bycat($post);
        $child_page   = texmod_get_parent_bycat($post);
        $event_title  = tribe_get_events_title();
        $dates        = texmod_get_timestamp($post->ID, $times );
        $location     = get_post_meta( $post->ID, '_VenueAddress', true );
		                if ( empty( $location ) ) { $location = ''; }
        $secret_key   = get_user_meta( $current_user->ID, 'gcalendar', true ); 
        $texmod_nonce = wp_create_nonce( 'texmod_nonce' );

$googleiPhoneApp = 'https://itunes.apple.com/us/app/google-calendar/id909319292?mt=8';
$icaltec        = get_home_url() . '/events/'. $page_slug .'?ical=1&tribe_display=month'; 
$export_tod     = get_home_url() . '/events/' . $date_slug . '/?classname=true';
//
$publicGcal     = 'https://calendar.google.com/calendar/ical'. $secret_key .'%40import.calendar.google.com/public/basic.ics';
$export_email   = get_home_url() . '/export-events-page?icalOutput=true&schedule=' . $permalink .'&title='. $event_title;
$confirmUrl     = get_home_url() . '/my-profile-page/';
$basicics       = 'https://calendar.google.com/calendar/ical/c6a5o6gnj3ejm9v21v1ppidv6um6pfo1%40import.calendar.google.com/public/basic.ics';
//$gcaluser     = $gmail_accnt .'/user';
$gcaluser       = 'https://www.google.com/calendar/dav/'. $gmail_accnt .'/user';
$altGcal        = 'https://calendar.google.com/calendar/embed?src='. $gcal_user .'%40gmail.com&ctz=America%2FChicago';
$client_ID      = '502943905467-mmj1e4kcldsl7k8ouq5g92jlpk37iegi.apps.googleusercontent.com';
$client_secret  = 'CL2ERk5mpEcVzVhFeLU_Ef38';
//maybe use
$hcoded         = 'https://calendar.google.com/calendar/ical/saintsppschool%40gmail.com/public/basic.ics';
$syncset        = 'https://calendar.google.com/calendar/syncselect';


/**
 * Shareable Link
 * delegate access to your calendar so another user in your organization can schedule and edit events. 
 * https://support.google.com/calendar/answer/151674?hl=en&ref_topic=3417969
 * https://calendar.google.com/calendar?cid=c2FpbnRzcHBzY2hvb2xAZ21haWwuY29t
 * @since 1.0.32
 */
$shareableLink  = 'https://calendar.google.com/calendar?cid=c2FpbnRzcHBzY2hvb2xAZ21haWwuY29t';

/**
 * Export link for raw data to personal GCalendar 
 * 
 * $gcalSingleEvent = 'https://www.google.com/calendar/event?action=TEMPLATE&text=%s&dates=%s&details&location=%s&trp=false&sprop=website:https://www.sspeterandpaulschool.com&ctz=America%2FChicago';
 */        
$gcalSingleEvent = texmod_parse_google_url();
//$iphoneUrl      = texmod_parse_gcaliPhone_url();
/**
 * Public Url
 * Use this URL to access this calendar from a web browser.
 * @since 1.0.33
 */
$PublicUrl      = 'https://calendar.google.com/calendar/embed?src=saintsppschool%40gmail.com&ctz=America%2FChicago';

/**
 * Public address in ical format
 * Use this address to access this calendar from other applications.
 * If you're using an Apple device, choose vCard.
 * https://support.google.com/calendar/answer/37118?hl=en&ref_topic=3417927
 * 
 * @since 1.0.32 was $gcalendars
 */
$PubliciCal     = 'https://calendar.google.com/calendar/ical/saintsppschool%40gmail.com/public/basic.ics';

/**
 * Use this private address to access this calendar from other applications without making it public.
 * Warning: Only share this address with those you trust to see all event details for this calenda
 * 
 * @since 1.0.32
 */
$secretAddress  = 'https://calendar.google.com/calendar/ical/saintsppschool%40gmail.com/private-b47da77f47508ef445030aeb28b81521/basic.ics';

/**
 * Web Dav for apple connections
 * 
 * @since 1.0.33
 */
$webdavGcal = 'webcal://calendar.google.com/calendar?cid=CL2ERk5mpEcVzVhFeLU_Ef38';

/**
 * Web ics same as ical .ics links
 * experimental
 * 
 * @since 1.0.32
 */
$webicsGcal = 'webcal://calendar.google.com/calendar?cid=c2FpbnRzcHBzY2hvb2xAZ21haWwuY29t';

ob_start();

    print ( '<div id="btnsBox" class="texmod-addon">
    <ul class="ical-list-buttons">' );
    printf( '<li class="ical-button ical-button-title">
            <a href="javascript:void();" rel="nofollow" title="%s">%s</a></li>',
                __( $btnstitle ),    
                __( $btnstitle )
    );
    //only for single event page 
    printf( '<li id="texmodSS" class="ical-button ical-button-eml" style="display:%s">
            <a id="%s" class="tecxref grayed" href="%s" title="%s">%s</a>
            </li>',    
                esc_attr( 'block' ),
                'icalToEms',
                $export_email, 
                __( 'email this page' ),
                __( 'eMail Link and Next 7 Events' )
    );
    printf( '<li class="ical-button ical-button-xls" style="display:%s">
            <a id="%s" class="tecxref" href="%s" title="%s" type="text/calendar">%s</a>
            </li>',    
                esc_attr( 'none' ),
                esc_attr( 'icalConfirm' ),
               esc_url( $confirmUrl ), 
                __( 'register for google calendars' ),
                __( 'Register to get a share key' )
    ); 
    //was shareable_link 1.0.3 now is apple calendar copy paste link 1.0.33
    printf( '<li class="ical-button ical-button-xls" style="display:%s">
    <a id="%s" class="tecxref" href="%s" title="%s" type="text/calendar" target="_blank">%s</a>
            </li>',    
                esc_attr( 'none' ),
                esc_attr( 'icalButtonA' ),
               esc_url( $secretAddress ),
                __( 'View on google calendar' ),
                __( 'Export file for G-Calendar' )
    ); 
    printf( '<li class="ical-button ical-button-xls" style="display:%s">
    <a id="%s" class="tecxref" href="%s" title="%s" type="text/calendar" target="_blank">%s</a>
            </li>',    
                esc_attr( 'block' ),
                esc_attr( 'icalButton' ),
                esc_url( $PublicUrl ),
                __( 'View on google calendar' ),
                __( 'Google Calendar' )
    ); 
    //was $altGcal 1.0.3
    printf( '<li class="ical-button ical-button-xls" style="display:%s">
    <a id="%s" class="tecxref" href="%s" title="%s" type="text/calendar" target="_blank">%s</a>
            </li>',   
                esc_attr( 'block' ), 
                esc_attr( 'icsGcal' ),
                esc_url( $webdavGcal ), 
                __( 'Send to google calendar for iphone or smartphone.' ),
                __( 'Sync to App' )
    );   
    //was $altGcal 1.0.3
    printf( '<li class="ical-button ical-button-xls" style="display:%s">
    <a id="%s" class="tecxref" href="%s" title="%s" type="text/calendar" target="_blank">%s</a>
            </li>',   
                esc_attr( 'none' ), 
                esc_attr( 'icsGcalG' ),
                esc_url( $webicsGcal ), 
                __( 'Send to google calendar for iphone or smartphone.' ),
                __( '4. Sync to App (alternate)' )
    );
    //single event only @since 1.0.32 was $maybe_single
    printf( '<li class="ical-button ical-button-xls" style="display:%s">
    <a id="%s" class="tecxref" href="%s" title="%s" type="text/calendar" target="_blank">%s</a>
            </li>',   
                esc_attr( 'none' ), 
                esc_attr( 'icsGcalS' ),
                esc_url( $gcalSingleEvent ), 
                __( 'Send to google calendar for iphone or smartphone.' ),
                __( 'Send this Single Event' )
    );   
    //instructions pullup
    printf( '<li class="ical-button ical-button-ins">
            <a id="%s" class="tecxref" href="%s" title="%s">%s</a>
            </li>', 
                'inStruct',
                $instructions, 
                __( 'tips and instructions' ),
                __( 'Instructions and Tips' )
    );

    print( '</ul></div>' );

    echo '<div id="inStructions" class="texmod-instruct" style="display:none;">
    <ul>
    <li class="ical-button ical-button-rmv"><a id="inRmv" class="tecxref" href="#btnsBox" title="close">[ - ]</a></li>
    </ul>
    <dl><dt>Email This Event Button</dt>
    <dd>This button is to be used when you are on the page you would like to email.</dd>
    <dd>Button only appears on single event pages.</dd>
    </dl>
    <dl><dt>Options for Androids</dt>
    <dd>The GooglePlay App can be found here <a href="https://play.google.com/store/apps/details?id=com.google.android.calendar">google.android.calendar</a></dd>
    <dd>Your phone may need an App to save the calendar information to.</dd>
    <dd>Reccomended Apps for SmartPhones are: <em>"Calendar Import Export"</em> and <em>"aCalendar - Android Calendar"</em></dd>
    </dl>
    <dl><dt>Options for iPhones</dt>
    <dd>Your phone may need an App to save the calendar information to.</dd>
    <dd>Also the official iPhone <a href="'. $googleiPhoneApp .'" title="google iphone app here">App for Google Calendars</a></dd>
    <dd>Other Reccomended Apps for iPhones are: <em>"iCloud"</em> and <em>"Sync for iCloud"</em></dd>
    <dd>More details about iPhones and Google Calendar <a href="https://support.google.com/calendar/answer/99358?hl=en&ref_topic=3417927">See Google Calendar events on Apple Calendar</a></dd>
    <dd>Google Calendar features that do not work on Apple Calendar a.)Email notifications for events b.)Create new Google calendars c.)Room Scheduler</dd>
<dd><strong>Alt method: </strong>
<pre style="max-width: 100%;overflow-x: auto;margin-left: 0px;font-family: inherit;padding-top: 0;margin-top: -15px">    
    1. On your computer, open Apple Calendar Apple Calendar. <img src="'. $iconA .'" height="18" width="18"/>
    2. In the top left corner of your screen, click Calendar and then Preferences.
    3. Click the Accounts tab.
    4. On the left side of the Accounts tab, click Add Add.
    5. Select Google and then Continue.
    6. To add your Google account information, follow the steps on the screen.
    7. On the Accounts tab, use "Refresh Calendars" to choose how often you want Apple Calendar and Google Calendar to sync.
</pre></dd>
    </dl>
    <dt>Import to Your Calendar</dt>
    <dd>If you need to import the events into your own personal calendar,&nbsp;<a style="color:blue" href="https://support.google.com/calendar/answer/37118?hl=en&amp;ref_topic=3417927" target="_blank" rel="noopener">follow these instructions</a></dd>
    <dd>If you need to add these calendars to your Google account,&nbsp;<a style="color:blue" href="https://support.google.com/calendar/answer/37100?co=GENIE.Platform%3DDesktop&amp;hl=en" target="_blank" rel="noopener">follow these steps.&nbsp;</a> 
     or <a href="https://support.google.com/calendar/answer/99358?co=GENIE.Platform%3DiOS&hl=en" title="apple calendar" target="_blank">Apple Calendar help</a></dd>
    <dl><dt>If eMail is not in your Inbox</dt>
    <dd>Look in your spam folder or junk email. Be sure to mark it as Not Spam once you have found it.</dd>
    </dl>
    <p>Other Options and Apps: <a href="https://support.google.com/calendar/answer/151674?hl=en&ref_topic=3417969" title="GooglePlay">Sync Calendar with a phone or tablet</a> Or check here for <a href="http://www.webcal.fi/en-US/supported_applications.php" title="desktop support" target="_blank">Desktop supported applications.</a></p>
    <p>ics or ical link: '. $PubliciCal .'</p>
    </div><div class="clearfix"></div>';

   $output = ob_get_clean();
   echo $output;
 
    endif;
} 