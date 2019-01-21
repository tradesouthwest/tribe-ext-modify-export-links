<?php 
/**
 * /wp-admin/admin.php?page=mailusers-send-notify-mail-post&post_id=5169
 */
//set wp_mail for HTML
if( ! function_exists( 'texmod_set_html_mail_content_type' ) ) : 
    function texmod_set_html_mail_content_type() {
        return 'text/html';
    }
    endif;
    
//form processor
function tribe_ext_modify_sendmail_formprocess()
{
    if ( $_POST['action'] && $_POST['action'] == 'texmod_transfer' )
    {
        $nonce = $_REQUEST['_wpnonce'];
        if ( ! wp_verify_nonce( $nonce, 'texmod_nonce_name' ) ) {
            exit; 
        }
        if( !empty ($_POST['event_id']) ) $event_id = $_POST['event_id'];

        //validation passed
        $date_sent = date('m-d-Y H:i:s');
        if( is_user_logged_in() ) 
        { 
            $curent_user = wp_get_current_user();
        
        $webmail_from       = santize_email( $user_email );
        $texmod_admin_email = get_option( 'admin_email' );
        $texmod_title       = get_option( 'blogname' );
    
        /** wp_mail
        * @param string|array $to Array or comma-separated list of email addresses to send message.
         * @param string $subject           Email subject
         * @param string $message           Message contents
         * @param string|array $headers     Optional. Additional headers.
         * @param string|array $attachments Optional. Files to attach.
         * @return bool                     Whether the email contents were sent successfully.
         */

        //add mail type in headers
        add_filter( 'wp_mail_content_type', 'texmod_set_html_mail_content_type' );

        $to       = $current_user->user_email;
	    $headers .= 'From:' . $texmod_title . ' <'.$webmail_from.'>'. "\n";

        $content = '<html><body style=\"font-family: sans-serif\"><br>
        <div style=\"border:1px solid #ddd;padding:7px;height:100%;width:98%\">';
        $content .= '<ul style="list-style:none">';
        // Get all events from 1 week before the present date to 1 week in the future
        // show per page are fetched, earliest first
        $events = tribe_get_events($id);
    
        // The result set may be empty
        if ( empty( $events ) ) {
            $content .= '<li>No events found for this day.</li>';
        }
    
        // we have some to show
        else {
    foreach( $events as $event ) 
    {
        $content .= '<li>'. get_the_title( $event ) . '</li>';
        $content .= '<li><a href="'. tribe_get_event_link( $event ) .'">'. tribe_get_event_link( $event ) .'</a>' . '</li>';
        $content .= '<li>'. tribe_get_start_date( $event ) . ' / '. tribe_get_end_date( $event ) . '</li>';
        $content .= '<li style="color:cyan">===================================================</li>';
    } 
        }
        $content .= '</ul>';
        $content .= '</div>';
        $content .= '<br></body></html>';

    $sendSuccess = wp_mail( $to, $subject, $content, $headers );

    if ( $sendSuccess ) {
        ?>
            <div class="row">
            <h4><?php esc_html_e('Your Information Was Sent Successfully.', 'hordes' ); ?></h4>
            <p><?php esc_html_e( 'Please check your email', 'hordes' ); ?></p>
            <p><?php printf( '&lt;%s> %s', $current_user->display_name, $current_user->user_email ); ?></p>
            </div>

            <?php
            } else {
            ?>

            <div class="row">
            <h2><?php _e('Your Information was invalid. Please re-validate.', 'hordes' ); ?></h2>
            <p><?php //echo $nameError; ?></p>
            </div>

            <?php
            }

        // Reset content-type to avoid conflicts -- https://core.trac.wordpress.org/ticket/23578
        remove_filter( 'wp_mail_content_type', 'texmod_set_html_mail_content_type' );
} else {             
            echo '<h4>' . esc_html__( 'You must be logged in to use our email services.', 
                                      'tribe-ext-remove-export-links' ) .'</h4>';
            
        }
         // ends if post[action]
    }
} 

/**
 * Shortcode [tribe_ext_modify_to_links]
 * 'export_to_csv' is ambiguous. Could export to ics, gcal, etc.
 * 
 * @param  $event_id string Id of event.
 * @uses   wp_mail; get_tribe_events;
 * @return success/error message to same page
 * @since 1.0.0

 * TODO $csv = array_map('str_getcsv', file('tec.ical'));
 */
   
function tribe_ext_create_email_file_output()
{    
    global $current_user;
    $curent_user = wp_get_current_user();


/**
 * Form processing to send email of events
 * 
 * Page is displayed and short form created above events listed.
 * Shortform is submit only w/hidden data fields.
 * TODO possible alt email input (security risk on frontside posting!)
 * 
 * @param string|input $event_id Gets event id from button links.
 * @uses tribe_get_events        https://theeventscalendar.com/function/tribe_get_events/
 * @return POST['data'] to wp_mail
 */   

    if ( $_POST['action'] && $_POST['action'] === 'texmod_transfer' )
    {
    if( !empty ($_POST['event_id']) ) $event_page     = $_POST['event_id'];
    if( !empty ($_POST['event_title']) ) $event_title     = $_POST['event_title'];
    if( !empty ($_POST['texmod_custom_email']) ) $custom_email = $_POST['texmod_custom_email'];

/**
 * Validation passed and we have data
 * @uses wp_get_current_user
 * @since 1.0.0
 */        
        //$webmail_from       = santize_email( $user_email );
        $texmod_admin_email = get_option( 'admin_email' );
        $valid_texmod_title = get_option( 'blogname' );
    
        /** 
         * wp_mail
         * @param string|array $to Array or comma-separated list of email addresses to send message.
         * @param string $subject Email subject
         * @param string $message Message contents
         * @param string|array $headers Optional. Additional headers.
         * @param string|array $attachments Optional. Files to attach.
         * @return bool Whether the email contents were sent successfully.
         */

        //add mail type in headers
        add_filter( 'wp_mail_content_type', 'texmod_set_html_mail_content_type' );

        $to       = $custom_email;
        $to       = sanitize_email($to);
        $headers .= 'From:' . $valid_texmod_title . ' <'.$texmod_admin_email.'>'. "\n";
        $subject  = 'New Event Calendar from' . $valid_texmod_title;
        $context  = '<html><body style=\"font-family: sans-serif\">
                    <div style=\"border:1px solid #ddd;padding:7px;height:100%;width:98%\">';
        $context .= '<h2>New Event Calendar ' . $valid_texmod_title . '</h2>';
        $context .= '<p><strong>' . $event_title . '</strong></p>';
        $context  .= '<p><a href="' . $event_page . '" title="' . $event_page .'" target="">' 
                     . $event_page . '</a></p>';
        $context .= '<hr>';
        $context .= '</div>';
        $context .= texmod_upcoming_events_shortcode();
        $context .= '<br></body></html>';

    $sendSuccess = wp_mail( $to, $subject, $context, $headers );

    if ( $sendSuccess ) {
        ?>
            <div class="row">
            <h4><?php esc_html_e('Your Information Was Sent Successfully.', 'hordes' ); ?></h4>
            <p><?php esc_html_e( 'Please check your email', 'hordes' ); ?></p>
            <p><?php printf( '&lt;%s> %s', $current_user->display_name, $custom_email ); ?></p>
            </div>

            <?php
            } else {
            ?>

            <div class="row">
            <h2><?php _e('Your Information was invalid. Please re-validate.', 'hordes' ); ?></h2>
            <p><?php //echo $nameError; ?></p>
            </div>

            <?php
            }

        // Reset content-type to avoid conflicts -- https://core.trac.wordpress.org/ticket/23578
        remove_filter( 'wp_mail_content_type', 'texmod_set_html_mail_content_type' );
    // ends if post[action]
    }


    /**
     * ********************************************
     * Retreive Links from buttons REQUEST
     * ********************************************
     * 
     * Form which shows at top of shortcoded page.
     * Submits to wp_email above.
     */
    // if(!defined ( 'DATE_ICAL' ) ) define( 'DATE_ICAL', 'Ymd\THis' );
    if( !empty ($_GET['icalOutput']) ) 
    {
        $validate  = $_GET['icalOutput'];
        //do nonce here
        $page_slug = ( empty( $_GET['schedule'] ) ) ? get_home_url() : $_GET['schedule'];
        if( is_user_logged_in() ) { 
            $curent_user   = wp_get_current_user();         
            $page_to_email = esc_url( $_GET['schedule'] );
            $event_title   = esc_html( $_GET['title'] );
            //preg_replace('/[event]/[a-zA-Z0-9._-]/', '', $page_toemail);
    ?>

    <div style="border:1px solid #ddd;padding:7px;height:100%">
    <form id="icalEmail-<?php print( '0' ); ?>" action="" method="POST">
    <fieldset><legend>Send Email Inspector</legend>
    <?php 
    printf( '<p>%s <em>&lt;%s> %s</p><p>%s</em></p>', 
                                    __( 'Your eMail:' ),
                                    $current_user->display_name, 
                                    $current_user->user_email,
                                    $page_to_email );
    printf( '<p>%s <em>%s</em></p>', 
                                    __( 'Event:' ),
                                    $event_title );
    ?>  
    <p><label>Change if not your email</label>
    <input type="text" name="texmod_custom_email" 
            value="<?php echo esc_attr($current_user->user_email); ?>" style="width:100%"></p>
    <p><input type="submit" name="texmod_submit" value="Looks Good Send Please"></p>
    <input type="hidden" name="action"     value="texmod_transfer">
    <input type="hidden" name="event_id" value="<?php echo esc_url( $page_to_email ); ?>">
    <input type="hidden" name="event_title" value="<?php print( $event_title ); ?>">
    <?php wp_nonce_field('texmod_sendmail_nonce' ); ?>
    </fieldset></form>

    <?php
    echo do_shortcode('[upcoming_events]');
    ?>

    </div><div class="clearfix"></div>
    
    <?php 
        } else { 
        
        echo '<h4>' . esc_html__( 'You must be logged in to use our email services.', 
                              'tribe-ext-remove-export-links' ) .'</h4>'; 
                              $page_slug = null;
                              wp_login_form();      
                              
        } 
    }
} 