<?php
/**
 * Test replacement for WP_Query.
 *
 * @package User_Activity
 */

/**
 * Test replacement for WP_Query.
 */
class WP_Query {
	/**
	 * Query results.
	 *
	 * @var array
	 */
	public $posts = array();

	/**
	 * Number of query results.
	 *
	 * @var int
	 */
	public $post_count = 0;

	/**
	 * Capture query arguments and populate configured results.
	 *
	 * @param array $args Query arguments.
	 */
	public function __construct( $args = array() ) {
		$posts            = wpuat_test_call( __CLASS__, array( $args ) );
		$this->posts      = is_array( $posts ) ? $posts : array();
		$this->post_count = count( $this->posts );
	}
}
