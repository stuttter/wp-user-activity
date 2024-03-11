<?php

/**
 * User Activity Cron
 *
 * @package User/Activity/Cron
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Schedule cron
 *
 * @since 2.3.0
 * @return void
 */
function wp_user_activity_schedule_cron() {

    // Define task name
    $task = 'wp_user_activity_trash_old_activities';

    // Schedule cron
    if ( ! wp_next_scheduled( $task ) ) {
        wp_schedule_event( time(), 'daily', $task );
    }
}

/**
 * Trash old activities
 *
 * This function is used to trash old activity items, both to keep the
 * database clean and to improve performance.
 *
 * It is scheduled to run daily, and will trash a maximum of 100 items,
 * but if it retrieves the maximum number of items, it will schedule the
 * next cron to run in 60 seconds, effectively running the cron until
 * there are no more items to trash.
 *
 * @since 2.3.0
 * @return void
 */
function wp_user_activity_trash_old_activities() {

    /**
     * Filter the number of days to keep trashed activities
     *
     * @since 2.3.0
     * @param int $days_to_keep The number of days to keep trashed activities
     * @return int
     */
    $days_to_keep = (int) apply_filters( 'wp_user_activity_trash_days_to_keep', 14 );

    /**
     * Filter the time until the next cron loop
     *
     * @since 2.3.0
     * @param int $time_until_next_cron The time until the next cron loop
     * @return int
     */
    $time_until_next_cron = (int) apply_filters( 'wp_user_activity_trash_time', 60 );

    /**
     * Filter the maximum number of activities to trash each time
     *
     * @since 2.3.0
     * @param int $max_trash_each_time The maximum number of activities to trash each time
     * @return int
     */
    $max_trash_each_time = (int) apply_filters( 'wp_user_activity_trash_each', 100 );

    // Date arguments
    $date_args = array(
        'before' => date( 'Y-m-d H:i:s', strtotime( "-{$days_to_keep} days" ) )
    );

    // Query arguments
    $args = array(
        'fields'         => 'ids',
        'post_type'      => wp_user_activity_get_post_type(),
        'post_status'    => 'publish',
        'orderby'        => 'post_date',
        'order'          => 'DESC',
        'date_query'     => $date_args,
        'posts_per_page' => $max_trash_each_time,
    );

    // Get activities
    $activities = new WP_Query( $args );

    // Name of the single event
    $single = 'wp_user_activity_trash_old_activities_loop';

    // Bail if no activities
    if ( empty( $activities ) ) {
        wp_clear_scheduled_hook( $single );
        wp_clear_scheduled_hook( __FUNCTION__ );
        return;
    }

    // Schedule next cron if we retrieved the max number of activities
    if ( ( $activities->post_count === $max_trash_each_time ) && ! wp_next_scheduled( $single ) ) {
        wp_schedule_single_event( time() + $time_until_next_cron, $single );
    }

    // Trash all activities
    foreach ( $activities->posts as $activity_id ) {
        wp_trash_post( $activity_id );
    }
}
