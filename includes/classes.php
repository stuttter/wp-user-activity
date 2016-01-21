<?php

/**
 * User Activity Classes
 *
 * @package User/Activity/Classes
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Empty abstract class
 *
 * Class WP_User_Activity_Type_Base
 */
abstract class WP_User_Activity_Type {

	/**
	 * The unique type for this activity
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public $object_type = '';

	/**
	 * Name of this activity type
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public $name = '';

	/**
	 * Icon of this activity type
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public $icon = '';

	/**
	 * Description of this activity type
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public $description = '';

	/**
	 * Array of action callback methods
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public $action_callbacks = array();

	/**
	 * Probably hook things in here
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
		global $wp_user_activity_actions;

		// Set the global action
		$wp_user_activity_actions[ $this->object_type ] = $this;

		// Register action callbacks
		$this->register_action_callbacks();
	}

	/**
	 * Register callbacks based on `action_callbacks` array
	 *
	 * @since 0.1.1
	 */
	protected function register_action_callbacks() {
		foreach ( $this->action_callbacks as $callback_id => $callback ) {

			// Create a method callback key
			$method = "{$callback_id}_action_callback";

			// Register method if it exists
			if ( method_exists( $this, $method ) ) {
				wp_user_activity_register_action_callback( $this->object_type, $callback_id, array( $this, $method ) );
			}
		}
	}

	/**
	 * Return the activity name
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	public function get_name() {
		return ! empty( $this->name )
			? $this->name
			: ucfirst( $this->object_type );
	}

	/**
	 * Return the activity name
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	public function get_icon() {
		return ! empty( $this->icon )
			? sanitize_key( $this->icon )
			: '';
	}

	/**
	 * Get the string used to output an action
	 *
	 * @since 0.1.0
	 *
	 * @param   string  $action
	 *
	 * @return  string
	 */
	public function get_activity_action_name( $action = '' ) {
		return ! empty( $this->action_callbacks[ $action ]->name )
			? $this->action_callbacks[ $action ]->name
			: ucwords( $action );
	}

	/**
	 * Get the string used to output an action
	 *
	 * @since 0.1.0
	 *
	 * @param   string  $action
	 *
	 * @return  string
	 */
	protected function get_activity_action( $action = '' ) {
		if ( ! empty( $this->action_callbacks[ $action ]->message ) ) {
			return $this->action_callbacks[ $action ]->message;
		}

		return '%s %s %s';
	}

	/**
	 * Get user that performed activity
	 *
	 * @since 0.1.0
	 *
	 * @param   int   $post
	 *
	 * @return  object
	 */
	protected function get_activity_author_link( $post = 0, $args = array() ) {

		// Parse arguments
		$r = wp_parse_args( $args, array(
			'author_link'        => true,
			'author_avatar'      => true,
			'author_avatar_size' => 32
		) );

		// Bail if user was not found
		$user = $this->get_activity_author( $post );
		if ( empty( $user ) ) {
			return false;
		}

		// Prefer fullname over display_name
		if ( ! empty( $user->first_name ) && ! empty( $user->last_name ) ) {
			$display_name = "{$user->first_name} {$user->last_name}";
		} else {
			$display_name = $user->display_name;
		}

		// Set author defaults
		$avatar = '';
		$author = esc_html( $display_name );

		// Get avatar
		if ( true === $r['author_avatar'] ) {
			$avatar = get_avatar( $user->ID, (int) $r['author_avatar_size'] );
		}

		// Link user if a link was found
		if ( true === $r['author_link'] ) {
			$link = $this->get_activity_author_url( $user );

			if ( false !== $link ) {
				$avatar = '<a href="' . esc_url( $link ) . '" class="wp-user-activity user-link alignleft">' . $avatar . '</a>';
				$author = '<a href="' . esc_url( $link ) . '" class="wp-user-activity user-link">' . esc_html( $display_name ) . '</a>';
			} else {
				$avatar = '<span class="wp-user-activity alignleft">' . $avatar . '</span>';
				$author = '<span class="wp-user-activity">' . esc_html( $display_name ) . '</span>';
			}
		}

		// Return avatar & author
		return $avatar . $author;
	}

	/**
	 * Return the user object for an activity item
	 *
	 * @since 0.1.1
	 *
	 * @param  int  $post
	 */
	protected function get_activity_author( $post = 0 ) {

		// Try to get author of post
		$author = get_post( $post )->post_author;
		$user   = get_user_by( 'id', $author );

		// Support for unknown user
		if ( empty( $user ) ) {
			$user = new WP_User();
			$user->ID           = 0;
			$user->display_name = __( 'Someone', 'wp-user-activity' );
		}

		return $user;
	}

	/**
	 * @since 0.1.1
	 *
	 * @param  int  $user
	 */
	protected function get_activity_author_url( $user = 0 ) {

		// No link
		$link = false;

		// If in admin, user admin area links
		if ( is_admin() && current_user_can( 'edit_user', $user->ID ) ) {
			$link = get_edit_user_link( $user->ID );

		// Link to author URL if not in admin
		} elseif ( ! empty( $user->ID ) ) {
			$link = get_author_posts_url( $user->ID );
		}

		// Return a URL to the author
		return $link;
	}

	/**
	 * Get the formatted date & time for the activity
	 *
	 * @since 0.1.0
	 *
	 * @param   int  $post_id
	 *
	 * @return  string
	 */
	protected function get_how_long_ago( $post_id = 0 ) {

		// Get the post
		$post  = get_post( $post_id );
		$date  = get_the_date( get_option( 'date_format' ), $post->ID );
		$time  = get_the_time( get_option( 'time_format' ), $post->ID );
		$both  = "{$date} {$time}";

		// Get the human readable difference
		$pt    = strtotime( $post->post_date_gmt );
		$human = wp_user_activity_human_diff_time( $pt, current_time( 'timestamp', true ) );

		// Start with the timestamp
		$classes = get_post_class( 'wp-user-activity', $post->ID );
		$url     = false;
		$retval  = '<time class="diff-time" pubdate datetime="' . esc_attr( $both ) . '" title="' . esc_attr( $both ) . '">' . sprintf( '%s ago', $human ) . '</time>';

		// Edit link
		if ( is_admin() && current_user_can( 'edit_activity', $post->ID ) ) {
			$classes[] = 'edit-link';
			$url       = get_edit_post_link( $post->ID );

		// View link
		} elseif ( is_post_type_viewable( get_post_type_object( $post->post_type ) ) ) {
			$classes[] = 'view-link';
			$url       = get_post_permalink( $post->ID );
		}

		// Wrap time in anchor tag
		if ( ! empty( $url ) ) {
			$retval = '<a href="' . esc_url( $url ) . '" class="' . join( ' ', $classes ) . '">' . $retval . '</a>';
		}

		return $retval;
	}
}

/**
 * An activity action
 *
 * @since 0.1.0
 */
class WP_User_Activity_Action {

	/**
	 * What type of object is this?
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public $action = '';

	/**
	 * Name of this activity action
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public $name = '';

	/**
	 * Message for this activity action
	 *
	 * @since 0.1.0
	 *
	 * @var string
	 */
	public $message = '';

	/**
	 * Probably hook things in here
	 *
	 * @since 0.1.0
	 */
	public function __construct( $args = array() ) {

		// Parse args
		$r = wp_parse_args( $args, array(
			'type'    => '',
			'action'  => '',
			'name'    => '',
			'message' => '',
			'order'   => 0
		) );

		// Set object vars
		$this->action  = sanitize_key( $r['action'] );
		$this->order   = intval( $r['order'] );
		$this->name    = wp_kses( $r['name'],    array() );
		$this->message = wp_kses( $r['message'], array() );

		// Setup the callback
		$r['type']->action_callbacks[ $r['action'] ] = $this;
	}
}
