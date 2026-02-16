<?php
/**
 * Plugin Name: Persian Slug Transliterator
 * Description: تبدیل خودکار نامک‌های فارسی/عربی به لاتین + ابزار بروزرسانی گروهی برای نوشته‌های قدیمی.
 * Version: 1.2.0
 * Author: 6arshid
 * License: GPLv2 or later
 */

if (!defined('ABSPATH')) exit;

class PST_Persian_Slug_Transliterator {

    const NONCE_ACTION = 'pst_bulk_update_slugs';

    public static function init() {
        add_filter('sanitize_title', [__CLASS__, 'sanitize_title'], 9, 3);
        add_action('admin_menu', [__CLASS__, 'admin_menu']);
        add_action('admin_post_pst_bulk_update', [__CLASS__, 'handle_bulk_update']);

        register_activation_hook(__FILE__, [__CLASS__, 'activate']);
        register_deactivation_hook(__FILE__, [__CLASS__, 'deactivate']);
    }

    public static function activate() { flush_rewrite_rules(); }
    public static function deactivate() { flush_rewrite_rules(); }

    public static function sanitize_title($title, $raw_title, $context) {
        if ($raw_title === '' || $raw_title === null) return $title;

        $slug = self::transliterate($raw_title);

        // اگر خالی شد، بگذار وردپرس خودش بسازد
        if ($slug === '') return $title;

        return $slug;
    }

    private static function transliterate($text) {
        $text = trim((string)$text);

        // یکسان‌سازی حروف عربی/فارسی رایج
        $text = str_replace(
            ['ي','ك','ة','ۀ','ؤ','إ','أ','ٱ','ء','ئ','آ','‌','ـ'],
            ['ی','ک','ه','ه','و','ا','ا','ا','','ی','a',' ',''],
            $text
        );

        // اعداد فارسی/عربی به انگلیسی
        $text = str_replace(
            ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹','٠','١','٢','٣','٤','٥','٦','٧','٨','٩'],
            ['0','1','2','3','4','5','6','7','8','9','0','1','2','3','4','5','6','7','8','9'],
            $text
        );

        // نگاشت حروف فارسی به لاتین
        $map = [
            'ا'=>'a','آ'=>'a','ب'=>'b','پ'=>'p','ت'=>'t','ث'=>'s','ج'=>'j','چ'=>'ch',
            'ح'=>'h','خ'=>'kh','د'=>'d','ذ'=>'z','ر'=>'r','ز'=>'z','ژ'=>'zh','س'=>'s',
            'ش'=>'sh','ص'=>'s','ض'=>'z','ط'=>'t','ظ'=>'z','ع'=>'','غ'=>'gh','ف'=>'f',
            'ق'=>'q','ک'=>'k','گ'=>'g','ل'=>'l','م'=>'m','ن'=>'n','و'=>'v','ه'=>'h',
            'ی'=>'y',
            "\xE2\x80\x8C" => ' ', // ZWNJ
            "\xC2\xA0"     => ' ', // NBSP
        ];

        $text = strtr($text, $map);
        $text = strtolower($text);

        $text = preg_replace('/[^a-z0-9]+/u', '-', $text);
        $text = trim($text, '-');
        $text = preg_replace('/-+/', '-', $text);

        return $text;
    }

    // ---------- Admin UI ----------
    public static function admin_menu() {
        add_management_page(
            'Persian Slug Transliterator',
            'Persian Slugs',
            'manage_options',
            'pst-persian-slugs',
            [__CLASS__, 'admin_page']
        );
    }

    public static function admin_page() {
        if (!current_user_can('manage_options')) return;

        $done = isset($_GET['pst_done']) ? intval($_GET['pst_done']) : 0;
        $skipped = isset($_GET['pst_skipped']) ? intval($_GET['pst_skipped']) : 0;

        ?>
        <div class="wrap">
            <h1>Persian Slug Transliterator</h1>

            <?php if ($done || $skipped): ?>
                <div class="notice notice-success is-dismissible">
                    <p>
                        بروزرسانی انجام شد. تغییر داده شد: <strong><?php echo esc_html($done); ?></strong>
                        — رد شد: <strong><?php echo esc_html($skipped); ?></strong>
                    </p>
                </div>
            <?php endif; ?>

            <p>این ابزار، اسلاگ نوشته‌ها/برگه‌های قدیمی را به لاتین تبدیل می‌کند. (روی عنوان پست کار می‌کند و اسلاگ را بازنویسی می‌کند)</p>

            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="pst_bulk_update">
                <?php wp_nonce_field(self::NONCE_ACTION, '_pst_nonce'); ?>

                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label for="post_type">نوع محتوا</label></th>
                        <td>
                            <select name="post_type" id="post_type">
                                <option value="post">Posts (نوشته‌ها)</option>
                                <option value="page">Pages (برگه‌ها)</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="limit">تعداد (برای هر اجرا)</label></th>
                        <td>
                            <input type="number" name="limit" id="limit" value="200" min="1" max="2000">
                            <p class="description">اگر سایت بزرگ است، با 200 یا 500 بزن تا فشار نیاد.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="offset">Offset</label></th>
                        <td>
                            <input type="number" name="offset" id="offset" value="0" min="0">
                            <p class="description">برای ادامه دادن مرحله‌ای، دفعه بعد offset را مثلاً 200، 400، 600… بگذار.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="force">اجبار به بازنویسی</label></th>
                        <td>
                            <label>
                                <input type="checkbox" name="force" id="force" value="1">
                                حتی اگر اسلاگ الان لاتین است هم دوباره بساز
                            </label>
                            <p class="description">پیشنهاد: تیک نزن مگر اینکه بخوای همه از نو یکدست بشن.</p>
                        </td>
                    </tr>
                </table>

                <?php submit_button('شروع بروزرسانی اسلاگ‌ها'); ?>
            </form>

            <hr>
            <p><strong>نکته مهم:</strong> بعد از تغییر اسلاگ‌ها، اگر لینک‌های قدیمی در گوگل/شبکه‌ها هست، بهتره ریدایرکت 301 هم داشته باشی (می‌تونم کد ریدایرکت هم بهت بدم).</p>
        </div>
        <?php
    }

    public static function handle_bulk_update() {
        if (!current_user_can('manage_options')) wp_die('Forbidden');

        if (!isset($_POST['_pst_nonce']) || !wp_verify_nonce($_POST['_pst_nonce'], self::NONCE_ACTION)) {
            wp_die('Invalid nonce');
        }

        $post_type = isset($_POST['post_type']) ? sanitize_key($_POST['post_type']) : 'post';
        if (!in_array($post_type, ['post', 'page'], true)) $post_type = 'post';

        $limit  = isset($_POST['limit']) ? max(1, min(2000, intval($_POST['limit']))) : 200;
        $offset = isset($_POST['offset']) ? max(0, intval($_POST['offset'])) : 0;
        $force  = !empty($_POST['force']);

        $q = new WP_Query([
            'post_type'      => $post_type,
            'post_status'    => 'any',
            'posts_per_page' => $limit,
            'offset'         => $offset,
            'orderby'        => 'ID',
            'order'          => 'ASC',
            'fields'         => 'ids',
        ]);

        $changed = 0;
        $skipped = 0;

        foreach ($q->posts as $post_id) {
            $post = get_post($post_id);
            if (!$post) { $skipped++; continue; }

            $current = (string) $post->post_name;
            $new_slug = self::transliterate($post->post_title);

            if ($new_slug === '') { $skipped++; continue; }

            // اگر force نیست و اسلاگ فعلی لاتین و همون است، رد کن
            if (!$force) {
                // اگر اسلاگ فعلی هیچ حرف غیرلاتین ندارد، یعنی احتمالاً قبلاً تنظیم شده
                if (preg_match('/^[a-z0-9-]+$/', $current) && $current !== '') {
                    $skipped++;
                    continue;
                }
            }

            // جلوگیری از تکراری شدن اسلاگ
            $unique = wp_unique_post_slug($new_slug, $post_id, $post->post_status, $post->post_type, $post->post_parent);

            // آپدیت مستقیم post_name
            $res = wp_update_post([
                'ID'        => $post_id,
                'post_name' => $unique,
            ], true);

            if (is_wp_error($res)) {
                $skipped++;
            } else {
                $changed++;
            }
        }

        // برای جلوگیری از 404
        flush_rewrite_rules(false);

        $url = add_query_arg([
            'page'        => 'pst-persian-slugs',
            'pst_done'    => $changed,
            'pst_skipped' => $skipped,
        ], admin_url('tools.php'));

        wp_safe_redirect($url);
        exit;
    }
}

PST_Persian_Slug_Transliterator::init();
