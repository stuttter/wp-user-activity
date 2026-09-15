<?php
/**
 * Tests for escaped administrative output.
 *
 * @package WP_User_Activity
 */

use PHPUnit\Framework\TestCase;

/**
 * Tests for activity list-table output.
 */
final class AdminOutputTest extends TestCase {
	/**
	 * Safe extension markup remains available while executable tags are removed.
	 */
	public function test_preserves_safe_extension_markup_without_executable_tags() {
		$GLOBALS['wpuat_test'] = array();
		$post                  = new WP_Post();
		$post->ID              = 12;

		$GLOBALS['wpuat_test']['returns']['get_post']        = $post;
		$GLOBALS['wpuat_test']['callbacks']['get_post_meta'] = function ( $post_id, $key ) {
			$values = array(
				'wp_user_activity_object_type' => 'posts',
				'wp_user_activity_action'      => 'updated',
			);
			return isset( $values[ $key ] ) ? $values[ $key ] : '';
		};
		$GLOBALS['wpuat_test']['returns']['apply_filters:wp_get_user_activity_posts_updated'] = '<strong>Alice updated a post.</strong><script>alert(1)</script>';

		ob_start();
		wp_user_activity_manage_custom_column_data( 'activity_username', 12 );
		$output = ob_get_clean();

		$this->assertSame( '<strong>Alice updated a post.</strong>alert(1)', $output );
	}

	/**
	 * Session values are escaped for their destination HTML contexts.
	 */
	public function test_escapes_session_values_for_their_html_contexts() {
		$GLOBALS['wpuat_test'] = array();
		$post                  = new WP_Post();
		$post->ID              = 14;

		$GLOBALS['wpuat_test']['returns']['get_post']        = $post;
		$GLOBALS['wpuat_test']['callbacks']['get_post_meta'] = function ( $post_id, $key ) {
			$values = array(
				'wp_user_activity_ip' => '<192.0.2.1>',
				'wp_user_activity_ua' => 'Browser "quoted"',
			);
			return isset( $values[ $key ] ) ? $values[ $key ] : '';
		};

		ob_start();
		wp_user_activity_manage_custom_column_data( 'activity_session', 14 );
		$output = ob_get_clean();

		$this->assertSame( '<abbr title="Browser &quot;quoted&quot;">&lt;192.0.2.1&gt;</abbr>', $output );
	}
}
