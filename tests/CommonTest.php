<?php

use PHPUnit\Framework\TestCase;

final class CommonTest extends TestCase {
	public function test_initializes_and_reuses_the_activity_type_registry() {
		$GLOBALS['wpuat_test'] = array();
		$GLOBALS['wp_user_activity_actions'] = null;

		$this->assertSame( array(), wp_user_activity_get_all_actions() );
		$GLOBALS['wp_user_activity_actions']['posts'] = 'registered';
		$this->assertSame( array( 'posts' => 'registered' ), wp_user_activity_get_all_actions() );
	}

	public function test_exposes_the_default_activity_type_contract() {
		$GLOBALS['wpuat_test'] = array();
		$types = wp_get_default_user_activity_types();

		$this->assertCount( 12, $types );
		$this->assertContains( 'WP_User_Activity_Type_Attachment', $types );
		$this->assertContains( 'WP_User_Activity_Type_Site_Settings', $types );
		$this->assertContains( 'WP_User_Activity_Type_User', $types );
	}

	public function test_reads_the_complete_activity_metadata_contract() {
		$GLOBALS['wpuat_test'] = array();
		$GLOBALS['wpuat_test']['callbacks']['get_post_meta'] = function ( $post_id, $key ) {
			return $post_id . ':' . $key;
		};

		$meta = wp_user_activity_get_meta( 14 );

		$this->assertSame( array( 'object_type', 'object_subtype', 'object_name', 'object_id', 'action', 'ip', 'ua' ), array_keys( $meta ) );
		$this->assertSame( '14:wp_user_activity_object_type', $meta['object_type'] );
		$this->assertSame( '14:wp_user_activity_ua', $meta['ua'] );
	}

	public function test_sanitizes_anonymous_request_context() {
		$GLOBALS['wpuat_test'] = array();
		$GLOBALS['wpuat_test']['returns']['is_user_logged_in'] = false;
		$_SERVER['REMOTE_ADDR'] = '2001:db8::1<script>';
		$_SERVER['HTTP_USER_AGENT'] = str_repeat( 'a', 300 );

		$this->assertSame( '2001:db8::1c', wp_user_activity_current_user_ip() );
		$this->assertSame( 254, strlen( wp_user_activity_current_user_ua() ) );
	}
}
