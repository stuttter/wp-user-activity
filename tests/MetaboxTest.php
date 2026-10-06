<?php
/**
 * Activity metabox tests.
 *
 * @package User/Activity/Tests
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/common-functions.php';
require_once __DIR__ . '/metabox-functions.php';
require_once dirname( __DIR__ ) . '/wp-user-activity/includes/metaboxes.php';

/**
 * Activity metabox tests.
 */
final class MetaboxTest extends TestCase {

	/**
	 * Reset request data after every test.
	 */
	protected function tearDown(): void {
		$_POST    = array();
		$_REQUEST = array();
	}

	/**
	 * Known actions are selected from the registered activity types.
	 */
	public function test_uses_registered_actions_for_the_action_field() {
		$GLOBALS['wpuat_test'] = array();
		$post                  = new WP_Post();
		$post->ID              = 41;

		$GLOBALS['wpuat_test']['returns']['get_post']        = $post;
		$GLOBALS['wpuat_test']['callbacks']['get_post_meta'] = function ( $post_id, $key ) {
			$values = array(
				'wp_user_activity_object_type' => 'post',
				'wp_user_activity_action'      => 'publish',
			);

			return isset( $values[ $key ] ) ? $values[ $key ] : '';
		};
		$GLOBALS['wp_user_activity_actions']                 = array(
			'post'    => $this->activity_type(
				'post',
				'Posts',
				array(
					'create'  => 'Create',
					'publish' => 'Publish',
				)
			),
			'comment' => $this->activity_type(
				'comment',
				'Comments',
				array(
					'create'  => 'Create',
					'pending' => 'Pending',
				)
			),
		);

		ob_start();
		wp_user_activity_object_metabox();
		$output = ob_get_clean();

		$this->assertStringContainsString( '<select class="wp_user_activity_action" name="wp_user_activity_action" id="wp_user_activity_action">', $output );
		$this->assertStringContainsString( '<option value="publish" selected="selected">Publish</option>', $output );
		$this->assertStringContainsString( '<option value="pending" >Pending</option>', $output );
		$this->assertSame( 1, substr_count( $output, '<option value="create"' ) );
	}

	/**
	 * Unknown extension actions remain available for administrative repair.
	 */
	public function test_preserves_an_unregistered_current_action() {
		$GLOBALS['wpuat_test'] = array();
		$post                  = new WP_Post();
		$post->ID              = 42;

		$GLOBALS['wpuat_test']['returns']['get_post']        = $post;
		$GLOBALS['wpuat_test']['callbacks']['get_post_meta'] = function ( $post_id, $key ) {
			return 'wp_user_activity_action' === $key ? 'custom-sync' : '';
		};
		$GLOBALS['wp_user_activity_actions']                 = array();

		ob_start();
		wp_user_activity_object_metabox();
		$output = ob_get_clean();

		$this->assertStringContainsString( '<option value="custom-sync" selected="selected">Custom Sync</option>', $output );
	}

	/**
	 * Request evidence is visible but cannot be edited in the metabox.
	 */
	public function test_marks_request_evidence_read_only() {
		$GLOBALS['wpuat_test'] = array();
		$post                  = (object) array(
			'ID'          => 43,
			'post_author' => 0,
		);

		$GLOBALS['wpuat_test']['returns']['get_post']        = $post;
		$GLOBALS['wpuat_test']['returns']['get_users']       = array();
		$GLOBALS['wpuat_test']['callbacks']['get_post_meta'] = function ( $post_id, $key ) {
			$values = array(
				'wp_user_activity_ip' => '192.0.2.15',
				'wp_user_activity_ua' => 'Test Browser',
			);

			return isset( $values[ $key ] ) ? $values[ $key ] : '';
		};

		ob_start();
		wp_user_activity_user_metabox();
		$output = ob_get_clean();

		$this->assertMatchesRegularExpression( '/<input(?=[^>]*id="wp_user_activity_ip")(?=[^>]*value="192\.0\.2\.15")(?=[^>]*\sreadonly="readonly")[^>]*>/', $output );
		$this->assertMatchesRegularExpression( '/<textarea(?=[^>]*id="wp_user_activity_ua")(?=[^>]*\sreadonly="readonly")[^>]*>Test Browser<\/textarea>/', $output );
	}

	/**
	 * Forged evidence values do not modify stored request evidence.
	 */
	public function test_ignores_submitted_request_evidence() {
		$GLOBALS['wpuat_test'] = array();
		$_POST                 = array(
			'wp_user_activity_metabox_nonce' => 'metabox-nonce',
			'wp_user_activity_type'          => 'post',
			'wp_user_activity_action'        => 'publish',
			'wp_user_activity_subtype'       => 'post',
			'wp_user_activity_name'          => 'Example',
			'wp_user_activity_id'            => '24',
			'wp_user_activity_ip'            => '203.0.113.8',
			'wp_user_activity_ua'            => 'Forged Browser',
		);

		$GLOBALS['wpuat_test']['returns']['wp_verify_nonce']     = true;
		$GLOBALS['wpuat_test']['returns']['get_post_type']       = 'activity';
		$GLOBALS['wpuat_test']['returns']['wp_is_post_revision'] = false;
		$GLOBALS['wpuat_test']['returns']['current_user_can']    = true;

		wp_user_activity_metabox_save( 44 );

		$updates = array_column( $GLOBALS['wpuat_test']['calls']['update_post_meta'], 1 );
		$this->assertSame(
			array(
				'wp_user_activity_object_type',
				'wp_user_activity_action',
				'wp_user_activity_object_subtype',
				'wp_user_activity_object_name',
				'wp_user_activity_object_id',
			),
			$updates
		);
		$this->assertNotContains( 'wp_user_activity_ip', $updates );
		$this->assertNotContains( 'wp_user_activity_ua', $updates );
	}

	/**
	 * Build a registered activity-type fixture.
	 *
	 * @param string $object_type Object type key.
	 * @param string $name        Object type name.
	 * @param array  $actions     Action names keyed by action ID.
	 * @return object
	 */
	private function activity_type( $object_type, $name, $actions ) {
		return new class( $object_type, $name, $actions ) {
			/**
			 * Object type key.
			 *
			 * @var string
			 */
			public $object_type;

			/**
			 * Registered action callbacks.
			 *
			 * @var array
			 */
			public $action_callbacks = array();

			/**
			 * Object type name.
			 *
			 * @var string
			 */
			private $name;

			/**
			 * Construct the fixture.
			 *
			 * @param string $object_type Object type key.
			 * @param string $name        Object type name.
			 * @param array  $actions     Action names keyed by action ID.
			 */
			public function __construct( $object_type, $name, $actions ) {
				$this->object_type = $object_type;
				$this->name        = $name;

				foreach ( $actions as $action => $action_name ) {
					$this->action_callbacks[ $action ] = (object) array( 'name' => $action_name );
				}
			}

			/** Return the fixture name. */
			public function get_name() {
				return $this->name;
			}

			/**
			 * Return an action name.
			 *
			 * @param string $action Action ID.
			 * @return string
			 */
			public function get_activity_action_name( $action ) {
				return $this->action_callbacks[ $action ]->name;
			}
		};
	}
}
