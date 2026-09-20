<?php get_header(); ?>
<main>
    <section class="hero" id="home">
        <div class="shell hero-copy">
            <p class="eyebrow light hero-kicker">Author · storyteller · lifelong learner</p>
            <h1>Stories That Begin<br>in Dreams</h1>
            <p>Discover the imaginative world of Madelyn Day—paranormal encounters, mysteries, practical guides, faith, and unforgettable characters.</p>
            <div class="actions"><a class="button button-light" href="#books">Explore the books</a><a class="button button-outline" href="#about">Meet Madelyn</a></div>
        </div>
        <div class="hero-foot shell" aria-label="Author highlights"><span>Seven distinctive titles</span><span>Stories inspired by life and vivid dreams</span><span>Based in Austin, Texas</span></div>
    </section>

    <section class="section about-section" id="about"><div class="shell about-grid">
        <div class="about-visual reveal reveal-left"><div class="about-photo" role="img" aria-label="Madelyn writing at her desk"></div><div class="experience-card"><strong>15+</strong><span>Years of stories,<br>service & discovery</span></div></div>
        <div class="about-copy reveal reveal-right"><p class="eyebrow">About the author</p><h2>A Life Across Cultures,<br>Careers, and Stories</h2>
            <p>Madelyn Day’s writing is often sparked by vivid dreams or long walks with her beloved dogs. Born in Corpus Christi and raised across Laredo, Caracas, and Mexico City, she built a life shaped by language, history, teaching, travel, and service.</p>
            <p>Her wide-ranging career has included university teaching, interpretation, aviation, community service, medical interpretation, caregiving, animal rescue, and astronomy writing. Now in Austin, she continues to turn a lifetime of curiosity and compassion into stories—one dream, one dog walk, and one book at a time.</p>
            <a class="text-link" href="<?php echo esc_url(home_url('/about/')); ?>">Read the full story <span>→</span></a>
            <div class="credential-grid"><div><b>✦ A world of experience</b><span>Teaching, travel, languages, public service, family, and a lifelong love of learning.</span></div><div><b>♥ Stories with heart</b><span>From paranormal suspense and crime to animal care, comedy, and faith.</span></div></div>
            <?php madelyn_day_amazon_author_card(); ?>
        </div>
    </div></section>

    <section class="section sand" id="books"><div class="shell">
        <div class="section-head reveal"><div><p class="eyebrow">The collection</p><h2>Explore Stories Crafted<br>With Lasting Purpose</h2></div><a class="button" href="<?php echo esc_url(get_post_type_archive_link('book') ?: home_url('/books/')); ?>">Explore all books</a></div>
        <?php $books = new WP_Query(['post_type' => 'book', 'posts_per_page' => 7, 'orderby' => 'menu_order', 'order' => 'ASC']); ?>
        <?php if ($books->have_posts()) : ?><div class="book-grid"><?php $book_index = 0; while ($books->have_posts()) : $books->the_post(); $purchase_url = (string) get_post_meta(get_the_ID(), 'madelyn_purchase_url', true); $genres = get_the_terms(get_the_ID(), 'book_genre'); ?>
            <article class="book-card reveal" style="--delay:<?php echo esc_attr((string) (($book_index % 3) * 120)); ?>ms">
                <a class="book-cover" href="<?php the_permalink(); ?>"><?php madelyn_day_featured_image(get_the_ID()); ?></a>
                <div class="book-meta"><span><?php echo esc_html($genres && !is_wp_error($genres) ? $genres[0]->name : 'Book'); ?></span><span>By Madelyn Day</span></div>
                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3><p><?php echo esc_html(get_the_excerpt()); ?></p>
                <?php if ($purchase_url) : ?><a class="text-link" href="<?php echo esc_url($purchase_url); ?>" target="_blank" rel="noopener">View on Amazon <span>→</span></a><?php else : ?><span class="text-link muted-link">Purchase link coming soon</span><?php endif; ?>
            </article>
        <?php $book_index++; endwhile; ?></div><?php else : ?><div class="empty">Books added in the dashboard will appear here automatically.</div><?php endif; wp_reset_postdata(); ?>
    </div></section>

    <section class="section events-section" id="events"><div class="shell">
        <div class="center-head reveal"><p class="eyebrow">Book events</p><h2>Upcoming Moments<br>Worth Joining Together</h2><p>Readings, conversations, launches, and community gatherings will be shared here.</p></div>
        <?php $events = new WP_Query(['post_type' => 'event', 'posts_per_page' => 3, 'meta_key' => 'madelyn_event_start', 'orderby' => 'meta_value', 'order' => 'ASC', 'meta_query' => [['key' => 'madelyn_event_start', 'value' => current_time('Y-m-d\TH:i'), 'compare' => '>=']]]); ?>
        <?php if ($events->have_posts()) : ?><div class="event-list"><?php $event_index = 0; while ($events->have_posts()) : $events->the_post(); $start = (string) get_post_meta(get_the_ID(), 'madelyn_event_start', true); $venue = (string) get_post_meta(get_the_ID(), 'madelyn_event_venue', true); $address = (string) get_post_meta(get_the_ID(), 'madelyn_event_address', true); $ticket = (string) get_post_meta(get_the_ID(), 'madelyn_event_ticket_url', true); ?>
            <article class="event-row reveal" style="--delay:<?php echo esc_attr((string) ($event_index * 140)); ?>ms"><div class="event-facts"><span>⌖ <?php echo esc_html($venue ?: 'Venue to be announced'); ?></span><span>◷ <?php echo esc_html($start ? wp_date('F j, Y · g:i A', strtotime($start)) : 'Date to be announced'); ?></span></div><div><h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3><p><?php echo esc_html($address ?: get_the_excerpt()); ?></p></div><a class="button" href="<?php echo esc_url($ticket ?: get_permalink()); ?>">Event details</a></article>
        <?php $event_index++; endwhile; ?></div><?php else : ?><div class="event-empty reveal"><p class="eyebrow">The next chapter</p><h3>New appearances will be announced soon.</h3><p>Join the mailing list or contact Madelyn about readings, interviews, and speaking invitations.</p><div class="actions centered"><a class="button" href="<?php echo esc_url(home_url('/contact/')); ?>">Invite Madelyn</a></div></div><?php endif; wp_reset_postdata(); ?>
    </div></section>

    <section class="section testimonials" id="reviews"><div class="shell">
        <div class="section-head reveal"><div><p class="eyebrow">Reader feedback</p><h2>Stories That Leave Lasting<br>Impressions Forever</h2></div><p>Highlights from verified reader feedback shared on Madelyn’s author page.</p></div>
        <?php $reviews = new WP_Query(['post_type' => 'review', 'posts_per_page' => 3, 'orderby' => 'menu_order', 'order' => 'ASC']); ?>
        <?php if ($reviews->have_posts()) : ?><div class="review-grid"><?php $review_index = 0; while ($reviews->have_posts()) : $reviews->the_post(); $rating = max(1, min(5, (int) get_post_meta(get_the_ID(), 'madelyn_rating', true))); $reviewer = (string) get_post_meta(get_the_ID(), 'madelyn_reviewer', true); $review_url = (string) get_post_meta(get_the_ID(), 'madelyn_review_url', true); ?>
            <blockquote class="review-card reveal" style="--delay:<?php echo esc_attr((string) ($review_index * 160)); ?>ms"><div class="stars" aria-label="<?php echo esc_attr((string) $rating); ?> out of 5 stars"><?php echo esc_html(str_repeat('★', $rating)); ?></div><h3><?php the_title(); ?></h3><div class="review-copy"><?php the_content(); ?></div><footer><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/person-' . (($review_index % 3) + 1) . '.webp'); ?>" alt=""><div><b><?php echo esc_html($reviewer); ?></b><?php if ($review_url) : ?><a href="<?php echo esc_url($review_url); ?>" target="_blank" rel="noopener">Read review →</a><?php endif; ?></div></footer></blockquote>
        <?php $review_index++; endwhile; ?></div><?php endif; wp_reset_postdata(); ?>
    </div></section>

    <?php $newsletter_url = get_theme_mod('madelyn_newsletter_url', ''); ?>
    <section class="newsletter"><div class="shell newsletter-content reveal"><p class="eyebrow light">Stay connected</p><h2>Step Into Madelyn’s<br>Imaginative World</h2><p>Get new-release updates, behind-the-scenes notes, event news, and special announcements.</p><div class="actions centered"><?php if ($newsletter_url) : ?><a class="button button-light" href="<?php echo esc_url($newsletter_url); ?>" target="_blank" rel="noopener">Join the mailing list</a><?php else : ?><a class="button button-light" href="<?php echo esc_url(home_url('/contact/')); ?>">Contact Madelyn</a><?php endif; ?><a class="button button-outline" href="#books">Browse books</a></div></div></section>

    <section class="section faq-section"><div class="shell faq-grid"><div class="reveal reveal-left"><p class="eyebrow">Frequently asked</p><h2>Answers to Your Reading<br>Questions, Clearly</h2><p>Looking for a book, an event, or a conversation with Madelyn? Start here.</p></div><div class="faq-list reveal reveal-right"><details><summary>How can readers contact Madelyn?</summary><p>Use the contact page to prepare an email for reader notes, media requests, or general inquiries.</p></details><details><summary>Can I invite Madelyn to an event?</summary><p>Yes. Select “Event or speaking invitation” on the contact form and include the date, location, and audience.</p></details><details><summary>Where can I buy Madelyn’s books?</summary><p>Available titles link directly to Amazon from each book card and book detail page.</p></details><details><summary>How do I hear about new releases?</summary><p>Join the mailing list once the newsletter signup is connected, or check the journal for updates.</p></details></div></div></section>

    <section class="section sand journal-section"><div class="shell"><div class="center-head reveal"><p class="eyebrow">The journal</p><h2>Story Insights and Writing<br>Inspiration</h2></div>
        <?php $posts = new WP_Query(['post_type' => 'post', 'posts_per_page' => 3, 'post_status' => 'publish']); ?>
        <?php if ($posts->have_posts()) : ?><div class="journal-grid"><?php $post_index = 0; while ($posts->have_posts()) : $posts->the_post(); ?><article class="journal-card reveal" style="--delay:<?php echo esc_attr((string) ($post_index * 160)); ?>ms"><a class="journal-image" href="<?php the_permalink(); ?>"><?php madelyn_day_featured_image(get_the_ID()); ?></a><div class="journal-body"><div class="meta"><?php echo esc_html(get_the_date()); ?> · <?php echo esc_html(get_the_author()); ?></div><h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3><p><?php echo esc_html(get_the_excerpt()); ?></p><a class="text-link" href="<?php the_permalink(); ?>">Read the story <span>→</span></a></div></article><?php $post_index++; endwhile; ?></div><?php endif; wp_reset_postdata(); ?>
    </div></section>
</main>
<?php get_footer(); ?>
