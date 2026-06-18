#!/usr/bin/env bash
# shellcheck disable=SC2016
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
ENV_FILE="$ROOT_DIR/local/.env"

source "$ROOT_DIR/tools/local/docker-access.sh"
rp_reexec_with_docker_group_if_needed "$ROOT_DIR/tools/local/smoke-test.sh" "$@"

if [ ! -f "$ENV_FILE" ]; then
	cp "$ROOT_DIR/local/.env.example" "$ENV_FILE"
fi

set -a
# shellcheck disable=SC1090
source "$ENV_FILE"
set +a

if ! command -v docker >/dev/null 2>&1 || ! docker compose version >/dev/null 2>&1; then
	cat <<'MSG'
Docker and Docker Compose are required for smoke tests.
Run `npm run local:check` for the exact Ubuntu package command.
MSG
	exit 1
fi

COMPOSE=(docker compose --env-file "$ENV_FILE" -f "$ROOT_DIR/local/docker-compose.yml")
source "$ROOT_DIR/tools/local/wordpress-core.sh"

"${COMPOSE[@]}" up -d --force-recreate db wordpress
rp_ensure_wordpress_core_version
curl -fsSI "$LOCAL_URL" >/dev/null
wp_version="$("${COMPOSE[@]}" run --rm cli core version)"
printf '%s\n' "$wp_version"
expected_wordpress_version="$(rp_expected_wordpress_version)"
case "$wp_version" in
	"$expected_wordpress_version"*)
		;;
	*)
		printf 'Expected local WordPress %s, found %s.\n' "$expected_wordpress_version" "$wp_version" >&2
		exit 1
		;;
esac
"${COMPOSE[@]}" run --rm cli eval 'if ( ! file_exists( WP_PLUGIN_DIR . "/reussitepersonnelle-core/reussitepersonnelle-core.php" ) ) { fwrite( STDERR, "Tracked plugin file is not mounted at wp-content/plugins/reussitepersonnelle-core/reussitepersonnelle-core.php" . PHP_EOL ); exit( 1 ); }'
"${COMPOSE[@]}" run --rm cli plugin is-active reussitepersonnelle-core
plugin_names="$("${COMPOSE[@]}" run --rm cli plugin list --field=name)"
if grep -qx 'ga4-analytics' <<<"$plugin_names"; then
	printf 'Obsolete ga4-analytics plugin is still present locally.\n' >&2
	exit 1
fi
"${COMPOSE[@]}" run --rm cli eval '$registry = WP_Block_Type_Registry::get_instance(); foreach ( array( "core/breadcrumbs", "core/navigation-overlay-close" ) as $block ) { if ( ! $registry->is_registered( $block ) ) { fwrite( STDERR, "Missing WordPress 7.0 core block: " . $block . PHP_EOL ); exit( 1 ); } }'
"${COMPOSE[@]}" run --rm cli eval '$registry = WP_Block_Type_Registry::get_instance(); foreach ( array( "reussitepersonnelle/related-posts", "reussitepersonnelle/topic-pathways", "reussitepersonnelle/topic-links", "reussitepersonnelle/footer-link-group" ) as $block ) { $block_type = $registry->get_registered( $block ); if ( ! $block_type ) { fwrite( STDERR, "Missing block: " . $block . PHP_EOL ); exit( 1 ); } if ( empty( $block_type->supports["autoRegister"] ) ) { fwrite( STDERR, "Block is not PHP-only auto-registered: " . $block . PHP_EOL ); exit( 1 ); } }'
"${COMPOSE[@]}" run --rm cli theme list --format=table

homepage_html="$(curl -fsS "$LOCAL_URL")"
tracking_script_count="$({ grep -o 'googletagmanager.com/gtag/js' <<<"$homepage_html" || true; } | wc -l | tr -d '[:space:]')"
if [ "$tracking_script_count" -ne 1 ]; then
	printf 'Expected one GA4 tracking script, found %s.\n' "$tracking_script_count" >&2
	exit 1
fi

if ! grep -q 'rp-topic-grid' <<<"$homepage_html" || ! grep -q 'Thèmes principaux' <<<"$homepage_html"; then
	printf 'Front page topic pathway section is missing.\n' >&2
	exit 1
fi

if grep -q 'rp-breadcrumbs' <<<"$homepage_html"; then
	printf 'Front page should not render breadcrumbs.\n' >&2
	exit 1
fi

if grep -q 'Chemins de lecture' <<<"$homepage_html"; then
	printf 'Front page still labels main categories as reading paths.\n' >&2
	exit 1
fi

topic_card_count="$({ grep -o 'rp-topic-card' <<<"$homepage_html" || true; } | wc -l | tr -d '[:space:]')"
if [ "$topic_card_count" -ne 7 ]; then
	printf 'Expected 7 front page topic cards, found %s.\n' "$topic_card_count" >&2
	exit 1
fi

previous_topic_position=-1
while IFS='|' read -r topic_slug topic_label; do
	topic_position="$({ grep -b -o "/category/${topic_slug}/" <<<"$homepage_html" || true; } | head -n1 | cut -d: -f1)"

	if [ -z "$topic_position" ]; then
		printf 'Front page is missing topic link: %s\n' "$topic_slug" >&2
		exit 1
	fi

	if [ "$topic_position" -le "$previous_topic_position" ]; then
		printf 'Front page topic link is out of order: %s\n' "$topic_slug" >&2
		exit 1
	fi

	if ! grep -q "$topic_label" <<<"$homepage_html"; then
		printf 'Front page is missing topic label: %s\n' "$topic_label" >&2
		exit 1
	fi

	previous_topic_position="$topic_position"
done <<'TOPICS'
identite-valeur-personnelle|Identité et valeur personnelle
emotions-securite-interieure|Émotions et sécurité intérieure
relations-limites|Relations et limites
action-habitudes-changement|Action, habitudes et changement
pensee-discernement-decision|Pensée, discernement et décision
conditions-vie-attention-energie|Conditions de vie, attention et énergie
sens-normes-reussite|Sens, normes et réussite personnelle
TOPICS

for retired_category_path in \
	category/developpement-personnel \
	category/motivation-et-productivite \
	category/emotions/ \
	category/relations/
do
	if grep -q "$retired_category_path" <<<"$homepage_html"; then
		printf 'Front page still links to retired category path: %s\n' "$retired_category_path" >&2
		exit 1
	fi
done

if ! grep -q 'rp-footer-links' <<<"$homepage_html"; then
	printf 'Plugin-rendered footer links are missing.\n' >&2
	exit 1
fi

assert_has_breadcrumbs() {
	local url="$1"
	local label="$2"
	local html

	html="$(curl -sS "$url")"
	if ! grep -q 'rp-breadcrumbs' <<<"$html"; then
		printf '%s breadcrumbs are missing.\n' "$label" >&2
		exit 1
	fi

	printf '%s\n' "$html"
}

extract_breadcrumbs() {
	perl -0ne 'print $1 if /(<nav[^>]*rp-breadcrumbs[\s\S]*?<\/nav>)/' <<<"$1"
}

assert_has_breadcrumbs "$LOCAL_URL/blog/" 'Blog page' >/dev/null
assert_has_breadcrumbs "$LOCAL_URL/?s=stress" 'Search page' >/dev/null
assert_has_breadcrumbs "$LOCAL_URL/a-propos/" 'Static page' >/dev/null
assert_has_breadcrumbs "$LOCAL_URL/page-introuvable-test/" '404 page' >/dev/null

category_html="$(assert_has_breadcrumbs "$LOCAL_URL/category/emotions-securite-interieure/" 'Category page')"
category_breadcrumbs="$(extract_breadcrumbs "$category_html")"
category_blog_position="$({ grep -b -o 'Blog' <<<"$category_breadcrumbs" || true; } | head -n1 | cut -d: -f1)"
category_topic_position="$({ grep -b -o 'Émotions et sécurité intérieure' <<<"$category_breadcrumbs" || true; } | head -n1 | cut -d: -f1)"
if [ -z "$category_blog_position" ] || [ -z "$category_topic_position" ] || [ "$category_blog_position" -ge "$category_topic_position" ]; then
	printf 'Category breadcrumbs should include Blog before the current category.\n' >&2
	exit 1
fi

sample_post_slug="$("${COMPOSE[@]}" run --rm cli post list --post_type=post --post_status=publish --posts_per_page=1 --field=post_name)"
if [ -n "$sample_post_slug" ]; then
	single_html="$(curl -fsS "$LOCAL_URL/$sample_post_slug/")"
	if ! grep -q 'rp-breadcrumbs' <<<"$single_html"; then
		printf 'Single post breadcrumbs are missing.\n' >&2
		exit 1
	fi
fi

printf 'Smoke tests passed for %s\n' "$LOCAL_URL"
