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
					'default' => 'themes',
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
			'slug'        => 'emotions-securite-interieure',
			'label'       => 'Émotions et sécurité intérieure',
			'description' => 'Peur, stress, culpabilité, anxiété : reconnaître le signal sans se laisser gouverner par lui.',
		),
		array(
			'slug'        => 'relations-limites',
			'label'       => 'Relations et limites',
			'description' => 'Amour, jalousie, manipulation, affirmation de soi : créer du lien sans s’effacer ni posséder.',
		),
		array(
			'slug'        => 'identite-valeur-personnelle',
			'label'       => 'Identité et valeur personnelle',
			'description' => 'Confiance, estime, regard des autres : habiter sa place sans réduire sa valeur à ses réussites.',
		),
		array(
			'slug'        => 'action-habitudes-changement',
			'label'       => 'Action, habitudes et changement',
			'description' => 'Motivation, volonté, courage, habitudes : agir sans attendre l’élan parfait ni se brutaliser.',
		),
		array(
			'slug'        => 'conditions-vie-attention-energie',
			'label'       => 'Conditions de vie, attention et énergie',
			'description' => 'Sommeil, fatigue, attention, productivité : construire des conditions qui rendent la vie plus habitable.',
		),
		array(
			'slug'        => 'pensee-discernement-decision',
			'label'       => 'Pensée, discernement et décision',
			'description' => 'Choix, incertitude, lecture, jugement : penser plus clairement quand tout ne peut pas être garanti.',
		),
		array(
			'slug'        => 'sens-normes-reussite',
			'label'       => 'Sens, normes et réussite',
			'description' => 'Réussite, liberté, valeurs, bonheur : avancer sans confondre transformation et conformité.',
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
 * Resolve a category URL from its slug.
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

	return '';
}

/**
 * Return topics that resolve to an existing category.
 *
 * @return array<int, array<string, string>>
 */
function get_renderable_topics(): array {
	$topics = array();

	foreach ( get_topics() as $topic ) {
		$slug = (string) ( $topic['slug'] ?? '' );

		if ( '' === $slug || '' === get_topic_url( $slug ) ) {
			continue;
		}

		$topics[] = $topic;
	}

	return $topics;
}

/**
 * Render the homepage topic pathway block.
 *
 * @return string
 */
function render_topic_pathways_block(): string {
	$topics   = get_renderable_topics();
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
	$topics = get_renderable_topics();

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
		'themes'  => array(
			'title' => 'Thèmes',
			'links' => array(
				array(
					'topic' => 'emotions-securite-interieure',
				),
				array(
					'topic' => 'relations-limites',
				),
				array(
					'topic' => 'identite-valeur-personnelle',
				),
			),
		),
		'reperes' => array(
			'title' => 'Repères',
			'links' => array(
				array(
					'topic' => 'action-habitudes-changement',
				),
				array(
					'topic' => 'conditions-vie-attention-energie',
				),
				array(
					'topic' => 'pensee-discernement-decision',
				),
				array(
					'topic' => 'sens-normes-reussite',
				),
			),
		),
		'site'    => array(
			'title' => 'Site',
			'links' => array(
				array(
					'label' => 'Accueil',
					'url'   => home_url( '/' ),
				),
				array(
					'label' => 'Blog',
					'url'   => home_url( '/blog/' ),
				),
				array(
					'label' => 'À propos',
					'url'   => home_url( '/a-propos/' ),
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
	$group_key = isset( $attributes['group'] ) ? sanitize_key( (string) $attributes['group'] ) : 'themes';
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
