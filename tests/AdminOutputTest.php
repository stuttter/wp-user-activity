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
		$unsafe_markup                                       = '<strong onmouseover="alert(1)">Alice updated a post.</strong><a href="javascript:alert(2)">details</a><script>alert(3)</script>';
		$GLOBALS['wpuat_test']['returns']['apply_filters:wp_get_user_activity_posts_updated'] = $unsafe_markup;
		$GLOBALS['wpuat_test']['callbacks']['wp_kses_post']                                   = function ( $value ) use ( $unsafe_markup ) {
			$this->assertSame( $unsafe_markup, $value );
			return '<strong>Alice updated a post.</strong><a>details</a>alert(3)';
		};

		ob_start();
		wp_user_activity_manage_custom_column_data( 'activity_username', 12 );
		$output = ob_get_clean();

		$this->assertSame( '<strong>Alice updated a post.</strong><a>details</a>alert(3)', $output );
		$this->assertSame( array( array( $unsafe_markup ) ), $GLOBALS['wpuat_test']['calls']['wp_kses_post'] );
	}

	/**
	 * Activity-type icon output passes through the WordPress KSES boundary.
	 */
	public function test_filters_activity_type_icon_markup() {
		$GLOBALS['wpuat_test']               = array();
		$GLOBALS['wp_user_activity_actions'] = array(
			'plugins' => new class() {
				/** Get the fixture activity type name. */
				public function get_name() {
					return 'Plugins';
				}

				/** Get the fixture activity type icon. */
				public function get_icon() {
					return 'admin-plugins';
				}
			},
		);
		$post                                = new WP_Post();
		$post->ID                            = 13;

		$GLOBALS['wpuat_test']['returns']['get_post']        = $post;
		$GLOBALS['wpuat_test']['callbacks']['get_post_meta'] = function ( $post_id, $key ) {
			return 'wp_user_activity_object_type' === $key ? 'plugins' : '';
		};
		$unsafe_markup                                       = '<i class="dashicons dashicons-admin-plugins" onmouseover="alert(1)"></i><script>alert(2)</script>';
		$GLOBALS['wpuat_test']['returns']['apply_filters:wp_get_user_activity_type_icon'] = $unsafe_markup;
		$GLOBALS['wpuat_test']['callbacks']['wp_kses_post']                               = function ( $value ) use ( $unsafe_markup ) {
			$this->assertSame( $unsafe_markup, $value );
			return '<i class="dashicons dashicons-admin-plugins"></i>alert(2)';
		};

		ob_start();
		wp_user_activity_manage_custom_column_data( 'activity_type', 13 );
		$output = ob_get_clean();

		$this->assertSame( '<i class="dashicons dashicons-admin-plugins"></i>alert(2)', $output );
		$this->assertSame( array( array( $unsafe_markup ) ), $GLOBALS['wpuat_test']['calls']['wp_kses_post'] );
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
