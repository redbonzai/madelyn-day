<?php get_header(); ?>
<main><article class="article book-detail">
    <p class="eyebrow">A book by Madelyn Day</p>
    <h1><?php the_title(); ?></h1>
    <div class="book-detail-grid">
        <?php if (has_post_thumbnail()) : ?><div class="book-detail-cover"><?php the_post_thumbnail('large'); ?></div><?php endif; ?>
        <div>
            <div class="article-content"><?php the_content(); ?></div>
            <?php $purchase = (string) get_post_meta(get_the_ID(), 'madelyn_purchase_url', true); ?>
            <?php if ($purchase) : ?>
                <aside class="purchase-block" aria-labelledby="amazon-purchase-heading">
                    <div>
                        <p class="eyebrow">Available on Amazon</p>
                        <h2 id="amazon-purchase-heading">View and purchase this book</h2>
                        <p>Scan this QR code with your phone’s camera to view <cite><?php the_title(); ?></cite> on Amazon and see available purchase options.</p>
                        <a class="button" href="<?php echo esc_url($purchase); ?>" target="_blank" rel="noopener noreferrer">View and purchase on Amazon</a>
                    </div>
                    <figure class="purchase-qr">
                        <div class="qr-code" data-qr-url="<?php echo esc_url($purchase); ?>" data-book-title="<?php echo esc_attr(get_the_title()); ?>"></div>
                        <figcaption>Amazon purchase link for <cite><?php the_title(); ?></cite></figcaption>
                    </figure>
                </aside>
            <?php else : ?>
                <p class="meta">Purchase link coming soon.</p>
            <?php endif; ?>
        </div>
    </div>
</article></main>
<?php get_footer(); ?>
