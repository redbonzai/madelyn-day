<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="site-header" id="site-header">
    <nav class="nav shell" aria-label="Primary navigation">
        <a class="brand" href="<?php echo esc_url(home_url('/')); ?>" aria-label="Madelyn Day home"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/madelyn-day-logo.png'); ?>" alt="Madelyn Day"></a>
        <?php wp_nav_menu(['theme_location' => 'primary', 'container' => false, 'menu_class' => 'menu', 'fallback_cb' => 'madelyn_day_menu_fallback']); ?>
        <div class="nav-actions"><?php if (is_user_logged_in()) : ?><a class="admin-link" href="<?php echo esc_url(admin_url()); ?>">Dashboard</a><?php endif; ?><a class="nav-contact" href="<?php echo esc_url(home_url('/contact/')); ?>">Contact</a><button class="menu-toggle" type="button" aria-expanded="false" aria-controls="mobile-menu"><span></span><span></span><span></span><span class="screen-reader-text">Open menu</span></button></div>
        <div class="mobile-menu" id="mobile-menu"><a href="<?php echo esc_url(home_url('/')); ?>">Home</a><a href="<?php echo esc_url(home_url('/#about')); ?>">About</a><a href="<?php echo esc_url(home_url('/#books')); ?>">Books</a><a href="<?php echo esc_url(home_url('/#events')); ?>">Events</a><a href="<?php echo esc_url(home_url('/blog/')); ?>">Blog</a><a href="<?php echo esc_url(home_url('/contact/')); ?>">Contact</a></div>
    </nav>
</header>
