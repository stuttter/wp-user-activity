<?php

/**
 * User Activity Admin
 *
 * @package User/Activity/Admin
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Filter activity posts list-table columns
 *
 * @since 0.1.0
 *
 * @param   array  $columns
 * @return  array
 */
function wp_user_activity_manage_posts_columns( $columns = array() ) {

	// Override all columns
	$new_columns = array(
		'cb'       => '<input type="checkbox" />',
		'severity' => '<span class="screen-reader-text">' . esc_html__( 'Severity', 'wp-user-activity' ) . '</span><span class="dashicons dashicons-shield" title="' . esc_html__( 'Severity', 'wp-user-activity' ) . '"></span>',
		'username' => esc_html__( 'Action', 'wp-user-activity' ),
		'when'     => esc_html__( 'Date',   'wp-user-activity' )
	);

	// Return overridden columns
	return apply_filters( 'wp_user_activity_manage_posts_columns', $new_columns, $columns );
}

/**
 * Force the primary column
 *
 * @since 0.1.0
 *
 * @return string
 */
function wp_user_activity_list_table_primary_column( $name = '', $screen_id = '' ) {

	// Only on the `edit-activity` screen
	if ( 'edit-activity' === $screen_id ) {
		$name = 'username';
	}

	// Return possibly overridden name
	return $name;
}

/**
 * Output content for each activity item
 *
 * @since 0.1.0
 *
 * @param  string  $column
 * @param  int     $post_id
 */
function wp_user_activity_manage_custom_column_data( $column = '', $post_id = 0 ) {

	// Get post & metadata
	$post  = get_post( $post_id );
	$meta  = wp_get_user_activity_meta( $post_id );

	// Custom column IDs
	switch ( $column ) {

		// Attempt to output human-readable action
		case 'severity' :
			echo wp_get_user_activity_severity( $post, $meta );
			break;

		// User who performed this activity
		case 'username' :
			echo wp_get_user_activity_action( $post, $meta );
			break;

		// Attempt to output helpful connection to object
		case 'when' :
			$when = strtotime( $post->post_date );
			$date = get_option( 'date_format' );
			$time = get_option( 'time_format' );
			echo date_i18n( $date, $when, true );
			echo '<br>';
			echo date_i18n( $time, $when, true );
			break;
	}
}

/**
 * Enqueue scripts
 *
 * @since 0.1.1
 */
function wp_user_activity_admin_assets() {

	// Bail if not an event post type
	if ( 'activity' !== get_post_type() ) {
		return;
	}

	// Activity styling
	wp_enqueue_style( 'wp_user_activity', wp_user_activity_get_plugin_url() . '/assets/css/activity.css', false, wp_user_activity_get_asset_version(), false );
}

/**
 * Disable months dropdown
 *
 * @since 0.1.2
 */
function wp_user_activity_disable_months_dropdown( $disabled = false, $post_type = 'post' ) {

	// Disable dropdown for events
	if ( 'activity' === $post_type ) {
		$disabled = true;
	}

	// Return maybe modified value
	return $disabled;
}

/**
 * Output dropdowns & filters
 *
 * @since 0.1.2
 */
function wp_user_activity_add_dropdown_filters( $post_type = '' ) {

	// Bail if not the event post type
	if ( 'activity' !== $post_type ) {
		return;
	}

	ob_start(); ?>

	<label class="screen-reader-text" for="cat">' . __( 'Filter by action', 'wp-user-activity' ) . '</label>
	<select name="wp-user-activity-actions">
		<option value=""><?php esc_html_e( 'All actions', 'wp-user-activity' ); ?></option>
	</select>

	<?php

	// Output the filters
	ob_end_flush();
}
