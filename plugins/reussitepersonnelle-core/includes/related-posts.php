<?php
/**
 * Related post rendering.
 *
 * @package ReussitePersonnelleCore
 */

namespace ReussitePersonnelle\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', __NAMESPACE__ . '\\register_related_posts_block' );

/**
 * Register the related-posts dynamic block.
 */
function register_related_posts_block(): void {
	register_block_type(
		'reussitepersonnelle/related-posts',
		array(
			'api_version'     => 3,
			'attributes'      => array(
				'postsPerPage' => array(
					'type'    => 'number',
					'default' => 3,
				),
			),
			'render_callback' => __NAMESPACE__ . '\\render_related_posts_block',
		)
	);
}

/**
 * Render the related-posts dynamic block.
 *
 * @param array<string, mixed> $attributes Block attributes.
 * @return string
 */
function render_related_posts_block( array $attributes ): string {
	$posts_per_page = isset( $attributes['postsPerPage'] ) ? (int) $attributes['postsPerPage'] : 3;

	return render_related_posts( $posts_per_page );
}

/**
 * Render related articles for a single post.
 *
 * @param int $posts_per_page Maximum related posts to show.
 * @return string
 */
function render_related_posts( int $posts_per_page = 3 ): string {
	if ( ! is_singular( 'post' ) ) {
		return '';
	}

	$post_id = get_the_ID();

	if ( ! $post_id ) {
		return '';
	}

	$posts_per_page = max( 1, min( 6, $posts_per_page ) );
	$category_ids   = wp_get_post_categories( $post_id );

	$query_args = array(
		'post__not_in'        => array( $post_id ),
		'posts_per_page'      => $posts_per_page,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		'post_status'         => 'publish',
	);

	if ( ! empty( $category_ids ) ) {
		$query_args['category__in'] = $category_ids;
	}

	$related_posts = get_posts( $query_args );

	if ( empty( $related_posts ) && ! empty( $category_ids ) ) {
		unset( $query_args['category__in'] );
		$related_posts = get_posts( $query_args );
	}

	if ( empty( $related_posts ) ) {
		return '';
	}

	$title_id = wp_unique_id( 'rp-related-posts-title-' );

	ob_start();
	?>
	<section class="rp-related-posts" aria-labelledby="<?php echo esc_attr( $title_id ); ?>">
		<p class="rp-section-label">À lire ensuite</p>
		<h2 id="<?php echo esc_attr( $title_id ); ?>" class="wp-block-heading has-x-large-font-size">Articles liés</h2>
		<div class="rp-related-grid">
			<?php foreach ( $related_posts as $related_post ) : ?>
				<article class="rp-related-card">
					<h3><a href="<?php echo esc_url( get_permalink( $related_post ) ); ?>"><?php echo esc_html( get_the_title( $related_post ) ); ?></a></h3>
					<p><?php echo esc_html( wp_trim_words( get_the_excerpt( $related_post ), 22 ) ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>
	</section>
	<?php

	return trim( (string) ob_get_clean() );
}
