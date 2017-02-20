<?php

/**
 * User Activity Metaboxes
 *
 * @package UserActivity/Metaboxes
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * User Activity Metaboxes
 *
 * @since  0.1.0
*/
function wp_user_activity_add_metaboxes() {

	// Plugin page
	$plugin_page = 'activity';

	// Activity add/edit (object)
	add_meta_box(
		'wp_user_activity_object_details',
		__( 'Object', 'wp-user-activity' ),
		'wp_user_activity_object_metabox',
		$plugin_page,
		'normal',
		'default'
	);

	// Activity add/edit (user)
	add_meta_box(
		'wp_user_activity_user_details',
		__( 'User', 'wp-user-activity' ),
		'wp_user_activity_user_metabox',
		$plugin_page,
		'normal',
		'default'
	);
}

/**
 * Add metabox to User Profiles section
 *
 * @since 0.2.0
 *
 * @param string $type
 */
function wp_user_activity_add_user_profiles_metabox( $type = '', $user = null ) {

	// Get hookname
	$hooks = wp_user_profiles_get_section_hooknames( 'activity' );

	// Bail if not the correct type
	if ( ! in_array( $type, $hooks, true ) ) {
		return;
	}

	// Add the metabox
	add_meta_box(
		'wp_user_activity_user_profile',
		_x( 'Activity', 'plural', 'wp-user-activity' ),
		'wp_user_activity_list_metabox',
		$type,
		'normal',
		'default',
		$user
	);
}

/**
 * Output the activity object metabox
 *
 * @since  0.1.0
*/
function wp_user_activity_object_metabox() {

	// Get the post
	$post = get_post();

	// Get the metas
	$meta = wp_user_activity_get_meta( $post->ID );

	// Action types (for dropdown)
	$action_types = ! empty( $GLOBALS['wp_user_activity_actions'] )
		? $GLOBALS['wp_user_activity_actions']
		: array();

	// Start an output buffer
	ob_start(); ?>

	<input type="hidden" name="wp_user_activity_metabox_nonce" value="<?php echo wp_create_nonce( 'wp_user_activity' ); ?>" />
	<table class="form-table rowfat">
		<tr>
			<td>
				<label for="wp_user_activity_type"><?php esc_html_e( 'Type', 'wp-user-activity'); ?></label>
			</td>

			<td>
				<select name="wp_user_activity_type" id="wp_user_activity_type">
					<option value="0"><?php esc_html_e( '&mdash; No type &mdash;', 'wp-user-activity' ); ?></option>

					<?php foreach ( $action_types as $type ) : ?>

						<option value="<?php echo esc_attr( $type->object_type ); ?>" <?php selected( $meta['object_type'], $type->object_type ); ?>><?php echo esc_html( $type->get_name() ); ?></option>

					<?php endforeach; ?>

				</select>
			</td>

			<td>
				<label for="wp_user_activity_action"><?php esc_html_e( 'Action', 'wp-user-activity'); ?></label>
			</td>

			<td>
				<input type="text" class="wp_user_activity_action" name="wp_user_activity_action" id="wp_user_activity_action" value="<?php echo esc_attr( $meta['action'] ); ?>" /><br>
			</td>
		</tr>

		<tr>
			<td>
				<label for="wp_user_activity_subtype"><?php esc_html_e( 'Sub-type', 'wp-user-activity'); ?></label>
			</td>

			<td>
				<input type="text" class="wp_user_activity_subtype" name="wp_user_activity_subtype" id="wp_user_activity_subtype" value="<?php echo esc_attr( $meta['object_subtype'] ); ?>" /><br>
			</td>

			<td>
				<label for="wp_user_activity_name"><?php esc_html_e( 'Name', 'wp-user-activity'); ?></label>
			</td>

			<td>
				<input type="text" class="wp_user_activity_name" name="wp_user_activity_name" id="wp_user_activity_name" value="<?php echo esc_attr( $meta['object_name'] ); ?>" /><br>
			</td>
		</tr>

		<tr>
			<td colspan="2">

			</td>

			<td>
				<label for="wp_user_activity_id"><?php esc_html_e( 'ID', 'wp-user-activity'); ?></label>
			</td>

			<td>
				<input type="text" class="wp_user_activity_id" name="wp_user_activity_id" id="wp_user_activity_id" value="<?php echo esc_attr( $meta['object_id'] ); ?>" /><br>
			</td>
		</tr>

	</table>

	<?php

	// End & flush the output buffer
	ob_end_flush();
}

/**
 * Output the activity user metabox
 *
 * Note that this metabox shares a nonce with `wp_user_activity_object_metabox()`
 *
 * @since  0.1.0
*/
function wp_user_activity_user_metabox() {

	// Get the post
	$post = get_post();

	// Get the metas
	$meta = wp_user_activity_get_meta( $post->ID );

	// Query for users
	$users = get_users( array(
		'count_total' => false,
		'orderby'     => 'display_name'
	) );

	// Start an output buffer
	ob_start(); ?>

	<table class="form-table rowfat">
		<tr>
			<td>
				<label for="post_author"><?php esc_html_e( 'User', 'wp-user-activity'); ?></label>
			</td>

			<td>
				<select name="post_author" id="post_author">
					<option value="0"><?php esc_html_e( '&mdash; No user &mdash;', 'wp-user-activity' ); ?></option>

					<?php foreach ( $users as $user ) :
						$user->filter = 'display';

						// Prefer first & last name, fallback to display name
						if ( ! empty( $user->first_name ) && ! empty( $user->last_name ) ) {
							$display_name = "{$user->first_name} {$user->last_name}";
						} else {
							$display_name = $user->display_name;
						} ?>

						<option value="<?php echo esc_attr( $user->ID ); ?>" <?php selected( $post->post_author, $user->ID ); ?>><?php echo esc_html( $display_name ); ?></option>

					<?php endforeach; ?>

				</select>
			</td>

			<td>
				<label for="wp_user_activity_ip"><?php esc_html_e( 'IP Address', 'wp-user-activity'); ?></label>
			</td>

			<td>
				<input type="text" class="wp_user_activity_ip" name="wp_user_activity_ip" id="wp_user_activity_ip" value="<?php echo esc_attr( $meta['ip'] ); ?>" /><br>
			</td>
		</tr>

		<tr>
			<td colspan="2">

			</td>

			<td>
				<label for="wp_user_activity_ua"><?php esc_html_e( 'User Agent', 'wp-user-activity'); ?></label>
			</td>

			<td>
				<textarea class="wp_user_activity_ua" name="wp_user_activity_ua" id="wp_user_activity_ua"><?php echo esc_attr( $meta['ua'] ); ?></textarea>
			</td>
		</tr>
	</table>

	<?php

	// End & flush the output buffer
	ob_end_flush();
}

/**
 * Metabox save
 *
 * @since  0.1.1
 *
 * @return int|void
 */
function wp_user_activity_metabox_save( $post_id = 0 ) {

	// Bail if no nonce or nonce check fails
	if ( empty( $_POST['wp_user_activity_metabox_nonce'] ) || ! wp_verify_nonce( $_POST['wp_user_activity_metabox_nonce'], 'wp_user_activity' ) ) {
		return $post_id;
	}

	// Bail on autosave, ajax, or bulk
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ( defined( 'DOING_AJAX') && DOING_AJAX ) || isset( $_REQUEST['bulk_edit'] ) ) {
		return $post_id;
	}

	// Only save activity metadata to activity post type
	if ( 'activity' !== get_post_type( $post_id ) ) {
		return $post_id;
	}

	// Bail if revision
	if ( wp_is_post_revision( $post_id ) ) {
		return $post_id;
	}

	// Bail if user cannot edit this activity
	if ( ! current_user_can( 'edit_activity', $post_id ) ) {
		return $post_id;
	}

	// Type
	$meta['object_type'] = ! empty( $_POST['wp_user_activity_type'] )
		? sanitize_key( $_POST['wp_user_activity_type'] )
		: false;

	// Action
	$meta['action'] = ! empty( $_POST['wp_user_activity_action'] )
		? sanitize_key( $_POST['wp_user_activity_action'] )
		: '';

	// Sub-type
	$meta['object_subtype'] = ! empty( $_POST['wp_user_activity_subtype'] )
		? sanitize_key( $_POST['wp_user_activity_subtype'] )
		: '';

	// Name
	$meta['object_name'] = ! empty( $_POST['wp_user_activity_name'] )
		? wp_kses( $_POST['wp_user_activity_name'], array() )
		: '';

	// ID
	$meta['object_id'] = ! empty( $_POST['wp_user_activity_id'] )
		? absint( $_POST['wp_user_activity_id'] )
		: '';

	// User IP
	$meta['ua'] = ! empty( $_POST['wp_user_activity_ip'] )
		? $_POST['wp_user_activity_ip']
		: '';

	// User Agent
	$meta['ua'] = ! empty( $_POST['wp_user_activity_ua'] )
		? $_POST['wp_user_activity_ua']
		: '';

	// Save or remove metadata
	foreach ( $meta as $key => $value ) {
		if ( empty( $value ) ) {
			delete_post_meta( $post_id, 'wp_user_activity_' . $key );
		} else {
			update_post_meta( $post_id, 'wp_user_activity_' . $key, $value );
		}
	}
}

/**
 * Output the user activity metabox
 *
 * @since 0.1.9
 *
 * @param object $user_id
 */
function wp_user_activity_list_metabox( $user_id = 0 ) {

	// Bail if no user ID
	if ( empty( $user_id ) ) {
		return;
	}

	// Bail if somehow in the wrong section
	if ( ! function_exists( 'wp_user_profiles_sections' ) ) {
		return;
	}

	// Page
	$page = isset( $_REQUEST['page'] )
		? $_REQUEST['page']
		: 0;

	// Load up the list table
	$list_table = new WP_User_Activity_List_table();
	$list_table->prepare_items( $user_id ); ?>

	<form id="wp-user-activity" method="get">
		<input type="hidden" name="page" value="<?php echo esc_attr( $page ); ?>" />

		<?php $list_table->display(); ?>
	</form>

	<?php
}
