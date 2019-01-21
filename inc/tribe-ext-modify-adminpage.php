<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @package    tribe-ext-modify
 * @subpackage tribe-ext-modify/inc
 * @author     Larry Judd <tradesouthwest@gmail.com>
 *  
 */
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'tribe_ext_modify_add_options_page' ); 
add_action( 'admin_init', 'tribe_ext_modify_register_admin_options' ); 

//create an options page
function tribe_ext_modify_add_options_page() 
{
   add_submenu_page(
       'tools.php',
        esc_html__( 'TECModifyGCal', 'tribe-ext-modify' ),
        esc_html__( 'TECModifyGCal', 'tribe-ext-modify' ),
        'manage_options',
        'tribe_ext_modify',
        'tribe_ext_modify_options_page',
        'dashicons-admin-tools' 
    );
}   
 
/** a.) Register new settings
 *  $option_group (page), $option_name, $sanitize_callback
 *  --------
 ** b.) Add sections
 *  $id, $title, $callback, $page
 *  --------
 ** c.) Add fields 
 *  $id, $title, $callback, $page, $section, $args = array() 
 *  --------
 ** d.) Options Form Rendering. action="options.php"
 *
 */

// a.) register all settings groups
function tribe_ext_modify_register_admin_options() 
{
    //options pg
    register_setting( 'tribe_ext_modify_options', 'tribe_ext_modify_options' );
     
/**
 * b1.) options section
 */
    add_settings_section(
        'tribe_ext_modify_options_section',
        esc_html__( 'Text Boxes and Options', 'tribe-ext-modify' ),
        'tribe_ext_modify_options_section_cb',
        'tribe_ext_modify_options'
    ); 
        // c.) settings 
    add_settings_field(
        'tribe_ext_modify_cstitle_field',
        esc_attr__('Label for Buttons text title', 'tribe-ext-modify'),
        'tribe_ext_modify_cstitle_field_cb',
        'tribe_ext_modify_options',
        'tribe_ext_modify_options_section',
        array( 
            'type'         => 'text',
            'option_group' => 'tribe_ext_modify_options', 
            'name'         => 'tribe_ext_modify_cstitle_field',
'value' => empty( get_option( 'tribe_ext_modify_options' )['tribe_ext_modify_cstitle_field'] ) 
            ? 'Select an Event to email or view' : get_option( 'tribe_ext_modify_options' )['tribe_ext_modify_cstitle_field'], 
            'description'  => esc_html__( 'Shows Above links buttons.', 'tribe-ext-modify' )
        )
    );
    // c.) settings 
    add_settings_field(
        'tribe_ext_modify_othertext_field',
        esc_attr__('Optional text field', 'tribe-ext-modify'),
        'tribe_ext_modify_othertext_field_cb',
        'tribe_ext_modify_options',
        'tribe_ext_modify_options_section',
        array( 
            'type'         => 'text',
            'option_group' => 'tribe_ext_modify_options', 
            'name'         => 'tribe_ext_modify_othertext_field',
'value' => empty( get_option( 'tribe_ext_modify_options' )['tribe_ext_modify_othertext_field'] ) 
           ? '' : get_option( 'tribe_ext_modify_options' )['tribe_ext_modify_othertext_field'], 
            'description'  => esc_html__( 'Additional fields', 'tribe-ext-modify' )
        )
    );
    // c.) settings 
    add_settings_field(
        'tribe_ext_modify_gcaluser_field',
        esc_attr__('Gmail User Name for Public GCal', 'tribe-ext-modify'),
        'tribe_ext_modify_gcaluser_field_cb',
        'tribe_ext_modify_options',
        'tribe_ext_modify_options_section',
        array( 
            'type'         => 'text',
            'option_group' => 'tribe_ext_modify_options', 
            'name'         => 'tribe_ext_modify_gcaluser_field',
'value' => empty( get_option( 'tribe_ext_modify_options' )['tribe_ext_modify_gcaluser_field'] ) 
           ? '' : get_option( 'tribe_ext_modify_options' )['tribe_ext_modify_gcaluser_field'], 
            'description'  => esc_html__( 'ONLY THE USER NAME - Everything BEFORE the @gmail.com', 'tribe-ext-modify' )
        )
    );
} 

/** 
 * name for 'branding' field
 * @since 1.0.0
 */
function tribe_ext_modify_cstitle_field_cb($args)
{  
   printf(
        '<input type="%1$s" name="%2$s[%3$s]" id="%2$s-%3$s" 
        value="%4$s" class="regular-text" />
        <span>%5$s <b class="wntip" title="tip"> ? </b></span>',
        $args['type'],
        $args['option_group'],
        $args['name'],
        $args['value'],
        $args['description']
    );
}

/** 
 * year for 'year_expire'field
 * @since 1.0.0
 */
function tribe_ext_modify_othertext_field_cb($args)
{  
   printf(
        '<input type="%1$s" name="%2$s[%3$s]" id="%2$s-%3$s" 
        value="%4$s" class="regular-text" />
        <span>%5$s <b class="wntip" title="tip"> ? </b></span>',
        $args['type'],
        $args['option_group'],
        $args['name'],
        $args['value'],
        $args['description']
    );
}


/** 
 * name for 'branding' field
 * @since 1.0.0
 */
function tribe_ext_modify_gcaluser_field_cb($args)
{  
   printf(
        '<input type="%1$s" name="%2$s[%3$s]" id="%2$s-%3$s" 
        value="%4$s" class="regular-text" />
        <span>%5$s <b class="wntip" title="tip"> ? </b></span>',
        $args['type'],
        $args['option_group'],
        $args['name'],
        $args['value'],
        $args['description']
    );
}

/**
 ** Section Callbacks
 *  $id, $title, $callback, $page
 */
// section heading cb
function tribe_ext_modify_options_section_cb()
{    
print( '<hr>' );
} 


// d.) render admin page
function tribe_ext_modify_options_page() 
{
    // check user capabilities
    if ( ! current_user_can( 'manage_options' ) ) return;
    // check if the user have submitted the settings
    // wordpress will add the "settings-updated" $_GET parameter to the url
    if ( isset( $_GET['settings-updated'] ) ) {
    // add settings saved message with the class of "updated"
    add_settings_error( 'tribe_ext_modify_messages', 'tribe_ext_modify_message', 
                        esc_html__( 'Settings Saved', 'tribe-ext-modify' ), 'updated' );
    }
    // show error/update messages
    settings_errors( 'tribe_ext_modify_messages' );
     
    ?>
    <div class="wrap wrap-tribe_ext_modify-admin">
    
    <h1><span id="TEMEOptions" class="dashicons dashicons-admin-tools"></span> 
    <?php echo esc_html( 'Admin' ); ?></h1>
         
    <form action="options.php" method="post">
    <?php //page=tribe_ext_modify&tab=tribe_ext_modify_options
        settings_fields( 'tribe_ext_modify_options' );
        do_settings_sections( 'tribe_ext_modify_options' ); 
        
        submit_button( 'Save Settings' ); 
 
    ?>
    </form>
    <div class="tecmod-dl">
    <h4>Instructions for the plugin</h4>
    <dl>
    <dt>Label for Buttons Title</dt>
    <dd>Displays at the top of the buttons.</dd>
    <dd></dd>
    <dt>GMail for Google Calendar </dt>
    <dd>Must have to show the public Google Calendar link.</dd>
    <dd>Add ONLY the user name and not the at gmail dot com part at the end!</dd>
    <dd>The at gmail and dot com are filtered for security reasons so omit them please.</dd>
    <dt></dt>
    <dd></dd>
    </dl>
    <h4>Editing Events and Adding to Google</h4>
    <dl>
    <dt>Event can be added by anyone with Editor or Administrator Privledges</dt>
    <dd>Button to update GCalendar is above the <strong>Publish</strong> button.</dd>
    <dd>Save to Google button only appears after event is initially saved.</dd>
    </dl>
    <p>Shortcode for Buttons to display below calendar. Should be put in <strong>Events</strong> > <strong>Settings</strong> > <strong>Display</strong> after html option.</p>
    <pre>
* shortcode required on page with definitive name
Page Name: Export Events Page
Shortcode: [tribe_ext_modify_to_links]

* In Tribe Events > Settings > Display add shortcode to initiate main plugin. 
Do not forget to add to the appropriate place, Add HTML after event content box should suffice.
Shortcode: [tribe_ext_modify_buttons]
</pre>
    <p>Additional shortcode [tecmod_profile_form] not used in this version.</p>
    </div>
</div>
<?php 
}