<?php
/**
 * Admin class.
 *
 * Handles the admin UI for bulk updating Persian/Arabic slugs.
 *
 * @package PersianSlugTransliterator
 * @since   1.0.0
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PST_Admin
 *
 * Provides the admin tools page and handles bulk slug update requests.
 *
 * @since 1.0.0
 */
class PST_Admin {

	/**
	 * Nonce action name for the bulk update form.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const NONCE_ACTION = 'pst_bulk_update_slugs';

	/**
	 * Nonce field name.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const NONCE_FIELD = '_pst_nonce';

	/**
	 * Admin page slug.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const PAGE_SLUG = 'persian-slug-transliterator';

	/**
	 * Initialize admin hooks.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
		add_action( 'admin_post_pst_bulk_update', array( $this, 'handle_bulk_update' ) );
	}

	/**
	 * Register the admin tools page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function register_admin_menu() {
		add_management_page(
			__( 'Persian Slug Transliterator', 'persian-slug-transliterator' ),
			__( 'Persian Slugs', 'persian-slug-transliterator' ),
			'manage_options',
			self::PAGE_SLUG,
			array( $this, 'render_admin_page' )
		);
	}

	/**
	 * Render the admin tools page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function render_admin_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only GET params for displaying results.
		$done    = isset( $_GET['pst_done'] ) ? absint( $_GET['pst_done'] ) : 0;
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$skipped = isset( $_GET['pst_skipped'] ) ? absint( $_GET['pst_skipped'] ) : 0;

		?>
		<div class="wrap">
			<h1><?php echo esc_html__( 'Persian Slug Transliterator', 'persian-slug-transliterator' ); ?></h1>

			<?php if ( $done || $skipped ) : ?>
				<div class="notice notice-success is-dismissible">
					<p>
						<?php
						printf(
							/* translators: 1: Number of updated slugs. 2: Number of skipped slugs. */
							esc_html__( 'Bulk update completed. Updated: %1$s — Skipped: %2$s', 'persian-slug-transliterator' ),
							'<strong>' . esc_html( number_format_i18n( $done ) ) . '</strong>',
							'<strong>' . esc_html( number_format_i18n( $skipped ) ) . '</strong>'
						);
						?>
					</p>
				</div>
			<?php endif; ?>

			<p>
				<?php esc_html_e( 'This tool converts existing Persian/Arabic slugs of posts and pages to clean Latin characters based on the post title.', 'persian-slug-transliterator' ); ?>
			</p>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="pst_bulk_update" />
				<?php wp_nonce_field( self::NONCE_ACTION, self::NONCE_FIELD ); ?>

				<table class="form-table" role="presentation">
					<tr>
						<th scope="row">
							<label for="pst_post_type"><?php esc_html_e( 'Content Type', 'persian-slug-transliterator' ); ?></label>
						</th>
						<td>
							<select name="post_type" id="pst_post_type">
								<option value="post"><?php esc_html_e( 'Posts', 'persian-slug-transliterator' ); ?></option>
								<option value="page"><?php esc_html_e( 'Pages', 'persian-slug-transliterator' ); ?></option>
								<?php
								// Allow custom post types.
								$custom_post_types = get_post_types(
									array(
										'public'   => true,
										'_builtin' => false,
									),
									'objects'
								);
								foreach ( $custom_post_types as $cpt ) {
									printf(
										'<option value="%s">%s</option>',
										esc_attr( $cpt->name ),
										esc_html( $cpt->labels->name )
									);
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="pst_limit"><?php esc_html_e( 'Batch Size', 'persian-slug-transliterator' ); ?></label>
						</th>
						<td>
							<input type="number" name="limit" id="pst_limit" value="200" min="1" max="2000" class="small-text" />
							<p class="description">
								<?php esc_html_e( 'Number of posts to process per batch. Use a smaller number for large sites to avoid timeouts.', 'persian-slug-transliterator' ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="pst_offset"><?php esc_html_e( 'Offset', 'persian-slug-transliterator' ); ?></label>
						</th>
						<td>
							<input type="number" name="offset" id="pst_offset" value="0" min="0" class="small-text" />
							<p class="description">
								<?php esc_html_e( 'Skip this many posts from the beginning. Use for batch processing (e.g., 0, 200, 400, ...).', 'persian-slug-transliterator' ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php esc_html_e( 'Force Overwrite', 'persian-slug-transliterator' ); ?>
						</th>
						<td>
							<label for="pst_force">
								<input type="checkbox" name="force" id="pst_force" value="1" />
								<?php esc_html_e( 'Overwrite slugs that are already in Latin characters.', 'persian-slug-transliterator' ); ?>
							</label>
							<p class="description">
								<?php esc_html_e( 'Not recommended unless you want all slugs regenerated uniformly.', 'persian-slug-transliterator' ); ?>
							</p>
						</td>
					</tr>
				</table>

				<?php submit_button( esc_html__( 'Start Bulk Update', 'persian-slug-transliterator' ) ); ?>
			</form>

			<hr />

			<div class="notice notice-warning inline">
				<p>
					<strong><?php esc_html_e( 'Important:', 'persian-slug-transliterator' ); ?></strong>
					<?php esc_html_e( 'After updating slugs, consider setting up 301 redirects from old URLs to new ones to preserve SEO and avoid broken links.', 'persian-slug-transliterator' ); ?>
				</p>
			</div>
		</div>
		<?php
	}

	/**
	 * Handle the bulk update form submission.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function handle_bulk_update() {
		// Verify user capability.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die(
				esc_html__( 'You do not have sufficient permissions to access this page.', 'persian-slug-transliterator' ),
				403
			);
		}

		// Verify nonce.
		if (
			! isset( $_POST[ self::NONCE_FIELD ] ) ||
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ self::NONCE_FIELD ] ) ), self::NONCE_ACTION )
		) {
			wp_die(
				esc_html__( 'Security check failed. Please try again.', 'persian-slug-transliterator' ),
				403
			);
		}

		// Sanitize inputs.
		$post_type = isset( $_POST['post_type'] ) ? sanitize_key( wp_unslash( $_POST['post_type'] ) ) : 'post';
		$limit     = isset( $_POST['limit'] ) ? absint( $_POST['limit'] ) : 200;
		$offset    = isset( $_POST['offset'] ) ? absint( $_POST['offset'] ) : 0;
		$force     = ! empty( $_POST['force'] );

		// Validate post type exists and is public.
		if ( ! post_type_exists( $post_type ) ) {
			$post_type = 'post';
		}

		// Clamp limit to a safe range.
		$limit = max( 1, min( 2000, $limit ) );

		// Query posts.
		$query = new WP_Query(
			array(
				'post_type'      => $post_type,
				'post_status'    => 'any',
				'posts_per_page' => $limit,
				'offset'         => $offset,
				'orderby'        => 'ID',
				'order'          => 'ASC',
				'fields'         => 'ids',
				'no_found_rows'  => true,
			)
		);

		$changed = 0;
		$skipped = 0;

		if ( ! empty( $query->posts ) ) {
			foreach ( $query->posts as $post_id ) {
				$post = get_post( $post_id );

				if ( ! $post ) {
					++$skipped;
					continue;
				}

				$current_slug = (string) $post->post_name;
				$new_slug     = PST_Transliterator::transliterate( $post->post_title );

				// Skip if transliteration yields empty slug.
				if ( '' === $new_slug ) {
					++$skipped;
					continue;
				}

				// Skip already-Latin slugs unless force is enabled.
				if ( ! $force && '' !== $current_slug && PST_Transliterator::is_latin_slug( $current_slug ) ) {
					++$skipped;
					continue;
				}

				// Ensure slug uniqueness.
				$unique_slug = wp_unique_post_slug(
					$new_slug,
					$post_id,
					$post->post_status,
					$post->post_type,
					$post->post_parent
				);

				// Update the post slug.
				$result = wp_update_post(
					array(
						'ID'        => $post_id,
						'post_name' => $unique_slug,
					),
					true
				);

				if ( is_wp_error( $result ) ) {
					++$skipped;
				} else {
					++$changed;
				}
			}
		}

		// Flush rewrite rules to prevent 404s.
		flush_rewrite_rules( false );

		// Redirect back with result counts.
		$redirect_url = add_query_arg(
			array(
				'page'        => self::PAGE_SLUG,
				'pst_done'    => $changed,
				'pst_skipped' => $skipped,
			),
			admin_url( 'tools.php' )
		);

		wp_safe_redirect( $redirect_url );
		exit;
	}
}
