<?php
/**
 * Uninstall handler.
 *
 * Fires when the plugin is deleted via the WordPress admin.
 *
 * @package PersianSlugTransliterator
 * @since   1.0.0
 */

// Prevent direct access and ensure this is a legitimate uninstall.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// This plugin does not store any options or custom database tables,
// so no cleanup is required. This file is provided for completeness
// and future-proofing.
