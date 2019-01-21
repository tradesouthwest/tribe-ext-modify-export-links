<?php
/**
 * Plugin Name:       Tribe Ext Modify Export Links
 * Plugin URI:        
 * Description:       Modifies the Default Export Links from Event Views. options in Tools > TECModifyGcal
 * Version:           1.0.4
 * 
 * GitHub Plugin URI: https://github.com/mt-support/tribe-ext-remove-export-links
 * Author:            Tradesouthwest | Modern Tribe, Inc.
 * Author URI:        
 * License:           GPL version 3 or any later version
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       tribe-ext-modify
 * Hooks ref: https://theeventscalendar.com/plugin/the-events-calendar/
 */
if ( ! defined( 'ABSPATH' ) ) exit; 


//activate/deactivate hooks
function tribe_ext_modify_plugin_activation() 
{
    //flush_rewrite_rules(); 
    //return false;
}

function tribe_ext_modify_plugin_deactivation() 
{
    //deregister user_contactmethods
    // tribe_ext_modify_texmod_contact_methods($profile_fields);
    return false;
}

/**
 * Scripts to include
 * 
 */
//enqueue public scripts
function tribe_ext_modify_links_add_scripts()
{

    wp_enqueue_style( 'texmod-style',  plugin_dir_url( __FILE__ )  
                        . 'lib/texmod-style.css',array(), false );
    wp_register_script( 'icsexport-plugin', plugins_url( 'lib/icsexport.js', 
                        __FILE__ ), array( 'jquery' ), false, true );
    wp_enqueue_script( 'icsexport-plugin' );
}
add_action( 'wp_enqueue_scripts', 'tribe_ext_modify_links_add_scripts' );

//admin scripts
if( is_admin() ) : 
    function tribe_ext_modify_links_custom_admin_styling($hook = null) 
    {
        
        //if ( 'edit.php' != $hook ) { return; }

        wp_register_style( 'texmod_wp_admin_css', plugin_dir_url( __FILE__ ) 
                           . 'lib/admin-style.css', false, '1.0.0' );
        wp_enqueue_style( 'texmod_wp_admin_css' );
        //wp_enqueue_script( 'texmod_custom_script', plugin_dir_url( __FILE__ ) . 'js/admin-script.js', array(), '1.0' );
    }
add_action( 'admin_enqueue_scripts', 'tribe_ext_modify_links_custom_admin_styling' );
endif;

/**
 * Include loadable plugin files
 */
// Initialise - load in translations
function tribe_ext_modify_loadtranslations() 
{

    $plugin_dir = basename(dirname(__FILE__)).'/languages';
        load_plugin_textdomain( 'tribe-ext-modify', false, $plugin_dir );
}
add_action('plugins_loaded', 'tribe_ext_modify_loadtranslations');

    // hook the plugin activation
    register_activation_hook(   __FILE__, 'tribe_ext_modify_plugin_activation');
    register_deactivation_hook( __FILE__, 'tribe_ext_modify_plugin_deactivation');	

// Add a custom endpoint "calendar" custom, render_feed
function tribe_ext_modify_links_add_calendar_feed()
{

	add_feed('tribe_events', 'ical');
}
//add_action('init', 'tribe_ext_modify_links_add_calendar_feed');
    require_once plugin_dir_path( __FILE__ ) . 'inc/tribe-ext-modify-adminpage.php';    
    require_once plugin_dir_path( __FILE__ ) . 'inc/tribe-ext-modify.php';    
    require_once plugin_dir_path( __FILE__ ) . 'inc/tribe-ext-modify-helpers.php';
    require_once plugin_dir_path( __FILE__ ) . 'inc/tribe-ext-modify-userprofile.php';
/**
 * init shortcode
 * https://codex.wordpress.org/Shortcode_API
 *
 * @uses if_class_exists
 * @since 1.0.0
 */
add_action( 'init', 'tribe_ext_modify_links_shortcode_init' );
function tribe_ext_modify_links_shortcode_init()
{
    add_shortcode( 'tecmod_profile_form', 'tribe_ext_modify_init_tecmod_profile' );
    add_shortcode( 'tribe_ext_modify_buttons',  'tribe_extention_button_footer_scripts' );
    add_shortcode( 'tribe_ext_modify_to_links', 'tribe_ext_create_email_file_output' );
    add_shortcode( 'upcoming_events',     'texmod_upcoming_events_shortcode' );
}
?>
