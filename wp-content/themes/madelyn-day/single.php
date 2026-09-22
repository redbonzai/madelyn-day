<?php get_header(); ?>
<main><article class="article"><p class="eyebrow"><?php echo esc_html(get_post_type_object(get_post_type())->labels->singular_name ?? 'Story'); ?></p><h1><?php the_title(); ?></h1><div class="meta"><?php echo esc_html(get_the_date()); ?></div><?php madelyn_day_featured_image(get_the_ID()); ?><div class="article-content"><?php the_content(); ?></div></article></main>
<?php get_footer(); ?>
