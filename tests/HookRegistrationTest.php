<?php

use PHPUnit\Framework\TestCase;

final class HookRegistrationTest extends TestCase {
	public function test_registers_the_activity_storage_and_capability_hooks() {
		$actions = $GLOBALS['wpuat_initial_calls']['add_action'];
		$filters = $GLOBALS['wpuat_initial_calls']['add_filter'];

		$this->assertContains( array( 'init', 'wp_user_activity_register_post_types' ), $actions );
		$this->assertContains( array( 'init', 'wp_user_activity_register_post_metadata' ), $actions );
		$this->assertContains( array( 'init', 'wp_user_activity_register_default_types', 11 ), $actions );
		$this->assertContains( array( 'map_meta_cap', 'wp_user_activity_meta_caps', 10, 4 ), $filters );
		$this->assertContains( array( 'wp_untrash_post_status', 'wp_user_activity_untrash_to_previous_status', 10, 3 ), $filters );
	}
}
