<?php
/**
 * Tests for activity retention cron jobs.
 *
 * @package User_Activity
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/cron-functions.php';
require_once __DIR__ . '/class-wp-query.php';
require_once dirname( __DIR__ ) . '/wp-user-activity/includes/cron.php';

/**
 * Tests for activity retention cron jobs.
 */
final class CronTest extends TestCase {
	/**
	 * Reset test state before each test.
	 */
	protected function setUp(): void {
		$GLOBALS['wpuat_test'] = array();
	}

	/**
	 * The retention cutoff uses the site's timezone.
	 */
	public function test_uses_the_site_timezone_for_the_retention_cutoff() {
		$GLOBALS['wpuat_test']['returns']['wp_date'] = '2026-10-01 12:34:56';

		wp_user_activity_trash_old_activities();

		$query_args = $GLOBALS['wpuat_test']['calls']['WP_Query'][0][0];
		$this->assertSame( '2026-10-01 12:34:56', $query_args['date_query']['before'] );
		$this->assertSame( 'Y-m-d H:i:s', $GLOBALS['wpuat_test']['calls']['wp_date'][0][0] );
	}

	/**
	 * The retention query processes the oldest activities first.
	 */
	public function test_trashes_the_oldest_activities_first() {
		wp_user_activity_trash_old_activities();

		$query_args = $GLOBALS['wpuat_test']['calls']['WP_Query'][0][0];
		$this->assertSame( 'ASC', $query_args['order'] );
	}
}
