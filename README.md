# Persian Slug Transliterator

[![WordPress Plugin Version](https://img.shields.io/wordpress/plugin/v/persian-slug-transliterator)](https://wordpress.org/plugins/persian-slug-transliterator/)
[![WordPress Plugin Required PHP Version](https://img.shields.io/wordpress/plugin/required-php/persian-slug-transliterator)](https://wordpress.org/plugins/persian-slug-transliterator/)
[![License: GPL v2+](https://img.shields.io/badge/License-GPLv2%2B-blue.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

A WordPress plugin that automatically transliterates Persian (Farsi) and Arabic post slugs to clean Latin characters.

افزونه وردپرس برای تبدیل خودکار نامک (اسلاگ) فارسی و عربی به حروف لاتین.

---

## 🇬🇧 English

### Description

**Persian Slug Transliterator** converts Persian and Arabic characters in WordPress post slugs (permalinks) to their Latin equivalents. This ensures clean, SEO-friendly, and universally compatible URLs.

WordPress by default encodes Persian/Arabic slugs as percent-encoded UTF-8, resulting in long, unreadable URLs like `%D8%B3%D9%84%D8%A7%D9%85`. This plugin converts them to readable Latin text like `slam-dnya`.

### Features

- **Automatic Transliteration** — New posts and pages automatically get clean Latin slugs
- **Bulk Update Tool** — Convert existing Persian/Arabic slugs in batch from the admin panel
- **Custom Post Type Support** — Works with posts, pages, and any public custom post type
- **Arabic Normalization** — Handles Arabic character variants (ي → ی, ك → ک, etc.)
- **Numeral Conversion** — Converts Persian/Arabic numerals (۰-۹, ٠-٩) to Western digits (0-9)
- **ZWNJ Handling** — Properly handles Zero-Width Non-Joiner characters common in Persian text
- **Duplicate Slug Prevention** — Ensures unique slugs after transliteration
- **Translation Ready** — Fully internationalized with text domain
- **Lightweight** — No external dependencies, no database tables, no JavaScript overhead

### Installation

1. Download the latest release or clone this repository:
   ```bash
   git clone https://github.com/6arshid/Persian-Slug-Transliterator.git
   ```
2. Copy the folder to `/wp-content/plugins/`
3. Activate the plugin through the **Plugins** menu in WordPress
4. New posts will automatically get Latin slugs

### Usage

#### Automatic Mode

Once activated, any new post or page you create will automatically have its Persian/Arabic title transliterated into a Latin slug. No configuration needed.

#### Bulk Update

To update existing posts with Persian/Arabic slugs:

1. Navigate to **Tools → Persian Slugs** in the WordPress admin
2. Select the content type (Posts, Pages, or a custom post type)
3. Set the batch size and offset if needed
4. Click **Start Bulk Update**

### Example

| Title | Generated Slug |
|---|---|
| سلام دنیا | `slam-dnya` |
| آموزش وردپرس | `amvzsh-vrdprs` |
| ۱۰ نکته مهم | `10-nkth-mhm` |

---

## 🇮🇷 فارسی

### توضیحات

**Persian Slug Transliterator** نامک‌های (اسلاگ‌های) فارسی و عربی نوشته‌ها و برگه‌های وردپرس را به‌صورت خودکار به حروف لاتین تبدیل می‌کند. این کار باعث می‌شود لینک‌های سایت شما خوانا، سازگار با سئو و قابل اشتراک‌گذاری باشند.

وردپرس به‌صورت پیش‌فرض اسلاگ‌های فارسی/عربی را به‌صورت درصد-کدگذاری شده (percent-encoded) ذخیره می‌کند که نتیجه آن لینک‌هایی طولانی و ناخوانا مانند `%D8%B3%D9%84%D8%A7%D9%85` است. این افزونه آن‌ها را به متن لاتین خوانا مانند `slam-dnya` تبدیل می‌کند.

### ویژگی‌ها

- **تبدیل خودکار** — نوشته‌ها و برگه‌های جدید به‌صورت خودکار اسلاگ لاتین دریافت می‌کنند
- **ابزار بروزرسانی گروهی** — اسلاگ‌های قدیمی فارسی/عربی را به‌صورت دسته‌ای تبدیل کنید
- **پشتیبانی از انواع محتوا** — با نوشته‌ها، برگه‌ها و تمام Custom Post Type‌های عمومی کار می‌کند
- **نرمال‌سازی عربی** — حروف عربی مشابه را به معادل فارسی تبدیل می‌کند
- **تبدیل اعداد** — اعداد فارسی/عربی (۰-۹) را به اعداد لاتین (0-9) تبدیل می‌کند
- **مدیریت نیم‌فاصله** — کاراکتر ZWNJ رایج در متون فارسی را به‌درستی مدیریت می‌کند
- **جلوگیری از تکراری شدن** — اسلاگ‌های یکتا را تضمین می‌کند
- **سبک و بدون وابستگی** — بدون کتابخانه خارجی، بدون جدول دیتابیس

### نصب

1. آخرین نسخه را دانلود کنید یا مخزن را کلون کنید:
   ```bash
   git clone https://github.com/6arshid/Persian-Slug-Transliterator.git
   ```
2. پوشه را در مسیر `/wp-content/plugins/` کپی کنید
3. افزونه را از منوی **افزونه‌ها** در وردپرس فعال کنید

### استفاده

#### حالت خودکار

بعد از فعال‌سازی، هر نوشته یا برگه جدیدی که ایجاد کنید، به‌صورت خودکار اسلاگ لاتین خواهد داشت.

#### بروزرسانی گروهی

برای بروزرسانی نوشته‌های قدیمی:

1. به **ابزارها → Persian Slugs** در پیشخوان وردپرس بروید
2. نوع محتوا را انتخاب کنید
3. تعداد و آفست را تنظیم کنید
4. روی **شروع بروزرسانی** کلیک کنید

> **نکته مهم:** بعد از تغییر اسلاگ‌ها، اگر لینک‌های قدیمی در گوگل یا شبکه‌های اجتماعی وجود دارد، حتماً ریدایرکت 301 تنظیم کنید.

---

## Plugin Structure

```
persian-slug-transliterator/
├── persian-slug-transliterator.php    # Main plugin file
├── index.php                          # Security: silence is golden
├── uninstall.php                      # Clean uninstall handler
├── readme.txt                         # WordPress.org readme
├── README.md                          # GitHub readme
├── includes/
│   ├── class-pst-transliterator.php   # Transliteration logic
│   ├── class-pst-admin.php            # Admin UI & bulk update
│   └── index.php                      # Security
└── languages/
    ├── persian-slug-transliterator.pot # Translation template
    └── index.php                      # Security
```

## Requirements

- WordPress 5.0 or higher
- PHP 7.2 or higher

## Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/your-feature`)
3. Commit your changes (`git commit -m 'Add your feature'`)
4. Push to the branch (`git push origin feature/your-feature`)
5. Open a Pull Request

Please ensure your code follows the [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/).

## Contributors

<table>
  <tr>
    <td align="center">
      <a href="https://github.com/6arshid/">
        <img src="https://github.com/6arshid.png" width="100px;" alt="6arshid" /><br />
        <sub><b>6arshid</b></sub>
      </a>
    </td>
    <td align="center">
      <a href="https://github.com/hassantafreshi/">
        <img src="https://github.com/hassantafreshi.png" width="100px;" alt="Hassan Tafreshi" /><br />
        <sub><b>Hassan Tafreshi</b></sub>
      </a>
    </td>
  </tr>
</table>

## License

This project is licensed under the **GPL-2.0-or-later** — see the [LICENSE](https://www.gnu.org/licenses/gpl-2.0.html) file for details.

## Support

- **Issues:** [GitHub Issues](https://github.com/6arshid/Persian-Slug-Transliterator/issues)
- **WordPress.org:** [Plugin Page](https://wordpress.org/plugins/persian-slug-transliterator/)

---

⭐ If this plugin is useful to you, please consider giving it a star on GitHub!
