<?php
/**
 * Plugin Name:       Persian Slug Transliterator
 * Plugin URI:        https://github.com/6arshid/Persian-Slug-Transliterator
 * Description:       Automatically transliterates Persian/Arabic post slugs to clean Latin characters. Includes a bulk update tool for existing content.
 * Version:           1.0.0
 * Requires at least: 5.0
 * Requires PHP:      7.2
 * Author:            6arshid
 * Author URI:        https://github.com/6arshid
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       persian-slug-transliterator
 * Domain Path:       /languages
 *
 * @package PersianSlugTransliterator
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin version constant.
 */
define( 'PST_VERSION', '1.0.0' );

/**
 * Plugin directory path constant.
 */
define( 'PST_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Plugin directory URL constant.
 */
define( 'PST_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Plugin basename constant.
 */
define( 'PST_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Include required files.
 */
require_once PST_PLUGIN_DIR . 'includes/class-pst-transliterator.php';
require_once PST_PLUGIN_DIR . 'includes/class-pst-admin.php';


/**
 * Register the sanitize_title filter for automatic transliteration.
 *
 * @since 1.0.0
 * @return void
 */
function pst_register_sanitize_filter() {
	add_filter( 'sanitize_title', array( 'PST_Transliterator', 'sanitize_title_filter' ), 9, 3 );
}
add_action( 'init', 'pst_register_sanitize_filter' );

/**
 * Normalize incoming taxonomy slugs containing Persian/Arabic characters.
 *
 * Allows legacy Persian tag/category URLs to keep working after transliteration.
 *
 * @since 1.0.1
 *
 * @param array $query_vars Main request query vars.
 * @return array Filtered query vars.
 */
function pst_normalize_taxonomy_request_slugs( $query_vars ) {
	if ( ! is_array( $query_vars ) || empty( $query_vars ) ) {
		return $query_vars;
	}

	$taxonomy_map = array(
		'tag'           => 'post_tag',
		'category_name' => 'category',
		'term'          => isset( $query_vars['taxonomy'] ) ? $query_vars['taxonomy'] : '',
	);

	foreach ( $taxonomy_map as $key => $taxonomy ) {
		if ( empty( $query_vars[ $key ] ) || ! is_string( $query_vars[ $key ] ) ) {
			continue;
		}

		if ( empty( $taxonomy ) || ! taxonomy_exists( $taxonomy ) ) {
			continue;
		}

		$incoming_slug = rawurldecode( $query_vars[ $key ] );
		$resolved_slug = pst_resolve_taxonomy_request_slug( $incoming_slug, $taxonomy );

		if ( '' !== $resolved_slug ) {
			$query_vars[ $key ] = $resolved_slug;
		}
	}

	return $query_vars;
}
add_filter( 'request', 'pst_normalize_taxonomy_request_slugs', 9 );

/**
 * Resolve an incoming taxonomy request slug to an existing term slug.
 *
 * Supports direct slug matches, transliterated matches, and name-based lookup.
 *
 * @since 1.0.1
 *
 * @param string $incoming_slug Incoming slug from the request.
 * @param string $taxonomy      Taxonomy name.
 * @return string Resolved term slug, or empty string if no match is found.
 */
function pst_resolve_taxonomy_request_slug( $incoming_slug, $taxonomy ) {
	$incoming_slug = trim( (string) $incoming_slug );

	if ( '' === $incoming_slug || '' === $taxonomy ) {
		return '';
	}

	$direct_match = get_term_by( 'slug', $incoming_slug, $taxonomy );
	if ( $direct_match instanceof WP_Term ) {
		return $direct_match->slug;
	}

	if ( PST_Transliterator::has_persian_or_arabic( $incoming_slug ) ) {
		$transliterated = PST_Transliterator::transliterate( $incoming_slug );

		if ( '' !== $transliterated ) {
			$transliterated_match = get_term_by( 'slug', $transliterated, $taxonomy );
			if ( $transliterated_match instanceof WP_Term ) {
				return $transliterated_match->slug;
			}
		}

		$name_match = get_term_by( 'name', $incoming_slug, $taxonomy );
		if ( $name_match instanceof WP_Term ) {
			return $name_match->slug;
		}

		$name_candidates = array_unique(
			array_filter(
				array(
					$incoming_slug,
					str_replace( '-', ' ', $incoming_slug ),
					str_replace( '-', '‌', $incoming_slug ),
				)
			)
		);

		foreach ( $name_candidates as $name_candidate ) {
			$name_candidate_match = get_term_by( 'name', $name_candidate, $taxonomy );

			if ( $name_candidate_match instanceof WP_Term ) {
				return $name_candidate_match->slug;
			}
		}
	}

	return '';
}

/**
 * Initialize admin functionality.
 *
 * @since 1.0.0
 * @return void
 */
function pst_admin_init() {
	if ( is_admin() ) {
		$admin = new PST_Admin();
		$admin->init();
	}
}
add_action( 'plugins_loaded', 'pst_admin_init' );

/**
 * Plugin activation hook.
 *
 * @since 1.0.0
 * @return void
 */
function pst_activate() {
	add_option( 'pst_show_activation_popup', 1 );
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'pst_activate' );

/**
 * Plugin deactivation hook.
 *
 * @since 1.0.0
 * @return void
 */
function pst_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'pst_deactivate' );

/**
 * Add settings link on the Plugins page.
 *
 * @since 1.0.0
 * @param array $links Array of existing plugin action links.
 * @return array Modified array of plugin action links.
 */
function pst_plugin_action_links( $links ) {
	$settings_link = sprintf(
		'<a href="%s">%s</a>',
		esc_url( admin_url( 'tools.php?page=persian-slug-transliterator' ) ),
		esc_html__( 'Bulk Update', 'persian-slug-transliterator' )
	);
	array_unshift( $links, $settings_link );
	return $links;
}
add_filter( 'plugin_action_links_' . PST_PLUGIN_BASENAME, 'pst_plugin_action_links' );
