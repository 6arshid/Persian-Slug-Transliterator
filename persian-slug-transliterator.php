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
 * Load plugin text domain for translations.
 *
 * @since 1.0.0
 * @return void
 */
function pst_load_textdomain() {
	load_plugin_textdomain(
		'persian-slug-transliterator',
		false,
		dirname( PST_PLUGIN_BASENAME ) . '/languages'
	);
}
add_action( 'init', 'pst_load_textdomain' );

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
