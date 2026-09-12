<?php

use PHPUnit\Framework\TestCase;

final class MetadataTest extends TestCase {
	public function test_registers_each_activity_metadata_key_with_its_sanitizer() {
		$GLOBALS['wpuat_test'] = array();

		wp_user_activity_register_post_metadata();

		$calls = $GLOBALS['wpuat_test']['calls']['register_meta'];
		$this->assertCount( 5, $calls );
		$sanitizers = array();
		foreach ( $calls as $call ) {
			$sanitizers[ $call[1] ] = $call[2]['sanitize_callback'];
		}
		$this->assertSame(
			array(
				'wp_user_activity_object_type'    => 'wp_user_activity_sanitize_object_type',
				'wp_user_activity_object_subtype' => 'wp_user_activity_sanitize_object_subtype',
				'wp_user_activity_object_name'    => 'wp_user_activity_sanitize_object_name',
				'wp_user_activity_object_id'      => 'wp_user_activity_sanitize_object_id',
				'wp_user_activity_action'         => 'wp_user_activity_sanitize_object_action',
			),
			$sanitizers
		);
		foreach ( $calls as $call ) {
			$this->assertSame( 'post', $call[0] );
			$this->assertSame( 'activity', $call[2]['object_subtype'] );
		}
	}

	public function test_sanitizes_keys_and_numeric_object_ids() {
		$this->assertSame( 'postupdated', wp_user_activity_sanitize_object_type( 'Post Updated!' ) );
		$this->assertSame( 'loggedin', wp_user_activity_sanitize_object_action( 'Logged In!' ) );
		$this->assertSame( 42, wp_user_activity_sanitize_object_id( '-42' ) );
		$this->assertSame( 'Original Name', wp_user_activity_sanitize_object_name( 'Original Name' ) );
	}
}
