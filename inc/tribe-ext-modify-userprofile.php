<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

/**
 * Add custom fields to user profiles to open calendar parameters.
 * 
 * @since 1.0.3
 * unset($profile_fields['gcalendar']); to unset
 */
function tribe_ext_modify_texmod_contact_methods($profile_fields) 
{
    // Add new fields
	$profile_fields['gcalendar']     = 'GCalendar or iCloud Username';
	$profile_fields['icloud_secret'] = 'iCloud Secret';
	$profile_fields['ical_email']    = 'Custom email to Calendar';

	return $profile_fields;
}
add_filter('user_contactmethods', 'tribe_ext_modify_texmod_contact_methods');

/**
 * To retrieve a custom field value
 * $gcalendarHandle = get_user_meta(1, 'twitter', true);
 */ 

/**
 * Shortcode for page My User Profile.
 * 
 * @uses 2 functions comment out if you want fields in registration form.
 * @uses shortcode [tecmod_profile_form]
 *  
 * Updates above
 */ 
add_action( 'profile_update', 'tribe_ext_modify_init_tecmod_profile' );
//add_action( 'user_new_form', 'tribe_ext_modify_init_tecmod_profile' );
//add_action( 'user_register', 'tribe_ext_modify_init_tecmod_profile');
function tribe_ext_modify_init_tecmod_profile( $user_id )
{
	
	global $current_user, $user_id;

	if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) 
	&& $_POST['action'] === 'update-user' ) 
	{
		$nonce = $_REQUEST['_wpnonce'];
		if ( ! wp_verify_nonce( $nonce, 'update-contacts' ) ) { 
			die( __( 'Security Connection Lost' ) );
		}
		$user_id = absint( $_POST['user_id'] );
		if ( !current_user_can( 'edit_user', $user_id ) ) return false;
 
update_user_meta( $user_id, 'gcalendar', sanitize_text_field( $_POST['user_gcalendar'] ) ); 
update_user_meta( $user_id, 'icloud_secret',  sanitize_text_field( $_POST['icloud_secret'] ) ); 
update_user_meta( $user_id, 'ical_email', sanitize_text_field( $_POST['ical_email'] ) ); 

		if ( !is_wp_error() ) 
		{
		?>

<div class="row">
	<h4><?php esc_html_e('Your Information Was Saved Successfully.' ); ?></h4>
	<p><?php esc_html_e( 'Verification may take some adjustment if this does not work for you.' ); ?></p>
	<p><?php printf( '%s', $current_user->display_name ); ?></p>
</div>

<?php
} else {
?>

<div class="row">
<h2><?php _e('Your Information did not save. Please re-validate.' ); ?></h2>
	<p><?php //echo $nameError; ?></p>
</div>

		<?php
		}    

	}
?>
<?php 
//values for user meta
$ical_email = $icloud_secret = $gcalendar = '';
$gcalendar     = get_user_meta( $current_user->ID, 'gcalendar', true ); 
$icloud_secret = get_user_meta( $current_user->ID, 'icloud_secret', true );
$ical_email    = get_user_meta( $current_user->ID, 'ical_email', true );
ob_start(); 
?>
<br>
<div class="tribe-ext-content">
    <?php if ( !is_user_logged_in() ) : ?>
    <p class="warnining"><?php _e( 'You must be logged in to edit your profile.' ); ?></p>
    <p><a href="<?php echo wp_login_url( get_permalink() ); ?>" title="Login">Login</a></p>
    <?php else : ?>
    <h3><?php print( $current_user->display_name ); ?></h3>
    <form name="user-profile-front" action="" method="POST">
        <table class="form-table">
			<tbody>
		<tr>
			<td>
				<?php // secret key field ?>
				<label for="gcalendar"><?php _e( 'GCalendar or iCloud Username' ); ?>  </label><br>
					<input id="user_gcalendar" value="<?php print( $gcalendar ); ?>" 
					     name="user_gcalendar" type="text">
			</td>
        </tr>
        <tr>
			<td>
				<?php // secret key field ?>
				<label for="icloud_secret"><?php _e( 'iCloud Secret' ); ?>  </label><br>
					<input id="icloud_secret" value="<?php print( $icloud_secret ); ?>" 
					     name="icloud_secret" type="text">
			</td>
        </tr>
        <tr>
			<td>
				<?php // secret key field ?>
				<label for="ical_email"><?php _e( 'Custom email to Calendar' ); ?>  </label><br>
					<input id="ical_email" value="<?php print( $ical_email ); ?>" 
					     name="ical_email" type="text">
			</td>
        </tr>
        <tr>
			<td>
				<?php // secret key field ?>
                <?php echo absint( $current_user->ID ); ?>
                    <input name="updateuser" type="submit" id="updateuser" 
                           class="submit button" value="<?php _e('Update'); ?>" />
                    <?php wp_nonce_field( 'update-contacts' ) ?>
                    <input name="action" type="hidden" id="action" value="update-user" />
                    <input name="user_id" type="hidden" id="user_id" 
                           value="<?php absint( $current_user->ID ); ?>" />        
			</td>
		</tr>
			</tbody>
		</table>
    </form><br><p><a class="button" href="<?php echo esc_url( get_home_url() . '/events/' ); ?>" title="back to calendar">Back to Events Calendar</a></p>
    <?php endif; ?>
</div><!-- tribe-ext-content -->
<?php 
	$output = ob_get_clean();
	echo $output;
}