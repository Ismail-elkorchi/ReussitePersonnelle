<?php
/**
 * Plugin Name: Reussite Personnelle Core
 * Plugin URI: https://reussitepersonnelle.com
 * Description: Site-specific functionality for reussitepersonnelle.com.
 * Version: 0.1.0
 * Requires at least: 6.9
 * Requires PHP: 8.4
 * Author: Ismail El Korchi
 * Text Domain: reussitepersonnelle-core
 *
 * @package ReussitePersonnelleCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'REUSSITEPERSONNELLE_CORE_VERSION', '0.1.0' );
define( 'REUSSITEPERSONNELLE_CORE_FILE', __FILE__ );
define( 'REUSSITEPERSONNELLE_CORE_DIR', plugin_dir_path( __FILE__ ) );

require_once REUSSITEPERSONNELLE_CORE_DIR . 'includes/tracking.php';
