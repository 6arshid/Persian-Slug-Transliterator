<?php
/**
 * Transliterator class.
 *
 * Handles the conversion of Persian/Arabic characters to Latin equivalents.
 *
 * @package PersianSlugTransliterator
 * @since   1.0.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PST_Transliterator
 *
 * Provides static methods for transliterating Persian/Arabic text to Latin.
 *
 * @since 1.0.0
 */
class PST_Transliterator {

	/**
	 * Persian/Arabic to Latin character map.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private static $char_map = array(
		'ا'  => 'a',
		'آ'  => 'a',
		'ب'  => 'b',
		'پ'  => 'p',
		'ت'  => 't',
		'ث'  => 's',
		'ج'  => 'j',
		'چ'  => 'ch',
		'ح'  => 'h',
		'خ'  => 'kh',
		'د'  => 'd',
		'ذ'  => 'z',
		'ر'  => 'r',
		'ز'  => 'z',
		'ژ'  => 'zh',
		'س'  => 's',
		'ش'  => 'sh',
		'ص'  => 's',
		'ض'  => 'z',
		'ط'  => 't',
		'ظ'  => 'z',
		'ع'  => '',
		'غ'  => 'gh',
		'ف'  => 'f',
		'ق'  => 'q',
		'ک'  => 'k',
		'گ'  => 'g',
		'ل'  => 'l',
		'م'  => 'm',
		'ن'  => 'n',
		'و'  => 'v',
		'ه'  => 'h',
		'ی'  => 'y',
		"\xE2\x80\x8C" => ' ', // Zero-width non-joiner (ZWNJ).
		"\xC2\xA0"     => ' ', // Non-breaking space (NBSP).
	);

	/**
	 * Arabic character normalization map.
	 *
	 * Maps common Arabic character variants to their Persian equivalents.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private static $normalization_map = array(
		'ي' => 'ی',
		'ك' => 'ک',
		'ة' => 'ه',
		'ۀ' => 'ه',
		'ؤ' => 'و',
		'إ' => 'ا',
		'أ' => 'ا',
		'ٱ' => 'ا',
		'ء' => '',
		'ئ' => 'ی',
		'آ' => 'a',
		'‌' => ' ', // ZWNJ.
		'ـ' => '',  // Tatweel.
	);

	/**
	 * Persian/Arabic numerals to Western Arabic numerals map.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private static $numeral_map = array(
		'۰' => '0',
		'۱' => '1',
		'۲' => '2',
		'۳' => '3',
		'۴' => '4',
		'۵' => '5',
		'۶' => '6',
		'۷' => '7',
		'۸' => '8',
		'۹' => '9',
		'٠' => '0',
		'١' => '1',
		'٢' => '2',
		'٣' => '3',
		'٤' => '4',
		'٥' => '5',
		'٦' => '6',
		'٧' => '7',
		'٨' => '8',
		'٩' => '9',
	);

	/**
	 * Filter callback for sanitize_title.
	 *
	 * Transliterates Persian/Arabic titles to clean Latin slugs.
	 *
	 * @since 1.0.0
	 *
	 * @param string $title     The sanitized title.
	 * @param string $raw_title The raw title before sanitization.
	 * @param string $context   The context for which the title is being sanitized.
	 * @return string The transliterated slug or the original title.
	 */
	public static function sanitize_title_filter( $title, $raw_title = '', $context = 'display' ) {
		if ( '' === $raw_title || null === $raw_title ) {
			return $title;
		}

		$slug = self::transliterate( $raw_title );

		// If transliteration results in an empty slug, return the original.
		if ( '' === $slug ) {
			return $title;
		}

		return $slug;
	}

	/**
	 * Transliterate a Persian/Arabic string to a Latin slug.
	 *
	 * @since 1.0.0
	 *
	 * @param string $text The text to transliterate.
	 * @return string The transliterated slug.
	 */
	public static function transliterate( $text ) {
		$text = trim( (string) $text );

		if ( '' === $text ) {
			return '';
		}

		// Normalize Arabic character variants to Persian.
		$text = str_replace(
			array_keys( self::$normalization_map ),
			array_values( self::$normalization_map ),
			$text
		);

		// Convert Persian/Arabic numerals to Western Arabic.
		$text = str_replace(
			array_keys( self::$numeral_map ),
			array_values( self::$numeral_map ),
			$text
		);

		// Transliterate Persian characters to Latin.
		$text = strtr( $text, self::$char_map );

		// Convert to lowercase.
		$text = strtolower( $text );

		// Replace any non-alphanumeric character with a hyphen.
		$text = preg_replace( '/[^a-z0-9]+/u', '-', $text );

		// Trim hyphens from both ends.
		$text = trim( $text, '-' );

		// Collapse multiple consecutive hyphens.
		$text = preg_replace( '/-+/', '-', $text );

		return $text;
	}

	/**
	 * Check whether a slug contains only Latin characters.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug The slug to check.
	 * @return bool True if slug is Latin-only, false otherwise.
	 */
	public static function is_latin_slug( $slug ) {
		return (bool) preg_match( '/^[a-z0-9-]+$/', $slug );
	}

	/**
	 * Check whether a value contains Persian/Arabic characters.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value The value to inspect.
	 * @return bool True when Persian/Arabic characters exist.
	 */
	public static function has_persian_or_arabic( $value ) {
		return (bool) preg_match( '/[\x{0600}-\x{06FF}]/u', (string) $value );
	}
}
