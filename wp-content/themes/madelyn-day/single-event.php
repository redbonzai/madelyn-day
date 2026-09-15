<?php get_header(); ?>
<?php while (have_posts()) : the_post();
    $start = (string) get_post_meta(get_the_ID(), 'madelyn_event_start', true);
    $end = (string) get_post_meta(get_the_ID(), 'madelyn_event_end', true);
    $venue = (string) get_post_meta(get_the_ID(), 'madelyn_event_venue', true);
    $address = (string) get_post_meta(get_the_ID(), 'madelyn_event_address', true);
    $ticket = (string) get_post_meta(get_the_ID(), 'madelyn_event_ticket_url', true);
    $status = (string) get_post_meta(get_the_ID(), 'madelyn_event_status', true);
?>
<main><article class="article event-detail">
    <p class="eyebrow">Book event</p>
    <h1><?php the_title(); ?></h1>
    <div class="event-detail-grid">
        <div class="article-content"><?php the_content(); ?></div>
        <aside class="event-detail-card" aria-label="Event information">
            <dl>
                <div><dt>Date and time</dt><dd><time datetime="<?php echo esc_attr($start); ?>"><?php echo esc_html($start ? wp_date('F j, Y · g:i A', strtotime($start)) : 'To be announced'); ?></time><?php if ($end) : ?><br><span>Until <?php echo esc_html(wp_date('g:i A', strtotime($end))); ?></span><?php endif; ?></dd></div>
                <div><dt>Venue</dt><dd><?php echo esc_html($venue ?: 'To be announced'); ?></dd></div>
                <div><dt>Location</dt><dd><?php echo esc_html($address ?: 'To be announced'); ?></dd></div>
                <div><dt>Status</dt><dd><?php echo esc_html(ucwords(str_replace('-', ' ', $status ?: 'scheduled'))); ?></dd></div>
            </dl>
            <?php if ($ticket) : ?><a class="button" href="<?php echo esc_url($ticket); ?>" target="_blank" rel="noopener noreferrer">Register for this event</a><?php else : ?><a class="button" href="<?php echo esc_url(home_url('/contact/')); ?>">Ask about this event</a><?php endif; ?>
        </aside>
    </div>
    <a class="text-link event-back-link" href="<?php echo esc_url(get_post_type_archive_link('event') ?: home_url('/events/')); ?>"><span>←</span> View all events</a>
</article></main>
<?php endwhile; ?>
<?php get_footer(); ?>
