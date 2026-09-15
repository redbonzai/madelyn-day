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
    if (is_singular('book')) {
        wp_enqueue_script('madelyn-day-qr-vendor', get_template_directory_uri() . '/assets/js/vendor/qrcode-generator.js', [], '1.4.4', true);
        wp_enqueue_script('madelyn-day-book-qr', get_template_directory_uri() . '/assets/js/book-qr.js', ['madelyn-day-qr-vendor'], wp_get_theme()->get('Version'), true);
    }
}
add_action('wp_enqueue_scripts', 'madelyn_day_assets');

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
