<?php
/**
 * Breadcrumb trail preferences.
 *
 * @package ReussitePersonnelleCore
 */

namespace ReussitePersonnelle\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'block_core_breadcrumbs_post_type_settings', __NAMESPACE__ . '\\filter_breadcrumb_post_type_settings', 10, 3 );
add_filter( 'block_core_breadcrumbs_items', __NAMESPACE__ . '\\add_blog_item_to_category_breadcrumbs' );
add_filter( 'render_block_core/breadcrumbs', __NAMESPACE__ . '\\localize_breadcrumbs_aria_label' );

/**
 * Prefer the public editorial topic taxonomy trail for articles.
 *
 * @param array<string, string> $settings Current breadcrumb settings.
 * @param string                $post_type Current post type.
 * @param int                   $post_id Current post ID.
 * @return array<string, string>
 */
function filter_breadcrumb_post_type_settings( array $settings, string $post_type, int $post_id ): array {
	if ( 'post' !== $post_type ) {
		return $settings;
	}

	$settings['taxonomy'] = 'category';

	$preferred_topic = get_preferred_breadcrumb_topic_slug( $post_id );
	if ( '' !== $preferred_topic ) {
		$settings['term'] = $preferred_topic;
	}

	return $settings;
}

/**
 * Resolve the first assigned topic using the public topic order.
 *
 * @param int $post_id Current post ID.
 */
function get_preferred_breadcrumb_topic_slug( int $post_id ): string {
	$terms = get_the_terms( $post_id, 'category' );

	if ( empty( $terms ) || is_wp_error( $terms ) ) {
		return '';
	}

	$assigned_slugs = wp_list_pluck( $terms, 'slug' );

	foreach ( get_topics() as $topic ) {
		$topic_slug = $topic['slug'] ?? '';

		if ( '' !== $topic_slug && in_array( $topic_slug, $assigned_slugs, true ) ) {
			return $topic_slug;
		}
	}

	return '';
}

/**
 * Keep category archives under the article index in breadcrumb trails.
 *
 * @param array<int, array<string, mixed>> $items Breadcrumb items.
 * @return array<int, array<string, mixed>>
 */
function add_blog_item_to_category_breadcrumbs( array $items ): array {
	if ( ! is_category() || empty( $items ) ) {
		return $items;
	}

	$blog_item = get_blog_breadcrumb_item();
	if ( empty( $blog_item ) || breadcrumb_items_contain_url( $items, $blog_item['url'] ) ) {
		return $items;
	}

	$insert_offset = 0;
	if ( ! empty( $items[0]['url'] ) && breadcrumb_urls_match( home_url( '/' ), (string) $items[0]['url'] ) ) {
		$insert_offset = 1;
	}

	array_splice( $items, $insert_offset, 0, array( $blog_item ) );

	return $items;
}

/**
 * Build the Blog breadcrumb item from the WordPress posts page setting.
 *
 * @return array<string, string>
 */
function get_blog_breadcrumb_item(): array {
	$page_for_posts = (int) get_option( 'page_for_posts' );

	if ( $page_for_posts > 0 ) {
		$label = wp_strip_all_tags( get_the_title( $page_for_posts ) );
		if ( '' === $label ) {
			$label = __( 'Blog', 'reussitepersonnelle-core' );
		}

		return array(
			'label' => $label,
			'url'   => get_permalink( $page_for_posts ),
		);
	}

	return array(
		'label' => __( 'Blog', 'reussitepersonnelle-core' ),
		'url'   => home_url( '/blog/' ),
	);
}

/**
 * Check whether a breadcrumb trail already contains a URL.
 *
 * @param array<int, array<string, mixed>> $items Breadcrumb items.
 * @param string                           $url URL to find.
 */
function breadcrumb_items_contain_url( array $items, string $url ): bool {
	foreach ( $items as $item ) {
		if ( ! empty( $item['url'] ) && breadcrumb_urls_match( $url, (string) $item['url'] ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Compare breadcrumb URLs without trailing slash differences.
 */
function breadcrumb_urls_match( string $first_url, string $second_url ): bool {
	return untrailingslashit( $first_url ) === untrailingslashit( $second_url );
}

/**
 * Localize the Breadcrumbs block landmark label.
 *
 * @param string $block_content Rendered block HTML.
 */
function localize_breadcrumbs_aria_label( string $block_content ): string {
	if ( '' === $block_content ) {
		return $block_content;
	}

	$processor = new \WP_HTML_Tag_Processor( $block_content );

	if ( $processor->next_tag( array( 'class_name' => 'wp-block-breadcrumbs' ) ) ) {
		$processor->set_attribute( 'aria-label', __( 'Fil d’Ariane', 'reussitepersonnelle-core' ) );

		return $processor->get_updated_html();
	}

	return $block_content;
}
