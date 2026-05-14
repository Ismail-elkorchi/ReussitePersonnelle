<?php
/**
 * Curated topic and footer navigation rendering.
 *
 * @package ReussitePersonnelleCore
 */

namespace ReussitePersonnelle\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', __NAMESPACE__ . '\\register_topic_blocks' );

/**
 * Register topic/navigation dynamic blocks.
 */
function register_topic_blocks(): void {
	register_block_type(
		'reussitepersonnelle/topic-pathways',
		array(
			'api_version'     => 3,
			'render_callback' => __NAMESPACE__ . '\\render_topic_pathways_block',
		)
	);

	register_block_type(
		'reussitepersonnelle/topic-links',
		array(
			'api_version'     => 3,
			'render_callback' => __NAMESPACE__ . '\\render_topic_links_block',
		)
	);

	register_block_type(
		'reussitepersonnelle/footer-link-group',
		array(
			'api_version'     => 3,
			'attributes'      => array(
				'group' => array(
					'type'    => 'string',
					'default' => 'read',
				),
			),
			'render_callback' => __NAMESPACE__ . '\\render_footer_link_group_block',
		)
	);
}

/**
 * Return the editorial topic map used across the site.
 *
 * @return array<int, array<string, string>>
 */
function get_topics(): array {
	$topics = array(
		array(
			'slug'        => 'emotions',
			'label'       => 'Émotions',
			'description' => 'Peur, stress, culpabilité ou jalousie : comprendre le signal avant de réagir.',
		),
		array(
			'slug'        => 'relations',
			'label'       => 'Relations',
			'description' => 'Critique, manipulation, séduction, limites : rester soi-même sans se fermer aux autres.',
		),
		array(
			'slug'        => 'identite-valeur-personnelle',
			'label'       => 'Valeur personnelle',
			'description' => 'Confiance, regard des autres, affirmation de soi : prendre sa place sans écraser.',
		),
		array(
			'slug'        => 'motivation-et-productivite',
			'label'       => 'Motivation',
			'description' => 'Habitudes, maîtrise de soi, temps, élan : agir même quand l’envie fluctue.',
		),
		array(
			'slug'        => 'developpement-personnel',
			'label'       => 'Développement personnel',
			'description' => 'Des repères pour progresser sans promesse magique ni pression de performance.',
		),
	);

	/**
	 * Filters the curated topic map used by Reussite Personnelle blocks.
	 *
	 * @param array<int, array<string, string>> $topics Topic definitions.
	 */
	return (array) apply_filters( 'reussitepersonnelle_topics', $topics );
}

/**
 * Resolve a curated topic by slug.
 *
 * @param string $slug Topic/category slug.
 * @return array<string, string>|null
 */
function get_topic_by_slug( string $slug ): ?array {
	foreach ( get_topics() as $topic ) {
		if ( $slug === ( $topic['slug'] ?? '' ) ) {
			return $topic;
		}
	}

	return null;
}

/**
 * Resolve a category URL from its slug, with a stable path fallback.
 *
 * @param string $slug Category slug.
 */
function get_topic_url( string $slug ): string {
	$term = get_term_by( 'slug', $slug, 'category' );

	if ( $term && ! is_wp_error( $term ) ) {
		$link = get_term_link( $term );

		if ( ! is_wp_error( $link ) ) {
			return $link;
		}
	}

	return home_url( '/category/' . trim( $slug, '/' ) . '/' );
}

/**
 * Render the homepage topic pathway block.
 *
 * @return string
 */
function render_topic_pathways_block(): string {
	$topics   = get_topics();
	$title_id = wp_unique_id( 'rp-topic-pathways-title-' );

	if ( empty( $topics ) ) {
		return '';
	}

	ob_start();
	?>
	<section class="wp-block-group alignfull rp-section is-layout-constrained wp-block-group-is-layout-constrained" aria-labelledby="<?php echo esc_attr( $title_id ); ?>">
		<div class="wp-block-group alignwide rp-section-header">
			<div class="wp-block-group">
				<p class="rp-section-label">Chemins de lecture</p>
				<h2 id="<?php echo esc_attr( $title_id ); ?>" class="wp-block-heading">Choisissez par point de départ</h2>
			</div>
		</div>

		<div class="wp-block-group alignwide rp-topic-grid">
			<?php foreach ( $topics as $index => $topic ) : ?>
				<article class="wp-block-group rp-topic-card">
					<p class="rp-topic-index"><?php echo esc_html( sprintf( '%02d', $index + 1 ) ); ?></p>
					<h3 class="wp-block-heading"><a href="<?php echo esc_url( get_topic_url( $topic['slug'] ) ); ?>"><?php echo esc_html( $topic['label'] ); ?></a></h3>
					<p><?php echo esc_html( $topic['description'] ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>
	</section>
	<?php

	return trim( (string) ob_get_clean() );
}

/**
 * Render compact topic links for archives.
 *
 * @return string
 */
function render_topic_links_block(): string {
	$topics = get_topics();

	if ( empty( $topics ) ) {
		return '';
	}

	ob_start();
	?>
	<nav class="wp-block-group rp-topic-links" aria-label="Thèmes principaux">
		<?php foreach ( $topics as $topic ) : ?>
			<p><a href="<?php echo esc_url( get_topic_url( $topic['slug'] ) ); ?>"><?php echo esc_html( $topic['label'] ); ?></a></p>
		<?php endforeach; ?>
	</nav>
	<?php

	return trim( (string) ob_get_clean() );
}

/**
 * Return footer link groups.
 *
 * @return array<string, array<string, mixed>>
 */
function get_footer_link_groups(): array {
	return array(
		'read'       => array(
			'title' => 'Lire',
			'links' => array(
				array(
					'label' => 'Tous les articles',
					'url'   => home_url( '/blog/' ),
				),
				array(
					'topic' => 'developpement-personnel',
				),
				array(
					'topic' => 'motivation-et-productivite',
				),
			),
		),
		'understand' => array(
			'title' => 'Comprendre',
			'links' => array(
				array(
					'topic' => 'emotions',
				),
				array(
					'topic' => 'relations',
				),
				array(
					'topic' => 'identite-valeur-personnelle',
				),
			),
		),
		'site'       => array(
			'title' => 'Site',
			'links' => array(
				array(
					'label' => 'Accueil',
					'url'   => home_url( '/' ),
				),
				array(
					'label' => 'À propos',
					'url'   => home_url( '/a-propos/' ),
				),
				array(
					'label' => 'Blog',
					'url'   => home_url( '/blog/' ),
				),
			),
		),
	);
}

/**
 * Render one footer link group.
 *
 * @param array<string, mixed> $attributes Block attributes.
 * @return string
 */
function render_footer_link_group_block( array $attributes ): string {
	$group_key = isset( $attributes['group'] ) ? sanitize_key( (string) $attributes['group'] ) : 'read';
	$groups    = get_footer_link_groups();

	if ( ! isset( $groups[ $group_key ] ) ) {
		return '';
	}

	$group = $groups[ $group_key ];

	ob_start();
	?>
	<div class="wp-block-group">
		<p class="rp-footer-heading"><?php echo esc_html( $group['title'] ); ?></p>
		<p class="rp-footer-links">
			<?php foreach ( $group['links'] as $link ) : ?>
				<?php
				if ( isset( $link['topic'] ) ) {
					$topic = get_topic_by_slug( (string) $link['topic'] );

					if ( null === $topic ) {
						continue;
					}

					$url   = get_topic_url( $topic['slug'] );
					$label = $topic['label'];
				} else {
					$url   = (string) ( $link['url'] ?? '' );
					$label = (string) ( $link['label'] ?? '' );
				}

				if ( '' === $url || '' === $label ) {
					continue;
				}
				?>
				<a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a>
			<?php endforeach; ?>
		</p>
	</div>
	<?php

	return trim( (string) ob_get_clean() );
}
