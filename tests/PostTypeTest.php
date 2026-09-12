<?php

use PHPUnit\Framework\TestCase;

final class PostTypeTest extends TestCase {
	public function test_registers_a_private_exportable_activity_post_type() {
		$GLOBALS['wpuat_test'] = array();

		wp_user_activity_register_post_types();

		$call = $GLOBALS['wpuat_test']['calls']['register_post_type'][0];
		$this->assertSame( 'activity', $call[0] );
		$this->assertFalse( $call[1]['public'] );
		$this->assertFalse( $call[1]['publicly_queryable'] );
		$this->assertTrue( $call[1]['show_ui'] );
		$this->assertTrue( $call[1]['can_export'] );
		$this->assertTrue( $call[1]['delete_with_user'] );
		$this->assertSame( 'do_not_allow', wp_user_activity_meta_caps( array(), 'create_activities' )[0] );
	}

	public function test_prepends_the_activity_action_to_activity_content() {
		$GLOBALS['wpuat_test'] = array();
		$post = new WP_Post();
		$post->ID = 12;
		$post->post_type = 'activity';
		$GLOBALS['wpuat_test']['returns']['get_post'] = $post;
		$GLOBALS['wpuat_test']['callbacks']['get_post_meta'] = function ( $post_id, $key ) {
			$values = array(
				'wp_user_activity_object_type' => 'posts',
				'wp_user_activity_action'      => 'updated',
			);
			return isset( $values[ $key ] ) ? $values[ $key ] : '';
		};
		$GLOBALS['wpuat_test']['returns']['apply_filters:wp_get_user_activity_posts_updated'] = 'Alice updated a post.';

		$this->assertSame( 'Alice updated a post.<br>Details', wp_user_activity_append_action_to_the_content( 'Details' ) );
	}

	public function test_leaves_content_alone_without_an_activity_post_context() {
		$GLOBALS['wpuat_test'] = array();
		$this->assertSame( 'Details', wp_user_activity_append_action_to_the_content( 'Details' ) );
	}

	public function test_restores_an_activity_to_its_previous_status() {
		$GLOBALS['wpuat_test'] = array();
		$GLOBALS['wpuat_test']['returns']['get_post_type'] = 'activity';
		$this->assertSame( 'publish', wp_user_activity_untrash_to_previous_status( 'draft', 12, 'publish' ) );
	}
}
