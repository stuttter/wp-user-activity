<?php

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

// Include the main list table class if it's not included
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

// No list table class, so something went very wrong
if ( class_exists( 'WP_List_Table' ) ) :
/**
 * User Activity list table
 *
 * This list table is responsible for showing the activity of a user
 * in a metabox, similar to comments in posts and pages, but in conjunction with
 * the WP User Profiles plugin.
 *
 * @since 0.1.4
 */
class WP_User_Activity_List_table extends WP_List_Table {

	/**
	 * The main constructor method
	 *
	 * @since 0.1.4
	 */
	public function __construct( $args = array() ) {
		$args = array(
			'singular' => esc_html__( 'Activity', 'wp-user-activity' ),
			'plural'   => esc_html__( 'Activity', 'wp-user-activity' ),
			'ajax'     => true
		);
		parent::__construct( $args );
	}

	/**
	 * Setup the list-table's columns
	 *
	 * @since 0.1.4
	 *
	 * @see WP_List_Table::::single_row_columns()
	 *
	 * @return array An associative array containing column information
	 */
	public function get_columns() {
		$columns = wp_user_activity_manage_posts_columns();
		unset( $columns['cb'] );
		return $columns;
	}

	/**
	 * Allow columns to be sortable
	 *
	 * @since 0.1.4
	 *
	 * @return array An associative array containing the sortable columns
	 */
	public function get_sortable_columns() {
		return wp_user_activity_sortable_columns();
	}

	/**
	 * Setup the bulk actions
	 *
	 * @since 0.1.4
	 *
	 * @return array An associative array containing all the bulk actions
	 */
	public function get_bulk_actions() {
		return array();
	}

	/**
	 * Output the contents of the `type` column
	 *
	 * @since 0.1.4
	 */
	public function column_activity_type( $item = '' ) {
		echo wp_user_activity_manage_custom_column_data( 'activity_type', $item->ID );
	}

	/**
	 * Output the contents of the `username` column
	 *
	 * @since 0.1.4
	 */
	public function column_activity_username( $item = '' ) {
		echo wp_user_activity_manage_custom_column_data( 'activity_username', $item->ID );
	}

	/**
	 * Output the contents of the `when` column
	 *
	 * @since 0.1.4
	 */
	public function column_activity_when( $item = '' ) {
		echo wp_user_activity_manage_custom_column_data( 'activity_when', $item->ID );
	}

	/**
	 * Output the contents of the `session` column
	 *
	 * @since 0.1.4
	 */
	public function column_activity_session( $item = '' ) {
		echo wp_user_activity_manage_custom_column_data( 'activity_session', $item->ID );
	}

	/**
	 * Prepare the list-table items for display
	 *
	 * @since 0.1.4
	 *
	 * @uses $this->_column_headers
	 * @uses $this->items
	 * @uses $this->get_columns()
	 * @uses $this->get_sortable_columns()
	 * @uses $this->get_pagenum()
	 * @uses $this->set_pagination_args()
	 */
	public function prepare_items( $user = null ) {

		// Set column headers
		$this->_column_headers = array(
			$this->get_columns(),
			array(),
			$this->get_sortable_columns()
		);

		// Handle bulk actions
		$this->process_bulk_action();

		// Query parameters
		$per_page     = 10;
		$current_page = $this->get_pagenum();
		$orderby      = ( ! empty( $_REQUEST['orderby'] ) ) ? $_REQUEST['orderby'] : 'date';
		$order        = ( ! empty( $_REQUEST['order']   ) ) ? $_REQUEST['order']   : 'asc';

		// Query for activity
		$query = new WP_Query( array(
			'post_type'           => 'activity',
			'post_status'         => 'publish',
			'author'              => $user->ID,
			'posts_per_page'      => $per_page,
			'paged'               => $current_page,
			'orderby'             => $orderby,
			'order'               => ucwords( $order ),
			'hierarchical'        => false,
			'ignore_sticky_posts' => true
		) );

		// Get count
		$total_items = count( $query->posts );

		// Set list table items to queried posts
		$this->items = $query->posts;

		// Set the pagination arguments
		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil( $total_items / $per_page )
		) );
	}

	/**
	 * Message to be displayed when there are no items
	 *
	 * @since 0.1.4
	 */
	public function no_items() {
		esc_html_e( 'No activity.', 'wp-user-activity' );
	}

	/**
	 * Display the table
	 *
	 * @since 0.1.4
	 *
	 * @access public
	 */
	public function display() {

		ob_start();

		// Top
		$this->display_tablenav( 'top' ); ?>

		<table class="wp-list-table <?php echo implode( ' ', $this->get_table_classes() ); ?>">
			<thead>
				<tr>
					<?php $this->print_column_headers(); ?>
				</tr>
			</thead>

			<tbody id="the-list" data-wp-lists='list:<?php echo $this->_args['singular']; ?>'>
				<?php $this->display_rows_or_placeholder(); ?>
			</tbody>

			<tfoot>
				<tr>
					<?php $this->print_column_headers( false ); ?>
				</tr>
			</tfoot>
		</table>

		<?php

		// Bottom
		$this->display_tablenav( 'bottom' );

		// Flush the buffer
		ob_end_flush();
	}

	/**
	 * Generate the table navigation above or below the table
	 *
	 * @since 0.1.4
	 *
	 * @access protected
	 * @param string $which
	 */
	protected function display_tablenav( $which ) {
	?>

		<div class="tablenav <?php echo esc_attr( $which ); ?>">
			<?php
				$this->extra_tablenav( $which );
				$this->pagination( $which );
			?>
			<br class="clear" />
		</div>
	<?php
	}
}
endif;
