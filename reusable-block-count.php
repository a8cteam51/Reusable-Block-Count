<?php

/**
 * Plugin Name: Reusable Block Count
 * Description: Display a "Reusable blocks" listing page, and a link to view all posts containing a given block.
 * Author: georgestephanis
 * Author URI: http://wpspecialprojects.wordpress.com/
 * License: GPLv2+
 * Version: 1.0
 *
 * Loosely based on https://github.com/yeswework/fabrica-reusable-block-instances/
 *
 * All three translateable strings -- "Reusable blocks" "Posts" and "View Posts" were intentionally chosen as they are already translated by WordPress Core.
 */

class Reusable_Block_Count {

	/**
	 * Set up our hooks to integrate to the admin.
	 */
	public static function add_hooks() {
		if ( is_admin() ) {
			add_action( 'admin_menu', array( __CLASS__, 'show_reusable_blocks_page' ) );

			// Add the occurrences column to the `wp_blocks` cpt listing page.
			add_filter( 'manage_wp_block_posts_columns', array( __CLASS__, 'wp_block_add_column' ) );
			add_filter( 'manage_wp_block_posts_custom_column', array( __CLASS__, 'wp_block_render_column' ), 10, 2 );
		}
	}

	/**
	 * Simply adds the existing archive page to the admin menu, under the posts page.
	 */
	public static function show_reusable_blocks_page() {
		add_posts_page(
			null,
			ucwords( __( 'Reusable blocks' ) ),
			get_post_type_object( 'wp_block' )->cap->edit_posts,
			'edit.php?post_type=wp_block'
		);
	}

	/**
	 * Add a column to the `wp_blocks` core post type listing page.
	 *
	 * @param $columns (array) The associative array of columns for the list table.
	 * @return (array) The filtered associative array.
	 */
	public static function wp_block_add_column( $columns ) {
		$columns = array_merge(
			array_slice( $columns, 0, 2 ),
			array( 'occurrences' => __( 'Posts' ) ),
			array_slice( $columns, 2 )
		);
		return $columns;
	}

	/**
	 * Populate the column on each row.
	 *
	 * @param $column (string) The slug of the column currently being rendered.
	 * @param $post_id (int) The id of the row for this post's entry in the list table.
	 */
	public static function wp_block_render_column( $column, $post_id ) {
		if ( 'occurrences' === $column ) {
			$occurrences = self::count_posts_with_block( $post_id );
			if ( $occurrences ) {
				// Build the URL to query the normal admin posts page.
				$url = add_query_arg(
					array(
						's'         => rawurlencode( sprintf( '<!-- wp:block {"ref":%d}', $post_id ) ),
						'post_type' => 'post',
					),
					admin_url( 'edit.php' )
				);
				echo '<a href="' . esc_url( $url ) . '">' . intval( $occurrences ) . '</a>';
				echo '<div class="row-actions"><a href="' . esc_url( $url ) . '">' . esc_html__( 'View Posts' ) . '</a></div>';
			} else {
				echo '—';
			}
		}
	}

	/**
	 * Count the number of posts that the reusable block is used in.
	 *
	 * @param $block_id (int) The id of the reusable block in question.
	 * @return (int) The number of posts.
	 */
	public static function count_posts_with_block( $reusable_block_id ) {
		global $wpdb;
		return $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*)
				FROM $wpdb->posts
				WHERE `post_content` LIKE %s
					AND `post_type` = 'post'
					AND `post_status` IN ( 'publish', 'draft', 'future', 'pending' )",
				sprintf( '%%<!-- wp:block {"ref":%d}%%', (int) $reusable_block_id )
			)
		);
	}

}

Reusable_Block_Count::add_hooks();