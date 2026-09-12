<?php

use PHPUnit\Framework\TestCase;

final class CapabilitiesTest extends TestCase {
	public function capability_provider() {
		return array(
			'read'   => array( 'read_activity', array( 'list_users' ) ),
			'create' => array( 'create_activities', array( 'do_not_allow' ) ),
			'edit'   => array( 'edit_activity', array( 'list_users' ) ),
			'delete' => array( 'delete_activity', array( 'list_users' ) ),
		);
	}

	/**
	 * @dataProvider capability_provider
	 */
	public function test_maps_activity_capabilities( $capability, $expected ) {
		$GLOBALS['wpuat_test'] = array();
		$this->assertSame( $expected, wp_user_activity_meta_caps( array( 'existing' ), $capability, 7 ) );
	}

	public function test_preserves_unknown_capabilities() {
		$GLOBALS['wpuat_test'] = array();
		$this->assertSame( array( 'existing' ), wp_user_activity_meta_caps( array( 'existing' ), 'unknown_activity_cap' ) );
	}
}
