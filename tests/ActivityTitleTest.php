<?php
/**
 * Activity title tests.
 *
 * @package User/Activity/Tests
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/activity-title-functions.php';

/**
 * Activity title tests.
 */
final class ActivityTitleTest extends TestCase {

	/**
	 * Titles reuse the public action renderer and omit its relative time.
	 */
	public function test_formats_titles_from_saved_activities() {
		$GLOBALS['wpuat_test'] = array();

		$post     = new WP_Post();
		$post->ID = 19;

		$GLOBALS['wpuat_test']['returns']['get_post'] = $post;

		$GLOBALS['wpuat_test']['callbacks']['get_post_meta']                                    = function ( $post_id, $key ) {

			$meta = array(
				'wp_user_activity_object_type' => 'plugin',
				'wp_user_activity_object_name' => 'BuddyPress',
				'wp_user_activity_action'      => 'activate',
			);

			return isset( $meta[ $key ] ) ? $meta[ $key ] : '';
		};
		$GLOBALS['wpuat_test']['returns']['apply_filters:wp_get_user_activity_plugin_activate'] = '<a>Admin</a> activated the &quot;BuddyPress&quot; plugin <a><time class="diff-time">moments ago</time></a>.';

		$this->assertSame( 'Admin activated the "BuddyPress" plugin.', wp_user_activity_get_post_title( 19 ) );
	}

	/**
	 * Extensions can filter a generated title without storing markup.
	 */
	public function test_sanitizes_filtered_activity_titles() {
		$GLOBALS['wpuat_test'] = array();

		$post     = new WP_Post();
		$post->ID = 20;

		$GLOBALS['wpuat_test']['returns']['get_post'] = $post;

		$GLOBALS['wpuat_test']['returns']['apply_filters:wp_get_user_activity_custom_sync'] = '<b>Rendered action</b>';

		$GLOBALS['wpuat_test']['returns']['apply_filters:wp_user_activity_post_title'] = '<b>Custom &amp; &lt;i&gt;readable&lt;/i&gt;</b>';

		$GLOBALS['wpuat_test']['callbacks']['get_post_meta'] = function ( $post_id, $key ) {
			return 'wp_user_activity_object_type' === $key ? 'custom' : ( 'wp_user_activity_action' === $key ? 'sync' : '' );
		};

		$this->assertSame( 'Custom & readable', wp_user_activity_get_post_title( 20 ) );
	}

	/**
	 * Activity titles are plain-text snapshots of the action at insertion.
	 */
	public function test_inserts_a_descriptive_plugin_title_without_relative_time() {
		global $wpdb;

		$GLOBALS['wpuat_test']                                   = array();
		$GLOBALS['wpuat_test']['returns']['get_current_user_id'] = 9;
		$GLOBALS['wpuat_test']['returns']['wp_insert_post']      = 45;

		$post     = new WP_Post();
		$post->ID = 45;

		$GLOBALS['wpuat_test']['returns']['get_post'] = $post;

		$GLOBALS['wpuat_test']['callbacks']['get_post_meta']                                    = function ( $post_id, $key ) {

			$meta = array(
				'wp_user_activity_object_type' => 'plugin',
				'wp_user_activity_object_name' => 'BuddyPress',
				'wp_user_activity_action'      => 'activate',
			);

			return isset( $meta[ $key ] ) ? $meta[ $key ] : '';
		};
		$GLOBALS['wpuat_test']['returns']['apply_filters:wp_get_user_activity_plugin_activate'] = 'Admin activated the "BuddyPress" plugin <time class="diff-time">moments ago</time>.';

		$GLOBALS['wp_user_activity_actions'] = array( 'plugin' => (object) array() );
		$wpdb                                = new class() {
			/**
			 * Posts table.
			 *
			 * @var string
			 */
			public $posts = 'wp_posts';

			/**
			 * Record a database update.
			 *
			 * @return mixed
			 */
			public function update() {
				return wpuat_test_call( 'wpdb_update', func_get_args() );
			}
		};

		wp_insert_user_activity(
			array(
				'object_type' => 'plugin',
				'object_name' => 'BuddyPress',
				'action'      => 'activate',
			)
		);

		$insert = $GLOBALS['wpuat_test']['calls']['wp_insert_post'][0][0];
		$update = $GLOBALS['wpuat_test']['calls']['wpdb_update'][0];
		$this->assertArrayNotHasKey( 'post_title', $insert );
		$this->assertSame( 'BuddyPress', $insert['meta_input']['wp_user_activity_object_name'] );
		$this->assertSame( 'wp_posts', $update[0] );
		$this->assertSame( 'Admin activated the "BuddyPress" plugin.', $update[1]['post_title'] );
		$this->assertSame( 45, $update[2]['ID'] );
		$this->assertSame( array( 45 ), $GLOBALS['wpuat_test']['calls']['clean_post_cache'][0] );

		wp_insert_user_activity(
			array(
				'object_type' => 'plugin',
				'object_name' => 'BuddyPress',
				'action'      => 'activate',
			)
		);
		$second_update = $GLOBALS['wpuat_test']['calls']['wpdb_update'][1];
		$this->assertSame( $update[1]['post_title'], $second_update[1]['post_title'] );
	}

	/**
	 * Suspended transition callbacks are restored when insertion fails.
	 */
	public function test_restores_transition_callbacks_after_an_insertion_exception() {
		global $merged_filters, $wp_filter;

		$GLOBALS['wpuat_test']                                = array();
		$GLOBALS['wpuat_test']['callbacks']['wp_insert_post'] = function () {
			throw new RuntimeException( 'Insertion failed.' );
		};
		$wp_filter      = array( 'transition_post_status' => array( 10 => array( 'callback' ) ) );
		$merged_filters = array( 'transition_post_status' => true );

		try {
			wp_insert_user_activity();
			$this->fail( 'The insertion exception was not thrown.' );
		} catch ( RuntimeException $exception ) {
			$this->assertSame( 'Insertion failed.', $exception->getMessage() );
		}

		$this->assertArrayHasKey( 'transition_post_status', $wp_filter );
		$this->assertArrayHasKey( 'transition_post_status', $merged_filters );
	}
}
