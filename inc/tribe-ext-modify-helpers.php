<?php
if ( ! defined( 'ABSPATH' ) ) exit;
//helpers
//thanks to: https://www.ict4g.net/adolfo/notes/2015/07/04/determing-url-of-caldav.html
// https://support.google.com/calendar/answer/37648?vid=0-635763762452997489-1595326527
//  - iCal requires a date format of "yyyymmddThhiissZ". The "T" and "Z"
//    characters are not placeholders, just plain ol' characters. The "T"
//    character acts as a delimeter between the date (yyyymmdd) and the time
//    (hhiiss), and the "Z" states that the date is in UTC time. Note that if
//    you don't want to use UTC time, you must prepend your date-time values
//    with a TZID property. See RFC 5545 section 3.3.5
//

//set wp_mail for HTML support
if( ! function_exists( 'texmod_set_html_mail_content_type' ) ) : 
    function texmod_set_html_mail_content_type() 
    {
    
        return 'text/html';
    }
endif;
/**
  * Description: Export buttons will open in a new window
  *
  * Plugins:      The Events Calendar
  * Author:       https://andrasguseo.com/gist/export-buttons-will-open-in-a-new-window/
  * @since   1.0.3
  *
  */
function texmod_export_buttons_open_in_new_window( $calendar_links ) 
{
  
    return str_replace( '<a ', '<a target="_blank" ', $calendar_links );
}
add_filter( 'tribe_events_ical_single_event_links', 'texmod_export_buttons_open_in_new_window' );

/**
 * Path helpers for urls
 * 
 * @since 1.0.2
 */
    function texmod_get_ID_by_slug($page_name) 
    {

        global $wpdb, $page_name;
        $page_name_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts 
                                        WHERE post_name = '".$page_name."'");
            return $page_name_id;
    } 
    
    function texmod_get_page_by_slug($slug) 
    {

        $page_url_id = get_page_by_path( $slug );
        $page_url_link = get_permalink($page_url_id);
            return $page_url_link;
    } 
    
    function texmod_get_parent_bycat($post)
    {

        if ($post->post_parent)	{
            $ancestors=get_post_ancestors($post->ID);
            $root=count($ancestors)-1;
            $parent = $ancestors[$root];
        } else {
            $parent = $post->ID;
        }
            return $parent;
    }
//CLIENT_ID, CLIENT_SECRET, API_KEY are required for synchronization first. 
/**
 * Easy convert to google calendar formating of dates string.
 *          events that are not entire days
 *         (format: 2006-07-04T18:00:00.000-07:00)
 * 
 * @uses   \Tribe__Events__Timezones::event_start_timestamp() 
 * @uses   ISO8601
 * @return $dates string
 * @since  1.0.2
 */
function texmod_get_timestamp( $post_id, $times=array() ) 
{
        $end = $start = $endDate = $startDate = '';
    $end = sprintf(
        '%s %s',
        get_post_meta( $post_id, "_EventEndDate", true ),
        Tribe__Events__Timezones::get_event_timezone_string( $post_id )
    );
    $start = sprintf(
        '%s %s',
        get_post_meta( $post_id, "_EventStartDate", true ),
        Tribe__Events__Timezones::get_event_timezone_string( $post_id )
    );
    //$start = strtotime($start);
    //$end   = strtotime($end);
    
        $startDateTime = new DateTime($start);
        $startDate     = $startDateTime->format(DateTime::ISO8601);
        $endDateTime   = new DateTime($end);
        $endDate       = $endDateTime->format(DateTime::ISO8601);
        
        //strip colons and dashes
        $start = preg_replace('/-|:/', null, $startDate);
        $end   = preg_replace('/-|:/', null, $endDate);
            
            //remove last four elements (seconds)
            return substr( $start, 0, -4).'/'.substr( $end, 0, -4 );
}        
/**
 * Output a Google Calendar string
 * 
 * @param $params string|array default TEC calendar protocol handlers
 * @param $dates  string       TEC global meta-key
 * https://www.google.com/calendar/event?action=TEMPLATE&text=%s&dates=%s&details&location&trp=%s&sprop=%s&ctz=%s
 * @since 1.0.2
 */
function texmod_parse_google_url()
{
    global $post, $dates, $event, $wpdb;
$gcaluri = $location = $dates = $timezone = '';
$param = array();
    
    //could use _VenueAddress
    $location = get_post_meta( $post->ID, '_VenueAddress', true );
		if ( empty( $location ) ) { $location = ''; }
    $dates = texmod_get_timestamp($post->ID, $times );

    //can comment out next line
        if( empty( $dates ) ) { $dates = '20190101T130000/20190101T140000'; }
    // If we have a good timezone string we setup it; UTC doesn't work on Google
    $timezone = Tribe__Events__Timezones::get_event_timezone_string( $post->ID );
    if ( false !== $timezone ) {
        $param['ctz'] = urlencode( $timezone );
    }
$param = array(
'action'   => 'TEMPLATE',
'text'     => urlencode( strip_tags( $post->post_title ) ),
'dates'    => $dates,
'details'  => urlencode( strip_tags( $post->post_title ) ),
'location' => urlencode( $location ),
'trp'      => 'false',
'sprop'    => 'website:' . home_url(),
'ctz'      => urlencode( $timezone )
);
/**
 * url string for public side events only
 */
ob_start();
printf( 'https://www.google.com/calendar/event?action=TEMPLATE&text=%s&dates=%s&details&location&trp=%s&sprop=%s&ctz=%s',

$param['text'],
$param['dates'],
$param['trp'],
$param['sprop'],
$param['ctz']
);
$gcaluri = ob_get_clean();
return $gcaluri;
} 


/**
 * Output a Google Calendar string for admin editor
 * 
 * @param $params string|array default TEC calendar protocol handlers
 * @param $dates  string       TEC global meta-key
 * https://www.google.com/calendar/event?action=TEMPLATE&text=%s&dates=%s&details&location&trp=%s&sprop=%s&ctz=%s
 * 
 * @since 1.0.33
 */
function texmod_parse_editor_google_url($post)
{
    global $post, $dates, $event;
    $gcaluri = $location = $dates = $timezone = '';
    $params = array();
    //could use _VenueAddress
    $location = get_post_meta( $post->ID, '_VenueAddress', true );
		if ( empty( $location ) ) { $location = ''; }
    $dates = texmod_get_timestamp($post->ID, $times );

    //can comment out next line
        if( empty( $dates ) ) { $dates = '20190101T130000/20190101T140000'; }
    // If we have a good timezone string we setup it; UTC doesn't work on Google
    $timezone = Tribe__Events__Timezones::get_event_timezone_string( $post->ID );
    if ( false !== $timezone ) {
        $params['ctz'] = urlencode( $timezone );
    }

    ob_start();
$tribe_excerpt = do_shortcode('[tribe_event_inline id="'. $event_id . '"]{excerpt}[/tribe_event_inline]');

$params = array(
    'action'   => 'TEMPLATE',
    'text'     => urlencode( trim( $post->post_title ) ),
    'dates'    => $dates,
    'details'  => wp_trim_words( $tribe_excerpt ), //urlencode( trim( $post->post_title ) ),
    'location' => urlencode( $location ),
    'trp'      => 'false',
    'sprop'    => 'website:' . home_url(),
    'ctz'      => urlencode( $timezone )
);

printf( 'https://www.google.com/calendar/event?action=TEMPLATE&text=%s&dates=%s&details=%s&location=%s&trp=%s&sprop=%s&ctz=%s',

$params['text'],
$params['dates'],
$params['details'],
$params['location'],
$params['trp'],
$params['sprop'],
$params['ctz']
);
$gcaluri = ob_get_clean();
return $gcaluri;
} 
/**
 * Fires after the post time/date setting in the Publish meta box.
 *
 * @since 1.0.2
 * @since 1.0.3 Added the `$post` parameter.
 *
 * @param WP_Post $post WP_Post object for the current post.
 */
add_action( 'post_submitbox_misc_actions', 'custom_button' );
add_action( 'post_submitbox_misc_actions', 'tribe_extends_modify_custom_button' );
function tribe_extends_modify_custom_button( $post, $pagenow ) 
{

    global $pagenow;
    if ( 'post.php' == $pagenow) {
    $post_type = get_post_type($_GET['post']);
     
    // Show only for published pages.
    if ( ( 'tribe_events' == $post_type ) && ( get_post_status( $post->ID ) === 'publish' ) )  {
        $gcaluri  = texmod_parse_editor_google_url($post); 
        ob_start();
        printf( '<fieldset class="misc-pub-section"><label>Update Google Calendar too!</label>
    <a id="%s" class="button-primary" accesskey="p" tabindex="5" href="%s" title="%s" target="_blank" 
    type="text/calendar">%s</a>
    <br><small style="background:#ffa;font-size:smaller">On Google <em style="color:red">decide if</em><strong> All Day Event is needed, or not</strong></small>
            </fieldset>',    
               esc_attr( 'icalEvent' ),
               esc_url_raw( $gcaluri ),
                __( 'send to google calendar' ),
                __( 'Save to Google Calendar' )
    );    
     
      $html = ob_get_clean();
        echo $html;
    }
    }

}

/**
 * Gcal/ICS ics formatted for iPhone on GCalendar 
 * Calendar-id 	STRING@gmail.com
 * Main url 	https://www.google.com/calendar/dav
 * CalDAV url 	https://www.google.com/calendar/dav/STRING@gmail.com
 * ICS 	https://www.google.com/calendar/ical/USERNAME/private-SOMESTRING/basic.ics
 * https://www.google.com/calendar/ical/'. $current_user->display_name . '-'. $pageclean .'/basic.ics"
 */ 
function texmod_parse_gcaliPhone_url()
{

    global $current_user;
    $disp_name = 'EventsCalendar'; $blogname = '';
    //$curent_user = wp_get_current_user();
    //$disp_name = $current_user->display_name;
    $blogname     = ( empty( get_bloginfo( 'name' ) ) ) 
                    ? 'Public Calendar' : get_bloginfo( 'name' );
ob_start();
printf( 'https://www.google.com/calendar/ical/%s/private-%s/basic.ics',
$current_user->display_name,
 urlencode( sanitize_title_with_dashes( $blogname ) ) 
);
$output = ob_get_clean();
        return $output;
}

/*    
function icalfeeds_autodiscover_tag() {
//using server address or the principal url for test/discovery
print('<link href="' . home_url('/?ical') . '" rel="alternative" type="text/calendar">');
}
add_action('wp_head', 'icalfeeds_autodiscover_tag');
*/   

// Usage: [upcoming_events limit="6"]
function texmod_upcoming_events_shortcode($atts)
{
     
    extract( shortcode_atts( array(
    		'limit' => 6,
    	), $atts ) );	
     
        $upcoming_events = tribe_get_events(array( 'posts_per_page'=>$limit, 
                                                   'eventDisplay'=>'upcoming') 
                                                );
    	$i=0;
    	$result='<div id="event-box"><ul>';
     
    	foreach ( $upcoming_events as $post )
    	{
    		$sdate = date('n/j', strtotime( tribe_get_start_date($post->ID, true, 'm/j') ) );
    		$stime = tribe_get_start_date( $post->ID, false, 'g:i a' );
    		$etime = tribe_get_end_date( $post->ID, false, 'g:i a' );			
     
    		$result .= '<li><h4><a class="blacklink" href="' . get_permalink($post->ID) . '">';
    		if( $stime == '12:00 am' ){
    			$result .= $sdate; //all day event
    		}else if( $stime != $etime ){
    			$result .= $sdate . ' <span class="time">@ ' . $stime . ' - ' . $etime . '</span>';
    		}else{
    			$result .= $sdate . ' <span class="time">@ ' . $stime . '</span>';
    		}
     
    		$result .= '</a></h4><p>';
    		$title = $post->post_title;
    		$result .= substr($title, 0, 65);
    		if (strlen($title) > 65) $result .= " ...";
    		$result .= '</p>';
     
    		if( ($i%$limit) == 0 && $i>0) { $result .= '</li>'; }
    		$i++;
    	}//end foreach
    	$result .= '</ul></div><!-- end #event-box -->';
    	return $result;
}
