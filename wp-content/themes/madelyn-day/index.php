<?php get_header(); ?>
<main class="archive-page"><div class="shell"><header class="archive-hero"><p class="eyebrow">Madelyn Day</p><h1><?php echo esc_html(get_the_archive_title() ?: 'The Journal'); ?></h1><?php the_archive_description('<p>', '</p>'); ?></header>
<?php if (have_posts()) : ?><div class="journal-grid"><?php while (have_posts()) : the_post(); ?><article class="journal-card"><a class="journal-image" href="<?php the_permalink(); ?>"><?php if (has_post_thumbnail()) { the_post_thumbnail('large'); } ?></a><div class="journal-body"><div class="meta"><?php echo esc_html(get_the_date()); ?></div><h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3><p><?php echo esc_html(get_the_excerpt()); ?></p><a class="text-link" href="<?php the_permalink(); ?>">Read more <span>→</span></a></div></article><?php endwhile; ?></div><?php the_posts_pagination(); ?><?php else : ?><div class="empty">No published content yet.</div><?php endif; ?>
</div></main>
<?php get_footer(); ?>
