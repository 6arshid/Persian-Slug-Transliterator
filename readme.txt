=== Persian Slug Transliterator ===
Contributors:      6arshid, hassantafreshi, whitestudio
Tags:              persian, slug, transliteration, farsi, seo
Requires at least: 5.0
Tested up to:      6.9
Requires PHP:      7.2
Stable tag:        1.0.0
License:           GPLv2 or later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html

Automatically transliterates Persian and Arabic post slugs to clean Latin characters. Includes a bulk update tool for existing content.

== Description ==

**Persian Slug Transliterator** automatically converts Persian (Farsi) and Arabic characters in post slugs (permalinks) to their Latin equivalents. This ensures clean, SEO-friendly, and universally readable URLs.

= Features =

* **Automatic Transliteration** — New posts and pages automatically get clean Latin slugs.
* **Bulk Update Tool** — Convert existing Persian/Arabic slugs in batch from the WordPress admin.
* **Custom Post Type Support** — Works with posts, pages, and any registered public custom post type.
* **Arabic Normalization** — Handles Arabic character variants (ي → ی, ك → ک, etc.).
* **Numeral Conversion** — Converts Persian/Arabic numerals (۰-۹, ٠-٩) to Western digits (0-9).
* **ZWNJ Handling** — Properly handles Zero-Width Non-Joiner characters common in Persian text.
* **Duplicate Slug Prevention** — Ensures unique slugs even after transliteration.
* **Translation Ready** — Fully internationalized with proper text domain.
* **Lightweight** — No external dependencies, no database tables, no JavaScript overhead.

= How It Works =

1. When you create or update a post, the plugin intercepts the slug generation via the `sanitize_title` filter.
2. Persian/Arabic characters are normalized and transliterated to their Latin equivalents.
3. Non-alphanumeric characters are replaced with hyphens.
4. The result is a clean, lowercase, hyphen-separated Latin slug.

= Example =

* **Title:** سلام دنیا
* **Generated Slug:** slam-dnya

= Bulk Update =

Navigate to **Tools → Persian Slugs** in the WordPress admin to batch-update existing posts and pages.

== Installation ==

1. Upload the `persian-slug-transliterator` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. That's it! New posts will automatically get Latin slugs.
4. To update existing posts, go to **Tools → Persian Slugs**.

== Frequently Asked Questions ==

= Does this plugin change my existing slugs automatically? =

No. Existing slugs are only changed when you use the **Bulk Update** tool under **Tools → Persian Slugs**. New posts will get Latin slugs automatically.

= Will this break my existing links? =

If you use the bulk update tool, old URLs will no longer work unless you set up 301 redirects. We recommend using a redirection plugin alongside.

= Does it work with custom post types? =

Yes. The automatic transliteration works with all post types. The bulk update tool also lists all public custom post types.

= Can I control how many posts are updated at once? =

Yes. The bulk update tool lets you set a batch size and offset for processing large sites in stages.

= Does the plugin store any data? =

No. The plugin does not create any database tables or store any options. It only modifies post slugs.

== Screenshots ==

1. Bulk update tool in the WordPress admin under Tools → Persian Slugs.

== Changelog ==

= 1.0.0 =
* Initial public release.

== Upgrade Notice ==

= 1.0.0 =
Major update with improved security, WordPress coding standards compliance, and i18n support. Recommended for all users.
