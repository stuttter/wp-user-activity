<?php

/**
 * User Activity Site-Settings Actions
 *
 * @package User/Activity/Actions/SiteSettings
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Options actions
 *
 * @since 0.1.0
 */
class WP_User_Activity_Type_Site_Settings extends WP_User_Activity_Type {

	/**
	 * The unique type for this activity
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public $object_type = 'site-setting';

	/**
	 * Icon of this activity type
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public $icon = 'admin-settings';

	/**
	 * Add hooks
	 *
	 * @since 0.1.0
	 */
	public function __construct() {

		// Set name
		$this->name = esc_html__( 'Site Settings', 'wp-user-activity' );

		// Update
		new WP_User_Activity_Action( array(
			'type'    => $this,
			'action'  => 'update',
			'name'    => esc_html__( 'Update', 'wp-user-activity' ),
			'message' => esc_html__( '%1$s updated the "%2$s" site setting %3$s.', 'wp-user-activity' )
		) );

		// Actions
		add_action( 'updated_option', array( $this, 'updated_option' ), 10, 3 );

		// Setup callbacks
		parent::__construct();
	}

	/** Callbacks *************************************************************/

	/**
	 * Callback for returning human-readable output.
	 *
	 * @since 0.1.0
	 *
	 * @param  object  $post
	 * @param  array   $meta
	 *
	 * @return string
	 */
	public function update_action_callback( $post, $meta = array() ) {
		return sprintf(
			$this->get_activity_action( 'update' ),
			$this->get_activity_author_link( $post ),
			$meta->object_name,
			$this->get_how_long_ago( $post )
		);
	}

	/** Logging ***************************************************************/

	/**
	 * Return an array of possible options to track
	 *
	 * @since 0.1.0
	 */
	protected function get_whitelist_options() {
		return apply_filters( 'wp_user_activity_whitelist_options', array(

			// General
			'blogname',
			'blogdescription',
			'siteurl',
			'home',
			'admin_email',
			'users_can_register',
			'default_role',
			'timezone_string',
			'date_format',
			'time_format',
			'start_of_week',

			// Writing
			'use_smilies',
			'use_balanceTags',
			'default_category',
			'default_post_format',
			'mailserver_url',
			'mailserver_login',
			'mailserver_pass',
			'default_email_category',
			'ping_sites',

			// Reading
			'show_on_front',
			'page_on_front',
			'page_for_posts',
			'posts_per_page',
			'posts_per_rss',
			'rss_use_excerpt',
			'blog_public',

			// Discussion
			'default_pingback_flag',
			'default_ping_status',
			'default_comment_status',
			'require_name_email',
			'comment_registration',
			'close_comments_for_old_posts',
			'close_comments_days_old',
			'thread_comments',
			'thread_comments_depth',
			'page_comments',
			'comments_per_page',
			'default_comments_page',
			'comment_order',
			'comments_notify',
			'moderation_notify',
			'comment_moderation',
			'comment_whitelist',
			'comment_max_links',
			'moderation_keys',
			'blacklist_keys',
			'show_avatars',
			'avatar_rating',
			'avatar_default',

			// Media
			'thumbnail_size_w',
			'thumbnail_size_h',
			'thumbnail_crop',
			'medium_size_w',
			'medium_size_h',
			'large_size_w',
			'large_size_h',
			'uploads_use_yearmonth_folders',

			// Permalinks
			'permalink_structure',
			'category_base',
			'tag_base',

			// Widgets
			'sidebars_widgets',
		) );
	}

	/**
	 * Option updated
	 *
	 * @since 0.1.0
	 *
	 * @param  string  $option
	 * @param  string  $oldvalue
	 * @param  string  $newvalue
	 */
	public function updated_option( $option, $oldvalue = '', $newvalue = '' ) {

		// Bail if not a whitelisted option
		if ( ! in_array( $option, $this->get_whitelist_options() ) ) {
			return;
		}

		// Insert activity
		wp_insert_user_activity( array(
			'object_type' => $this->object_type,
			'object_name' => $option,
			'action'      => 'update'
		) );
	}
}
