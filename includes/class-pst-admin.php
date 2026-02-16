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
	 * Option key for showing the first-install popup.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const ACTIVATION_POPUP_OPTION = 'pst_show_activation_popup';

	/**
	 * Initialize admin hooks.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
		add_action( 'admin_post_pst_bulk_update', array( $this, 'handle_bulk_update' ) );
		add_action( 'admin_init', array( $this, 'handle_popup_dismissal' ) );
		add_action( 'admin_footer', array( $this, 'render_activation_popup' ) );
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
				<?php esc_html_e( 'This tool converts existing Persian/Arabic slugs for posts, pages, categories, and tags to clean Latin characters based on their names.', 'persian-slug-transliterator' ); ?>
			</p>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="pst_bulk_update" />
				<?php wp_nonce_field( self::NONCE_ACTION, self::NONCE_FIELD ); ?>

				<table class="form-table" role="presentation">
					<tr>
						<th scope="row">
							<?php esc_html_e( 'Items to translate', 'persian-slug-transliterator' ); ?>
						</th>
						<td>
							<label><input type="checkbox" name="targets[]" value="post" checked="checked" /> <?php esc_html_e( 'Posts', 'persian-slug-transliterator' ); ?></label><br />
							<label><input type="checkbox" name="targets[]" value="page" checked="checked" /> <?php esc_html_e( 'Pages', 'persian-slug-transliterator' ); ?></label><br />
							<label><input type="checkbox" name="targets[]" value="category" checked="checked" /> <?php esc_html_e( 'Categories', 'persian-slug-transliterator' ); ?></label><br />
							<label><input type="checkbox" name="targets[]" value="post_tag" checked="checked" /> <?php esc_html_e( 'Tags', 'persian-slug-transliterator' ); ?></label>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="pst_limit"><?php esc_html_e( 'Batch Size', 'persian-slug-transliterator' ); ?></label>
						</th>
						<td>
							<input type="number" name="limit" id="pst_limit" value="200" min="1" max="2000" class="small-text" />
							<p class="description">
								<?php esc_html_e( 'Number of items to process per selected type/taxonomy.', 'persian-slug-transliterator' ); ?>
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
								<?php esc_html_e( 'Skip this many items from each selected type/taxonomy.', 'persian-slug-transliterator' ); ?>
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
								<?php esc_html_e( 'If a tag/category slug is already Persian, its own Persian slug will be kept unless force is enabled.', 'persian-slug-transliterator' ); ?>
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
	 * Handle popup dismissal action.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function handle_popup_dismissal() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce checked below if action exists.
		if ( empty( $_GET['pst_popup_action'] ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$action = sanitize_key( wp_unslash( $_GET['pst_popup_action'] ) );

		if ( 'dismiss' !== $action ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, 'pst_popup_dismiss' ) ) {
			return;
		}

		delete_option( self::ACTIVATION_POPUP_OPTION );
		wp_safe_redirect( remove_query_arg( array( 'pst_popup_action', '_wpnonce' ) ) );
		exit;
	}

	/**
	 * Render first-install popup to run translation quickly.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function render_activation_popup() {
		if ( ! current_user_can( 'manage_options' ) || ! get_option( self::ACTIVATION_POPUP_OPTION ) ) {
			return;
		}

		$dismiss_url = wp_nonce_url(
			add_query_arg( 'pst_popup_action', 'dismiss', admin_url() ),
			'pst_popup_dismiss'
		);
		?>
		<div id="pst-activation-modal" style="position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:99999;display:flex;align-items:center;justify-content:center;">
			<div style="background:#fff;max-width:560px;width:92%;padding:24px;border-radius:6px;box-shadow:0 15px 35px rgba(0,0,0,.2);">
				<h2 style="margin-top:0;"><?php esc_html_e( 'Translate existing slugs now?', 'persian-slug-transliterator' ); ?></h2>
				<p><?php esc_html_e( 'Select which content you want to transliterate. Persian tags/categories are detected from their names and updated to clean slugs.', 'persian-slug-transliterator' ); ?></p>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="pst_bulk_update" />
					<input type="hidden" name="limit" value="2000" />
					<input type="hidden" name="offset" value="0" />
					<?php wp_nonce_field( self::NONCE_ACTION, self::NONCE_FIELD ); ?>
					<p>
						<label><input type="checkbox" name="targets[]" value="post" checked="checked" /> <?php esc_html_e( 'Posts', 'persian-slug-transliterator' ); ?></label><br />
						<label><input type="checkbox" name="targets[]" value="page" checked="checked" /> <?php esc_html_e( 'Pages', 'persian-slug-transliterator' ); ?></label><br />
						<label><input type="checkbox" name="targets[]" value="category" checked="checked" /> <?php esc_html_e( 'Categories', 'persian-slug-transliterator' ); ?></label><br />
						<label><input type="checkbox" name="targets[]" value="post_tag" checked="checked" /> <?php esc_html_e( 'Tags', 'persian-slug-transliterator' ); ?></label>
					</p>
					<?php submit_button( esc_html__( 'Start Translation', 'persian-slug-transliterator' ), 'primary', 'submit', false ); ?>
					<a href="<?php echo esc_url( $dismiss_url ); ?>" class="button" style="margin-left:8px;"><?php esc_html_e( 'Not now', 'persian-slug-transliterator' ); ?></a>
				</form>
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
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die(
				esc_html__( 'You do not have sufficient permissions to access this page.', 'persian-slug-transliterator' ),
				403
			);
		}

		if (
			! isset( $_POST[ self::NONCE_FIELD ] ) ||
			! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ self::NONCE_FIELD ] ) ), self::NONCE_ACTION )
		) {
			wp_die(
				esc_html__( 'Security check failed. Please try again.', 'persian-slug-transliterator' ),
				403
			);
		}

		$targets = array();
		if ( isset( $_POST['targets'] ) && is_array( $_POST['targets'] ) ) {
			$targets = array_map( 'sanitize_key', wp_unslash( $_POST['targets'] ) );
		}

		if ( empty( $targets ) ) {
			$targets = array( 'post', 'page' );
		}

		$allowed_targets = array( 'post', 'page', 'category', 'post_tag' );
		$targets         = array_values( array_intersect( $allowed_targets, $targets ) );

		$limit  = isset( $_POST['limit'] ) ? absint( $_POST['limit'] ) : 200;
		$offset = isset( $_POST['offset'] ) ? absint( $_POST['offset'] ) : 0;
		$force  = ! empty( $_POST['force'] );

		$limit = max( 1, min( 2000, $limit ) );

		$changed = 0;
		$skipped = 0;

		foreach ( $targets as $target ) {
			if ( in_array( $target, array( 'category', 'post_tag' ), true ) ) {
				$result  = $this->bulk_update_terms( $target, $limit, $offset, $force );
				$changed += $result['changed'];
				$skipped += $result['skipped'];
				continue;
			}

			if ( post_type_exists( $target ) ) {
				$result  = $this->bulk_update_posts( $target, $limit, $offset, $force );
				$changed += $result['changed'];
				$skipped += $result['skipped'];
			}
		}

		delete_option( self::ACTIVATION_POPUP_OPTION );
		flush_rewrite_rules( false );

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

	/**
	 * Bulk update slugs for a post type.
	 *
	 * @since 1.0.0
	 *
	 * @param string $post_type Post type slug.
	 * @param int    $limit     Number of items to process.
	 * @param int    $offset    Query offset.
	 * @param bool   $force     Whether to force overwrite latin slugs.
	 * @return array{changed:int,skipped:int}
	 */
	private function bulk_update_posts( $post_type, $limit, $offset, $force ) {
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

		if ( empty( $query->posts ) ) {
			return array(
				'changed' => 0,
				'skipped' => 0,
			);
		}

		foreach ( $query->posts as $post_id ) {
			$post = get_post( $post_id );

			if ( ! $post ) {
				++$skipped;
				continue;
			}

			$current_slug = (string) $post->post_name;
			$new_slug     = PST_Transliterator::transliterate( $post->post_title );

			if ( '' === $new_slug ) {
				++$skipped;
				continue;
			}

			if ( ! $force && '' !== $current_slug && PST_Transliterator::is_latin_slug( $current_slug ) ) {
				++$skipped;
				continue;
			}

			$unique_slug = wp_unique_post_slug(
				$new_slug,
				$post_id,
				$post->post_status,
				$post->post_type,
				$post->post_parent
			);

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

		return array(
			'changed' => $changed,
			'skipped' => $skipped,
		);
	}

	/**
	 * Bulk update slugs for a taxonomy.
	 *
	 * @since 1.0.0
	 *
	 * @param string $taxonomy Taxonomy name.
	 * @param int    $limit    Number of items to process.
	 * @param int    $offset   Query offset.
	 * @param bool   $force    Whether to force overwrite latin slugs.
	 * @return array{changed:int,skipped:int}
	 */
	private function bulk_update_terms( $taxonomy, $limit, $offset, $force ) {
		$terms = get_terms(
			array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
				'number'     => $limit,
				'offset'     => $offset,
			)
		);

		$changed = 0;
		$skipped = 0;

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return array(
				'changed' => 0,
				'skipped' => is_wp_error( $terms ) ? $limit : 0,
			);
		}

		foreach ( $terms as $term ) {
			$current_slug = (string) $term->slug;
			$new_slug     = PST_Transliterator::transliterate( $term->name );

			if ( '' === $new_slug ) {
				++$skipped;
				continue;
			}

			if ( ! $force && '' !== $current_slug && PST_Transliterator::is_latin_slug( $current_slug ) ) {
				++$skipped;
				continue;
			}

			if ( ! $force && '' !== $current_slug && PST_Transliterator::has_persian_or_arabic( $current_slug ) ) {
				++$skipped;
				continue;
			}

			if ( ! $force && $current_slug === $new_slug ) {
				++$skipped;
				continue;
			}

			$result = wp_update_term(
				$term->term_id,
				$taxonomy,
				array(
					'slug' => $new_slug,
				)
			);

			if ( is_wp_error( $result ) ) {
				++$skipped;
			} else {
				++$changed;
			}
		}

		return array(
			'changed' => $changed,
			'skipped' => $skipped,
		);
	}
}
