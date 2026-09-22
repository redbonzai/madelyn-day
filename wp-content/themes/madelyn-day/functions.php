<?php
if (!defined('ABSPATH')) {
    exit;
}

function madelyn_day_setup(): void {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('responsive-embeds');
    add_theme_support('editor-styles');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);
    register_nav_menus(['primary' => __('Primary Navigation', 'madelyn-day')]);
}
add_action('after_setup_theme', 'madelyn_day_setup');

function madelyn_day_assets(): void {
    wp_enqueue_style('madelyn-day', get_stylesheet_uri(), [], wp_get_theme()->get('Version'));
    wp_enqueue_script('madelyn-day-site', get_template_directory_uri() . '/assets/js/site.js', [], wp_get_theme()->get('Version'), true);
    if (is_page('contact')) {
        wp_enqueue_script('madelyn-day-contact', get_template_directory_uri() . '/assets/js/contact.js', [], wp_get_theme()->get('Version'), true);
    }
    if (is_singular('book') || is_front_page() || is_page('about')) {
        wp_enqueue_script('madelyn-day-qr-vendor', get_template_directory_uri() . '/assets/js/vendor/qrcode-generator.js', [], '1.4.4', true);
        wp_enqueue_script('madelyn-day-book-qr', get_template_directory_uri() . '/assets/js/book-qr.js', ['madelyn-day-qr-vendor'], wp_get_theme()->get('Version'), true);
    }
}
add_action('wp_enqueue_scripts', 'madelyn_day_assets');

function madelyn_day_amazon_author_card(): void {
    $author_url = 'https://www.amazon.com/stores/author/B0FF7HWRG7';
    ?>
    <aside class="author-amazon-card" aria-labelledby="amazon-author-heading">
        <div class="author-amazon-copy">
            <p class="eyebrow">Find Madelyn on Amazon</p>
            <h3 id="amazon-author-heading">Browse Madelyn Day’s books on Amazon</h3>
            <p>Scan the QR code with your phone’s camera to visit Madelyn Day’s Amazon author page, explore available books, and view current purchase options.</p>
            <a class="button" href="<?php echo esc_url($author_url); ?>" target="_blank" rel="noopener noreferrer">Visit Amazon author page</a>
        </div>
        <figure class="author-amazon-qr">
            <a href="<?php echo esc_url($author_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="Open Madelyn Day’s Amazon author page">
                <span class="qr-code" data-qr-url="<?php echo esc_url($author_url); ?>" data-qr-title="QR code for Madelyn Day’s Amazon author page" data-qr-description="Scan with a phone camera to open Madelyn Day’s Amazon author page."></span>
            </a>
            <figcaption>Scan to view Madelyn Day’s books on Amazon.com.</figcaption>
        </figure>
    </aside>
    <?php
}

/**
 * Render a managed featured image, with a bundled image as a resilient fallback.
 *
 * The fallback keeps seeded content presentable if a migration copies posts before
 * WordPress has created its Media Library attachments. As soon as an author sets a
 * featured image in the dashboard, WordPress uses that image instead.
 */
function madelyn_day_featured_image(int $post_id, string $size = 'large', string $class = ''): void {
    $fallbacks = [
        'shut-up-and-dig' => 'shut-up-and-dig.png',
        'the-veil-beyond-the-walls' => 'veil-beyond-the-walls.png',
        'the-house-that-whispers' => 'house-that-whispers.png',
        'choose-train-your-new-best-friend' => 'choose-train-best-friend.png',
        'unveiling-the-truth' => 'unveiling-the-truth.png',
        'the-chair-the-car-the-chaos-and-the-siamese-cat' => 'chair-car-chaos-cat.png',
        'where-all-prayers-meet' => 'where-all-prayers-meet.png',
        'welcome-to-madelyn-days-imaginative-world' => 'journal-dreams.webp',
        'stories-that-begin-in-dreams' => 'journal-stories.webp',
        'a-life-across-cultures-careers-and-stories' => 'journal-cultures.webp',
    ];
    $slug = (string) get_post_field('post_name', $post_id);
    $thumbnail_id = (int) get_post_thumbnail_id($post_id);
    $is_seeded_media = $thumbnail_id && (string) get_post_meta($thumbnail_id, '_madelyn_seed_asset', true) !== '';

    if ($thumbnail_id && !$is_seeded_media) {
        echo get_the_post_thumbnail($post_id, $size, [
            'class' => $class,
            'loading' => 'eager',
            'decoding' => 'async',
        ]);
        return;
    }

    if (!isset($fallbacks[$slug])) {
        return;
    }

    printf(
        '<img src="%s" alt="%s" class="%s" loading="eager" decoding="async">',
        esc_url(get_template_directory_uri() . '/assets/images/' . $fallbacks[$slug]),
        esc_attr(get_the_title($post_id)),
        esc_attr($class)
    );
}

function madelyn_day_favicon(): void {
    if (has_site_icon()) {
        return;
    }

    $favicon_url = get_template_directory_uri() . '/assets/images/madelyn-day-logo.png';
    echo '<link rel="icon" href="' . esc_url($favicon_url) . '" type="image/png">' . "\n";
}
add_action('wp_head', 'madelyn_day_favicon');
add_action('admin_head', 'madelyn_day_favicon');
add_action('login_head', 'madelyn_day_favicon');

function madelyn_day_customize_register(WP_Customize_Manager $customizer): void {
    $customizer->add_section('madelyn_day_contact', ['title' => __('Contact & newsletter', 'madelyn-day'), 'priority' => 35]);
    $customizer->add_setting('madelyn_contact_email', ['default' => get_option('admin_email'), 'sanitize_callback' => 'sanitize_email']);
    $customizer->add_control('madelyn_contact_email', ['section' => 'madelyn_day_contact', 'label' => __('Contact form recipient', 'madelyn-day'), 'type' => 'email']);
    $customizer->add_setting('madelyn_newsletter_url', ['default' => '', 'sanitize_callback' => 'esc_url_raw']);
    $customizer->add_control('madelyn_newsletter_url', ['section' => 'madelyn_day_contact', 'label' => __('Newsletter signup URL', 'madelyn-day'), 'description' => __('Paste the hosted form URL from Kit, MailerLite, or another email provider.', 'madelyn-day'), 'type' => 'url']);
}
add_action('customize_register', 'madelyn_day_customize_register');

function madelyn_day_menu_fallback(): void {
    echo '<ul class="menu">';
    echo '<li><a href="' . esc_url(home_url('/')) . '">Home</a></li>';
    echo '<li><a href="' . esc_url(home_url('/#about')) . '">About</a></li>';
    echo '<li><a href="' . esc_url(get_post_type_archive_link('book') ?: home_url('/books/')) . '">Books</a></li>';
    echo '<li><a href="' . esc_url(get_post_type_archive_link('event') ?: home_url('/events/')) . '">Events</a></li>';
    echo '<li><a href="' . esc_url(home_url('/blog/')) . '">Blog</a></li>';
    echo '</ul>';
}
