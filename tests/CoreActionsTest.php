<?php
/**
 * Core activity action tests.
 *
 * @package User/Activity/Tests
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/common-functions.php';
require_once dirname( __DIR__ ) . '/wp-user-activity/includes/classes.php';
require_once dirname( __DIR__ ) . '/wp-user-activity/includes/actions/class-action-core.php';

/**
 * Core activity action tests.
 */
final class CoreActionsTest extends TestCase {

	/**
	 * Core activity type under test.
	 *
	 * @var WP_User_Activity_Type_Core
	 */
	private $type;

	/**
	 * Set up a core activity type and bypass persistence.
	 */
	protected function setUp(): void {
		$GLOBALS['wpuat_test']               = array();
		$GLOBALS['wp_user_activity_actions'] = array();

		$GLOBALS['wpuat_test']['returns']['get_current_blog_id']                       = 7;
		$GLOBALS['wpuat_test']['returns']['apply_filters:wp_pre_insert_user_activity'] = true;

		$this->type = new WP_User_Activity_Type_Core();
	}

	/**
	 * Dashboard core updates remain attributed to the current user.
	 */
	public function test_logs_dashboard_core_updates_as_user_updates() {
		$this->assertSame(
			array(
				'object_type' => 'core',
				'object_name' => 'WordPress Updated',
				'object_id'   => 7,
				'action'      => 'update',
			),
			$this->log_core_update( 'update-core.php' )
		);
	}

	/**
	 * Background core updates remain automatic.
	 */
	public function test_logs_background_core_updates_as_auto_updates() {
		$this->assertSame(
			array(
				'object_type' => 'core',
				'object_name' => 'WordPress Auto Updated',
				'object_id'   => 7,
				'action'      => 'auto_update',
			),
			$this->log_core_update( null )
		);
	}

	/**
	 * WP-CLI core updates are attributed to the current user.
	 *
	 * @runInSeparateProcess
	 * @preserveGlobalState disabled
	 */
	public function test_logs_wp_cli_core_updates_as_user_updates() {
		define( 'WP_CLI', true );

		$this->assertSame(
			array(
				'object_type' => 'core',
				'object_name' => 'WordPress Updated',
				'object_id'   => 7,
				'action'      => 'update',
			),
			$this->log_core_update( null )
		);
	}

	/**
	 * Log a core update and return the arguments passed to insertion.
	 *
	 * @param string|null $pagenow Current administration page.
	 * @return array
	 */
	private function log_core_update( $pagenow ) {
		$GLOBALS['pagenow'] = $pagenow;

		$this->type->core_updated_successfully( '7.2' );

		$calls = $GLOBALS['wpuat_test']['calls']['apply_filters:wp_pre_insert_user_activity'];
		$args  = end( $calls );

		return array_intersect_key(
			$args[2],
			array_flip( array( 'object_type', 'object_name', 'object_id', 'action' ) )
		);
	}
}
