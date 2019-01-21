<?php 
/**
 * Template Name: My User Profile
 *
 * Allow users to update their profiles from Frontend.
 * https://stackoverflow.com/questions/51399287/wordpress-custom-user-profile-fields
 */

get_header(); 
$sidebar = para_blog_get_sidebar_option();
$main_box_class = para_blog_get_main_class($sidebar);
$sidebar_class = para_blog_get_sidebar_class($sidebar);
?>
<div class="row">
	<div class="<?php echo esc_attr( $main_box_class ); ?>">
		<div id="primary" class="content-area">
			<main id="main" class="site-main all-blogs" role="main">
                <?php

/*  ********* BEGIN CUSTOM TOP OF PAGE (Copy a page template maybe) ********* */

if( function_exists( 'tribe_ext_modify_contactmethods_save' ) ) { 
  /* If profile was saved, update profile. */
    tribe_ext_modify_contactmethods_save();
}
  
  
?>

<?php
$ical_email= $icloud_secret = $gcalendar = '';
$gcalendar     = get_user_meta( $current_user->ID, 'gcalendar', true ); 
$icloud_secret = get_user_meta( $current_user->ID, 'icloud_secret', true );
$ical_email    = get_user_meta( $current_user->ID, 'ical_email', true );
    
if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <div id="post-<?php the_ID(); ?>">
        <div class="entry-content entry">
            <?php the_content(); ?>
            <br>
            <?php if ( !is_user_logged_in() ) : ?>
                    <p class="warning">
                        <?php _e('You must be logged in to edit your profile.', 'profile'); ?>
                    </p>
                    <p>
                        <a href="<?php echo wp_login_url( get_permalink() ); ?>" title="Login">Login</a>
                    </p>
            <?php else : ?>
              <h3><?php print( $current_user->display_name ); ?></h3>
              <form name="user-profile-front" action="" method="POST">
            <table class="form-table"><tbody>
		<tr>
			<td>
				<?php // secret key field ?>
				<label for="gcalendar"><?php _e( 'GCalendar or iCloud Username' ); ?>  </label><br>
                    <input id="user_gcalendar" value="<?php print( $gcalendar ); ?>" name="user_gcalendar" type="text">
			</td>
        </tr>
        <tr>
			<td>
				<?php // secret key field ?>
				<label for="icloud_secret"><?php _e( 'iCloud Secret' ); ?>  </label><br>
                    <input id="icloud_secret" value="<?php print( $icloud_secret ); ?>" name="icloud_secret" type="text">
			</td>
        </tr>
        <tr>
			<td>
				<?php // secret key field ?>
				<label for="ical_email"><?php _e( 'Custom email to Calendar' ); ?>  </label><br>
                    <input id="ical_email" value="<?php print( $ical_email ); ?>" name="ical_email" type="text">
			</td>
        </tr>
        <tr>
			<td>
				<?php // secret key field ?>
                <?php echo $referer; ?>
                        <input name="updateuser" type="submit" id="updateuser" 
                               class="submit button" value="<?php _e('Update'); ?>" />
                        <?php wp_nonce_field( 'update-contacts' ) ?>
                        <input name="action" type="hidden" id="action" value="update-user" />
                        <input name="user_id" type="hidden" id="user_id" 
                               value="<?php $current_user->ID; ?>" />
                    
			</td>
		</tr>
</tbody></table>
                </form><!-- #adduser -->
            <?php endif; ?>
        </div><!-- .entry-content -->
    </div><!-- .hentry .post -->
    <?php endwhile; ?>
<?php else: ?>
    <p class="no-data">
        <?php _e('Sorry, no page matched your criteria.', 'profile'); ?>
    </p><!-- .no-data -->
<?php endif; ?>
<?php 
/* *************************** BEGIN CUSTOM FOOTER *******************  */
?>

			</main><!-- #main -->
		</div><!-- #primary -->
	</div> <!-- .col-sm-8 --> 
	<?php if($sidebar != 'no-sidebar'){ ?>
	<div class="<?php echo esc_attr( $sidebar_class ) ?>">
		<?php get_sidebar(); ?>
	</div> <!-- .col-sm-4 -->
	<?php } ?>
</div> <!-- .row -->
<?php get_footer(); ?>