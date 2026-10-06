<?php
/**
 * Post activity action tests.
 *
 * @package User/Activity/Tests
 */

use PHPUnit\Framework\TestCase;

require_once dirname( __DIR__ ) . '/wp-user-activity/includes/classes.php';
require_once dirname( __DIR__ ) . '/wp-user-activity/includes/actions/class-action-posts.php';

/**
 * Post activity action tests.
 */
final class PostActionsTest extends TestCase {

	/**
	 * Post activity type under test.
	 *
	 * @var WP_User_Activity_Type_Posts
	 */
	private $type;

	/**
	 * Set up a post activity type and bypass persistence.
	 */
	protected function setUp(): void {
		$GLOBALS['wpuat_test'] = array();

		$GLOBALS['wp_user_activity_actions'] = array();

		$GLOBALS['wpuat_test']['returns']['is_post_type_hierarchical'] = false;

		$GLOBALS['wpuat_test']['returns']['apply_filters:wp_pre_insert_user_activity'] = true;

		$this->type = new WP_User_Activity_Type_Posts();
	}

	/**
	 * Status actions expose stable keys and human-readable messages.
	 */
	public function test_registers_post_status_actions() {
		$this->assertSame( 'Draft', $this->type->action_callbacks['draft']->name );
		$this->assertSame( '%1$s changed the "%2$s" %3$s status to draft %4$s.', $this->type->action_callbacks['draft']->message );
		$this->assertSame( 'Pending', $this->type->action_callbacks['pending']->name );
		$this->assertSame( '%1$s submitted the "%2$s" %3$s for review %4$s.', $this->type->action_callbacks['pending']->message );
		$this->assertSame( 'Publish', $this->type->action_callbacks['publish']->name );
		$this->assertSame( '%1$s published the "%2$s" %3$s %4$s.', $this->type->action_callbacks['publish']->message );
	}

	/**
	 * Each requested destination status is stored as its own action.
	 *
	 * @param string $expected_action Expected activity action.
	 * @param string $new_status      New post status.
	 * @param string $old_status      Old post status.
	 *
	 * @dataProvider status_transition_provider
	 */
	public function test_logs_post_status_transitions( $expected_action, $new_status, $old_status ) {
		$this->assertSame( $expected_action, $this->log_transition( $new_status, $old_status ) );
	}

	/**
	 * Post status transition cases.
	 */
	public function status_transition_provider() {
		return array(
			'published to draft'   => array( 'draft', 'draft', 'publish' ),
			'draft to pending'     => array( 'pending', 'pending', 'draft' ),
			'pending to published' => array( 'publish', 'publish', 'pending' ),
			'draft to scheduled'   => array( 'future', 'future', 'draft' ),
		);
	}

	/**
	 * Saving without a status change remains an update.
	 *
	 * @param string $status Post status.
	 *
	 * @dataProvider unchanged_status_provider
	 */
	public function test_logs_unchanged_status_as_an_update( $status ) {
		$this->assertSame( 'update', $this->log_transition( $status, $status ) );
	}

	/**
	 * Unchanged status cases.
	 */
	public function unchanged_status_provider() {
		return array(
			'draft'     => array( 'draft' ),
			'pending'   => array( 'pending' ),
			'published' => array( 'publish' ),
			'scheduled' => array( 'future' ),
		);
	}

	/**
	 * The first save remains a creation activity.
	 */
	public function test_logs_the_first_save_as_creation() {
		$this->assertSame( 'create', $this->log_transition( 'draft', 'auto-draft' ) );
	}

	/**
	 * Discarding an auto-draft is a trash activity, not a creation.
	 */
	public function test_logs_a_discarded_auto_draft_as_trash() {
		$this->assertSame( 'trash', $this->log_transition( 'trash', 'auto-draft' ) );
	}

	/**
	 * Log a transition and return the action passed to insertion.
	 *
	 * @param string $new_status New post status.
	 * @param string $old_status Old post status.
	 * @return string
	 */
	private function log_transition( $new_status, $old_status ) {
		$post = (object) array(
			'ID'          => 17,
			'post_parent' => 0,
			'post_status' => $new_status,
			'post_title'  => 'Status test',
			'post_type'   => 'post',
		);

		$GLOBALS['wpuat_test']['returns']['get_post'] = $post;
		$this->type->transition_post_status( $new_status, $old_status, $post );

		$calls = $GLOBALS['wpuat_test']['calls']['apply_filters:wp_pre_insert_user_activity'];
		$args  = end( $calls );

		return $args[2]['action'];
	}
}
