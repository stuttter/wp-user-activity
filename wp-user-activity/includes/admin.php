<?php

/**
 * User Activity Admin
 *
 * @package User/Activity/Admin
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Tuck the top level menu underneath the dashboard menu instead
 *
 * @since 0.1.3
 */
function wp_user_activity_menu_humility() {

	// Bail if not using menu humility
	if ( ! apply_filters( 'wp_user_activity_menu_humility', false ) ) {
		return;
	}

	// Unset top level page & add a dashboard page on success
	if ( remove_menu_page( 'edit.php?post_type=activity' ) ) {
		add_dashboard_page( esc_html_x( 'Activity', 'plural', 'wp-user-activity' ), esc_html_x( 'Activity', 'post type general name', 'wp-user-activity' ), 'edit_activities', 'edit.php?post_type=activity' );
	}
}

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
		'cb'                => '<input type="checkbox" />',
		'activity_type'     => '<span class="screen-reader-text">' . esc_html__( 'Type', 'wp-user-activity' ) . '</span><span class="dashicons dashicons-backup" title="' . esc_html__( 'Type', 'wp-user-activity' ) . '"></span>',
		'activity_username' => esc_html__( 'Action',  'wp-user-activity' ),
		'activity_when'     => esc_html__( 'Date',    'wp-user-activity' ),
		'activity_session'  => esc_html__( 'Session', 'wp-user-activity' ),
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
		$name = 'activity_username';
	}

	// Return possibly overridden name
	return $name;
}

/**
 * Sortable activity columns
 *
 * @since 0.1.0
 *
 * @param   array  $columns
 *
 * @return  array
 */
function wp_user_activity_sortable_columns( $columns = array() ) {

	// Override columns
	$columns = array(
		'activity_type'     => 'type',
		'activity_username' => 'username',
		'activity_session'  => 'session',
		'activity_when'     => 'when'
	);

	return $columns;
}

/**
 * Set the relevant query vars for sorting posts by our front-end sortables.
 *
 * @since 0.1.0
 *
 * @param WP_Query $wp_query The current WP_Query object.
 */
function wp_user_activity_maybe_sort_by_fields( WP_Query $wp_query ) {

	// Bail if not 'activty' post type
	if ( empty( $wp_query->query['post_type'] ) || ! in_array( 'activity', (array) $wp_query->query['post_type'] ) ) {
		return;
	}

	// Default order
	$order = 'DESC';

	// Some default order values
	if ( ! empty( $_REQUEST['order'] ) ) {
		$new_order = strtolower( $_REQUEST['order'] );
		if ( ! in_array( $order, array( 'asc', 'desc' ) ) ) {
			$order = $new_order;
		}
	}

	// Set by 'orderby'
	if ( ! empty( $wp_query->query['orderby'] ) ) {
		switch ( $wp_query->query['orderby'] ) {

			// Type
			case 'activity_type' :
				$wp_query->set( 'order',     $order                         );
				$wp_query->set( 'orderby',   'meta_value'                   );
				$wp_query->set( 'meta_key',  'wp_user_activity_object_type' );
				$wp_query->set( 'meta_type', 'CHAR'                         );
				break;

			// Action
			case 'activity_username' :
				$wp_query->set( 'order',   $order        );
				$wp_query->set( 'orderby', 'post_author' );
				break;

			// Session
			case 'activity_session' :
				$wp_query->set( 'order',     $order                );
				$wp_query->set( 'orderby',   'meta_value'          );
				$wp_query->set( 'meta_key',  'wp_user_activity_ip' );
				$wp_query->set( 'meta_type', 'NUMERIC'             );
				break;

			// Date (default)
			case 'activity_when' :
			default :
				$wp_query->set( 'order',   $order      );
				$wp_query->set( 'orderby', 'post_date' );
				break;
		}
	}
}

/**
 * Set the relevant query vars for filtering posts by our front-end filters.
 *
 * @since 0.1.0
 *
 * @param WP_Query $wp_query The current WP_Query object.
 */
function wp_user_activity_maybe_filter_by_fields( WP_Query $wp_query ) {

	// Bail if not 'activty' post type
	if ( empty( $wp_query->query['post_type'] ) || ! in_array( 'activity', (array) $wp_query->query['post_type'] ) ) {
		return;
	}

	// Check for activity user
	$activity_user = isset( $_GET['wp-user-activity-user'] )
		? (int) $_GET['wp-user-activity-user']
		: '';

	if ( ! empty( $activity_user ) ) {
		$wp_query->set( 'author__in', array( $activity_user ) );
	}

	// Check for activity action
	$activity_action = isset( $_GET['wp-user-activity-action'] )
		? sanitize_key( $_GET['wp-user-activity-action'] )
		: '';

	// Bail if no activity action
	if ( empty( $activity_action ) ) {
		return;
	}

	// Get type & action
	$parts  = explode( '-', $activity_action );
	$type   = implode( '-', array_slice( $parts, 0, -1 ) );
	$action = array_pop( $parts );

	// Set meta_query based on type & action
	$wp_query->set( 'meta_query', array(
		array(
			'key'     => 'wp_user_activity_object_type',
			'value'   => $type,
			'type'    => 'CHAR',
			'compare' => '='
		),
		array(
			'key'     => 'wp_user_activity_action',
			'value'   => $action,
			'type'    => 'CHAR',
			'compare' => '='
		)
	) );
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
	$meta  = wp_user_activity_get_meta( $post_id );

	// Custom column IDs
	switch ( $column ) {

		// Attempt to output human-readable action
		case 'activity_type' :
			echo wp_get_user_activity_type_icon( $post, $meta ); // HTML
			break;

		// User who performed this activity
		case 'activity_username' :
			echo wp_get_user_activity_action( $post, $meta ); // HTML
			break;

		// Session of the user who performed this activity
		case 'activity_session' :
			echo '<abbr title="' . esc_attr( wp_get_user_activity_ua( $post, $meta ) ) . '">' . esc_html( wp_get_user_activity_ip( $post, $meta ) ) . '</abbr>';
			break;

		// Attempt to output helpful connection to object
		case 'activity_when' :
			$when = strtotime( $post->post_date );
			$date = get_option( 'date_format' );
			$time = get_option( 'time_format' );
			echo esc_html( date_i18n( $date, $when, true ) );
			echo '<br>';
			echo esc_html( date_i18n( $time, $when, true ) );
			break;
	}
}

/**
 * Enqueue scripts
 *
 * @since 0.1.1
 */
function wp_user_activity_admin_assets() {

	// Asset management
	$url = wp_user_activity_get_plugin_url();
	$ver = wp_user_activity_get_asset_version();

	// Activity styling
	wp_enqueue_style( 'wp_user_activity', $url . 'assets/css/activity.css', false, $ver );
}

/**
 * Disable months dropdown
 *
 * @since 0.1.2
 */
function wp_user_activity_disable_months_dropdown( $disabled = false, $post_type = 'post' ) {

	// Disable dropdown for activities
	if ( 'activity' === $post_type ) {
		$disabled = true;
	}

	// Return maybe modified value
	return $disabled;
}

/**
 * Unset the "Quick Edit" row action
 *
 * @since 0.1.0
 *
 * @param array $actions
 */
function wp_user_activity_disable_quick_edit_link( $actions = array(), $post = '' ) {

	// Unset the quick edit action
	if ( 'activity' === $post->post_type ) {
		unset( $actions['inline hide-if-no-js'] );
	}

	return $actions;
}

/**
 * Filter bulk actions & unset the edit action
 *
 * @since 0.1.0
 *
 * @param   array  $actions
 * @return  array
 */
function wp_user_activity_disable_bulk_action( $actions = array() ) {

	// No bulk edit
	unset( $actions['edit'] );

	// Return without bulk edit
	return $actions;
}

/**
 * Output dropdowns & filters
 *
 * @since 0.1.2
 */
function wp_user_activity_add_dropdown_filters( $post_type = '' ) {

	// Bail if not the activity post type
	if ( 'activity' !== $post_type ) {
		return;
	}

	// Query for users
	$users = get_users( array(
		'count_total' => false,
		'orderby'     => 'display_name'
	) );

	// Setup action types
	$action_types = $GLOBALS['wp_user_activity_actions'];

	// Current action
	$current_action = ! empty( $_GET['wp-user-activity-action'] )
		? sanitize_key( $_GET['wp-user-activity-action'] )
		: '';

	// Current user
	$current_user = ! empty( $_GET['wp-user-activity-user'] )
		? (int) $_GET['wp-user-activity-user']
		: 0;

	// Start an output buffer
	ob_start();

	// Show the action filter
	if ( apply_filters( 'wp_user_activity_show_action_filter', true ) ) : ?>

	<label class="screen-reader-text" for="type"><?php esc_html_e( 'Filter by type', 'wp-user-activity' ); ?></label>
	<select name="wp-user-activity-action" id="wp-user-activity-action">
		<option value=""><?php esc_html_e( 'All actions', 'wp-user-activity' ); ?></option>

		<?php foreach ( $action_types as $action_class ) : ?>

			<optgroup label="<?php echo esc_html( $action_class->get_name() ); ?>">

				<?php foreach ( $action_class->action_callbacks as $callback_id => $callback ) :

					// Setup the action value
					$action_value = sprintf( '%s-%s', $action_class->object_type, $callback_id ); ?>

					<option value="<?php echo esc_attr( $action_value ); ?>" <?php selected( $current_action, $action_value ); ?>><?php echo esc_html( $action_class->get_activity_action_name( $callback_id ) ); ?></option>

				<?php endforeach; ?>

			</optgroup>

		<?php endforeach; ?>

	</select><?php

	endif;

	// Show the user filter
	if ( apply_filters( 'wp_user_activity_show_user_filter', true ) ) : ?>

	<label class="screen-reader-text" for="wp-user-activity-user"><?php esc_html_e( 'Filter by user', 'wp-user-activity' ); ?></label>
	<select name="wp-user-activity-user" id="wp-user-activity-user">
		<option value="0"><?php esc_html_e( 'All users', 'wp-user-activity' ); ?></option>

		<?php foreach ( $users as $user ) :
			$user->filter = 'display';

			// Prefer first & last name, fallback to display name
			if ( ! empty( $user->first_name ) && ! empty( $user->last_name ) ) {
				$display_name = "{$user->first_name} {$user->last_name}";
			} else {
				$display_name = $user->display_name;
			} ?>

			<option value="<?php echo esc_attr( $user->ID ); ?>" <?php selected( $current_user ); ?>><?php echo esc_html( $display_name ); ?></option>

		<?php endforeach; ?>

	</select>

	<?php

	endif;

	// Output the filters
	ob_end_flush();
}

/**
 * Add new section to User Profiles
 *
 * @since 0.1.9
 *
 * @param array $sections
 */
function wp_user_activity_add_profile_section( $sections = array() ) {

	// Copy for modifying
	$new_sections = $sections;

	// Add the "Activity" section
	$new_sections['activity'] = array(
		'slug'  => 'activity',
		'name'  => esc_html__( 'Activity', 'wp-user-activity' ),
		'cap'   => 'read_activity',
		'icon'  => 'dashicons-backup',
		'order' => 95
	);

	// Filter & return
	return apply_filters( 'wp_user_activity_add_profile_section', $new_sections, $sections );
}
