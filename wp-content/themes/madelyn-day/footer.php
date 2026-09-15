<footer class="site-footer">
    <div class="shell footer-grid">
        <div><a class="footer-brand" href="<?php echo esc_url(home_url('/')); ?>"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/madelyn-day-logo.png'); ?>" alt="Madelyn Day"></a><p>Stories, guides, mysteries, and memorable journeys by author Madelyn Day.</p></div>
        <div><h3>Quick links</h3><a href="<?php echo esc_url(home_url('/#about')); ?>">About</a><a href="<?php echo esc_url(get_post_type_archive_link('book') ?: home_url('/books/')); ?>">Books</a><a href="<?php echo esc_url(get_post_type_archive_link('event') ?: home_url('/events/')); ?>">Events</a><a href="<?php echo esc_url(home_url('/contact/')); ?>">Contact</a></div>
        <div><h3>Resources</h3><a href="<?php echo esc_url(home_url('/blog/')); ?>">Journal</a><a href="https://www.amazon.com/stores/author/B0FF7HWRG7" target="_blank" rel="noopener">Amazon author page</a><?php if (is_user_logged_in()) : ?><a href="<?php echo esc_url(admin_url()); ?>">Author dashboard</a><?php endif; ?></div>
        <div><h3>Newsletter</h3><p>Subscribe for new releases, event news, and writing insights.</p><?php $newsletter_url = get_theme_mod('madelyn_newsletter_url', ''); ?><a class="button button-light" href="<?php echo esc_url($newsletter_url ?: home_url('/contact/')); ?>">Stay connected</a></div>
    </div>
    <div class="shell copyright"><span>© <?php echo esc_html(wp_date('Y')); ?> Madelyn Day. All rights reserved.</span><span><a href="<?php echo esc_url(home_url('/contact/')); ?>">Contact</a><?php if (is_user_logged_in()) : ?> · <a href="<?php echo esc_url(admin_url()); ?>">Administration</a><?php endif; ?></span></div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
