<?php

use PHPUnit\Framework\TestCase;

final class InsertActivityTest extends TestCase {
	public function test_inserts_a_private_activity_record_with_prefixed_metadata() {
		global $merged_filters, $wp_filter;

		$GLOBALS['wpuat_test'] = array();
		$GLOBALS['wpuat_test']['returns']['get_current_user_id'] = 9;
		$GLOBALS['wpuat_test']['returns']['is_user_logged_in'] = false;
		$GLOBALS['wpuat_test']['returns']['wp_insert_post'] = 44;
		$_SERVER['REMOTE_ADDR'] = '192.0.2.8';
		$_SERVER['HTTP_USER_AGENT'] = 'Test Browser';
		$wp_filter = array( 'transition_post_status' => array( 10 => array( 'callback' ) ) );
		$merged_filters = array( 'transition_post_status' => true );

		$activity_id = wp_insert_user_activity( array(
			'object_type' => 'post',
			'object_id'   => 27,
			'action'      => 'updated',
		) );

		$this->assertSame( 44, $activity_id );
		$post = $GLOBALS['wpuat_test']['calls']['wp_insert_post'][0][0];
		$this->assertSame( 'activity', $post['post_type'] );
		$this->assertSame( 9, $post['post_author'] );
		$this->assertSame( 'publish', $post['post_status'] );
		$this->assertSame( 'post', $post['meta_input']['wp_user_activity_object_type'] );
		$this->assertSame( 27, $post['meta_input']['wp_user_activity_object_id'] );
		$this->assertSame( '192.0.2.8', $post['meta_input']['wp_user_activity_ip'] );
		$this->assertSame( 'Test Browser', $post['meta_input']['wp_user_activity_ua'] );
		$this->assertArrayHasKey( 'transition_post_status', $wp_filter );
		$this->assertArrayHasKey( 'transition_post_status', $merged_filters );
	}
}
