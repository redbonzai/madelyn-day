<?php
/**
 * Plugin Name: Madelyn Day Content
 * Description: Books, events, and reader reviews for the Madelyn Day author website.
 * Version: 0.3.2
 * Author: Madelyn Day
 */

if (!defined('ABSPATH')) {
    exit;
}

final class Madelyn_Day_Content {
    private const NONCE_ACTION = 'madelyn_save_content';
    private const NONCE_NAME = 'madelyn_content_nonce';

    public static function boot(): void {
        add_action('init', [self::class, 'register_content_types']);
        add_action('init', [self::class, 'register_meta']);
        add_action('init', [self::class, 'maybe_create_core_pages'], 30);
        add_action('init', [self::class, 'seed_initial_content'], 40);
        add_action('init', [self::class, 'repair_missing_featured_media'], 45);
        add_action('init', [self::class, 'sync_known_purchase_urls'], 50);
        add_action('add_meta_boxes', [self::class, 'add_meta_boxes']);
        add_action('save_post', [self::class, 'save_meta']);
        add_filter('manage_book_posts_columns', [self::class, 'book_columns']);
        add_action('manage_book_posts_custom_column', [self::class, 'book_column_values'], 10, 2);
    }

    public static function activate(): void {
        self::register_content_types();
        flush_rewrite_rules();
    }

    public static function deactivate(): void {
        flush_rewrite_rules();
    }

    public static function maybe_create_core_pages(): void {
        if ((int) get_option('madelyn_core_pages_version', 0) >= 3) {
            return;
        }

        $home = get_page_by_path('home');
        $home_id = $home ? $home->ID : wp_insert_post([
            'post_type' => 'page',
            'post_status' => 'publish',
            'post_title' => 'Home',
            'post_name' => 'home',
        ]);

        $blog = get_page_by_path('blog');
        $blog_id = $blog ? $blog->ID : wp_insert_post([
            'post_type' => 'page',
            'post_status' => 'publish',
            'post_title' => 'Blog',
            'post_name' => 'blog',
        ]);

        $contact = get_page_by_path('contact');
        $contact_id = $contact ? $contact->ID : wp_insert_post([
            'post_type' => 'page',
            'post_status' => 'publish',
            'post_title' => 'Contact',
            'post_name' => 'contact',
        ]);

        $about = get_page_by_path('about');
        $about_id = $about ? $about->ID : wp_insert_post([
            'post_type' => 'page',
            'post_status' => 'publish',
            'post_title' => 'About Madelyn',
            'post_name' => 'about',
            'post_content' => '<p>Madelyn Day’s writing is often sparked by vivid dreams or long walks with her beloved dogs. Born in Corpus Christi and raised across Laredo, Caracas, and Mexico City, she built a life shaped by language, history, teaching, travel, and service.</p><p>Her wide-ranging career has included university teaching, interpretation, aviation, community service, medical interpretation, caregiving, animal rescue, and astronomy writing. Now in Austin, she continues to turn a lifetime of curiosity and compassion into stories—one dream, one dog walk, and one book at a time.</p>',
        ]);

        if (!is_wp_error($home_id) && !is_wp_error($blog_id) && !is_wp_error($contact_id) && !is_wp_error($about_id)) {
            update_option('show_on_front', 'page');
            update_option('page_on_front', (int) $home_id);
            update_option('page_for_posts', (int) $blog_id);
            update_option('madelyn_core_pages_created', 1);
            update_option('madelyn_core_pages_version', 3);
            flush_rewrite_rules(false);
        }
    }

    public static function seed_initial_content(): void {
        if ((int) get_option('madelyn_seed_content_version', 0) >= 2) {
            return;
        }

        $asset_dir = get_theme_file_path('assets/images');
        $books = [
            ['slug' => 'shut-up-and-dig', 'title' => 'Shut Up and Dig!', 'genre' => 'Paranormal suspense', 'image' => 'shut-up-and-dig.png', 'url' => 'https://www.amazon.com/SHUT-DIG-Nightmarish-Accompanies-Cemetery-ebook/dp/B0H2WV6S2Y/', 'excerpt' => 'A chilling cemetery mystery where every secret uncovered brings the nightmare closer.'],
            ['slug' => 'the-veil-beyond-the-walls', 'title' => 'The Veil Beyond the Walls', 'genre' => 'Paranormal fiction', 'image' => 'veil-beyond-the-walls.png', 'url' => 'https://www.amazon.com/VEIL-BEYOND-WALLS-ultimate-doorway-ebook/dp/B0GXLKS755/', 'excerpt' => 'The fictional sequel to The House That Whispers opens an ultimate doorway into the unknown.'],
            ['slug' => 'the-house-that-whispers', 'title' => 'The House That Whispers', 'genre' => 'True paranormal experience', 'image' => 'house-that-whispers.png', 'url' => 'https://www.amazon.com/HOUSE-THAT-WHISPERS-PARANORMAL-EXPERIENCE-ebook/dp/B0GTN2S4HS/', 'excerpt' => 'A true paranormal experience shaped by memory, mystery, and the things a house refuses to forget.'],
            ['slug' => 'choose-train-your-new-best-friend', 'title' => 'Choose & Train Your New Best Friend', 'genre' => 'Pets & animal care', 'image' => 'choose-train-best-friend.png', 'url' => 'https://www.amazon.com/CHOOSE-TRAIN-YOUR-BEST-FRIEND-ebook/dp/B0FRGH8BFF/', 'excerpt' => 'Friendly guidance on breed choice, training, feeding, rescue, adoption, and preparing your home.'],
            ['slug' => 'unveiling-the-truth', 'title' => 'Unveiling the Truth', 'genre' => 'Criminal investigation', 'image' => 'unveiling-the-truth.png', 'url' => 'https://www.amazon.com/UNVEILING-TRUTH-STRATEGY-CRIMINAL-INVESTIGATIONS-ebook/dp/B0FCSLPQLS/', 'excerpt' => 'Forensic science, behavioral clues, and practical strategy for understanding criminal investigations.'],
            ['slug' => 'the-chair-the-car-the-chaos-and-the-siamese-cat', 'title' => 'The Chair, the Car, the Chaos… and the Siamese Cat', 'genre' => 'Comedic mystery', 'image' => 'chair-car-chaos-cat.png', 'url' => 'https://www.amazon.com/CHAIR-CAR-CHAOS-Siamese-Cat/dp/B0FD7FDV51/', 'excerpt' => 'An entertaining mystery packed with bizarre events, coincidences, and unpredictable twists.'],
            ['slug' => 'where-all-prayers-meet', 'title' => 'Where All Prayers Meet', 'genre' => 'Faith & culture', 'image' => 'where-all-prayers-meet.png', 'url' => '', 'excerpt' => 'An exploration of the Lord’s Prayer across cultures, faiths, and traditions.'],
        ];

        foreach ($books as $order => $book) {
            $post = get_page_by_path($book['slug'], OBJECT, 'book');
            $post_id = $post ? $post->ID : wp_insert_post([
                'post_type' => 'book', 'post_status' => 'publish', 'post_name' => $book['slug'],
                'post_title' => $book['title'], 'post_excerpt' => $book['excerpt'],
                'post_content' => $book['excerpt'], 'menu_order' => $order,
            ]);
            if (is_wp_error($post_id)) {
                continue;
            }
            update_post_meta($post_id, 'madelyn_purchase_url', $book['url']);
            update_post_meta($post_id, 'madelyn_availability', $book['url'] ? 'available' : 'coming-soon');
            update_post_meta($post_id, 'madelyn_featured', 1);
            wp_set_object_terms($post_id, $book['genre'], 'book_genre');
            if (!has_post_thumbnail($post_id)) {
                $attachment_id = self::seed_attachment($asset_dir . '/' . $book['image'], $book['title'] . ' book cover');
                if ($attachment_id) {
                    set_post_thumbnail($post_id, $attachment_id);
                }
            }
        }

        $reviews = [
            ['slug' => 'practical-and-easy-to-follow', 'title' => 'Practical and Easy to Follow', 'reviewer' => 'Bel Young', 'text' => 'Warm, clear, and useful guidance for choosing, training, and caring for a dog.', 'url' => 'https://www.amazon.com/gp/customer-reviews/R3J362LWNYH6P7/'],
            ['slug' => 'interestingly-informative', 'title' => 'Interestingly Informative', 'reviewer' => 'Migyver', 'text' => 'An engaging blend of criminal psychology, forensic methods, real cases, and investigative insight.', 'url' => 'https://www.amazon.com/gp/customer-reviews/R1V9RH9DD1OHCX/'],
            ['slug' => 'educational-excellence-meets-true-crime', 'title' => 'Educational Excellence Meets True Crime', 'reviewer' => 'rgb2', 'text' => 'Technical precision made approachable through compelling cases for curious true-crime readers.', 'url' => 'https://www.amazon.com/gp/customer-reviews/R1KI38B6H431YL/'],
        ];
        foreach ($reviews as $order => $review) {
            $post = get_page_by_path($review['slug'], OBJECT, 'review');
            $post_id = $post ? $post->ID : wp_insert_post(['post_type' => 'review', 'post_status' => 'publish', 'post_name' => $review['slug'], 'post_title' => $review['title'], 'post_content' => $review['text'], 'menu_order' => $order]);
            if (!is_wp_error($post_id)) {
                update_post_meta($post_id, 'madelyn_reviewer', $review['reviewer']);
                update_post_meta($post_id, 'madelyn_rating', 5);
                update_post_meta($post_id, 'madelyn_review_source', 'Amazon reader review');
                update_post_meta($post_id, 'madelyn_review_url', $review['url']);
                update_post_meta($post_id, 'madelyn_featured', 1);
            }
        }

        $event_time = current_time('timestamp');
        $events = [
            [
                'slug' => 'stories-that-begin-in-dreams-virtual-conversation',
                'title' => 'Stories That Begin in Dreams: A Virtual Conversation',
                'excerpt' => 'Join Madelyn for a welcoming online conversation about the dreams, memories, and experiences that inspire her books.',
                'content' => '<p>Join Madelyn Day for a virtual author conversation about the dreams, memories, and real-life experiences that become the starting points for her stories.</p><p>The conversation will include a short reading, a behind-the-scenes look at her writing process, and time for reader questions. Connection details will be shared with registered guests before the event.</p>',
                'offset' => '+30 days 7:00 PM',
                'duration' => 90,
                'venue' => 'Online event',
                'address' => 'Virtual event — access details to be announced',
                'status' => 'virtual',
            ],
            [
                'slug' => 'an-evening-of-paranormal-stories',
                'title' => 'An Evening of Paranormal Stories',
                'excerpt' => 'An intimate reading and conversation featuring Madelyn’s paranormal fiction and true unexplained experiences.',
                'content' => '<p>Spend an evening with Madelyn Day as she reads from her paranormal books and discusses the line between lived experience and imagination.</p><p>This event is planned for the Austin area. Final venue and attendance details will be announced as soon as they are confirmed.</p>',
                'offset' => '+67 days 6:30 PM',
                'duration' => 90,
                'venue' => 'Austin-area venue to be announced',
                'address' => 'Austin, Texas',
                'status' => 'scheduled',
            ],
            [
                'slug' => 'books-dogs-and-storytelling',
                'title' => 'Books, Dogs, and Storytelling',
                'excerpt' => 'A community conversation about animal companionship, practical care, and the long walks that spark new stories.',
                'content' => '<p>Madelyn shares how dogs have shaped her life, inspired her writing, and informed <em>Choose &amp; Train Your New Best Friend</em>.</p><p>The program will include a short author talk and audience conversation. Venue and registration information will be posted when confirmed.</p>',
                'offset' => '+104 days 2:00 PM',
                'duration' => 75,
                'venue' => 'Community venue to be announced',
                'address' => 'Austin, Texas',
                'status' => 'scheduled',
            ],
        ];
        foreach ($events as $event) {
            $post = get_page_by_path($event['slug'], OBJECT, 'event');
            $post_id = $post ? $post->ID : wp_insert_post([
                'post_type' => 'event',
                'post_status' => 'publish',
                'post_name' => $event['slug'],
                'post_title' => $event['title'],
                'post_excerpt' => $event['excerpt'],
                'post_content' => $event['content'],
            ]);
            if (is_wp_error($post_id)) {
                continue;
            }
            $start = strtotime($event['offset'], $event_time);
            update_post_meta($post_id, 'madelyn_event_start', wp_date('Y-m-d\TH:i', $start));
            update_post_meta($post_id, 'madelyn_event_end', wp_date('Y-m-d\TH:i', $start + ($event['duration'] * MINUTE_IN_SECONDS)));
            update_post_meta($post_id, 'madelyn_event_venue', $event['venue']);
            update_post_meta($post_id, 'madelyn_event_address', $event['address']);
            update_post_meta($post_id, 'madelyn_event_status', $event['status']);
        }

        $hello = get_page_by_path('hello-world', OBJECT, 'post');
        if ($hello && $hello->post_title === 'Hello world!') {
            wp_trash_post($hello->ID);
        }
        $journal_posts = [
            ['slug' => 'welcome-to-madelyn-days-imaginative-world', 'title' => 'Welcome to Madelyn Day’s Imaginative World', 'excerpt' => 'Meet the dreams, memories, mysteries, practical lessons, and lifelong curiosity behind Madelyn’s growing collection.', 'image' => 'journal-dreams.webp'],
            ['slug' => 'stories-that-begin-in-dreams', 'title' => 'Stories That Begin in Dreams', 'excerpt' => 'Vivid dreams and long walks with beloved dogs can become the first sparks of unforgettable stories.', 'image' => 'journal-stories.webp'],
            ['slug' => 'a-life-across-cultures-careers-and-stories', 'title' => 'A Life Across Cultures, Careers, and Stories', 'excerpt' => 'Teaching, language, travel, service, animal rescue, and astronomy all bring a distinctive point of view to the page.', 'image' => 'journal-cultures.webp'],
        ];
        foreach ($journal_posts as $entry) {
            $post = get_page_by_path($entry['slug'], OBJECT, 'post');
            $post_id = $post ? $post->ID : wp_insert_post(['post_type' => 'post', 'post_status' => 'publish', 'post_name' => $entry['slug'], 'post_title' => $entry['title'], 'post_excerpt' => $entry['excerpt'], 'post_content' => '<p>' . esc_html($entry['excerpt']) . '</p><p>This journal will share new-release updates, reflections on storytelling, event news, and notes from behind the books.</p>']);
            if (!is_wp_error($post_id) && !has_post_thumbnail($post_id)) {
                $attachment_id = self::seed_attachment($asset_dir . '/' . $entry['image'], $entry['title']);
                if ($attachment_id) {
                    set_post_thumbnail($post_id, $attachment_id);
                }
            }
        }

        update_option('madelyn_seed_content_version', 2);
    }

    /**
     * Repair media relationships after a database-first deployment.
     *
     * A migrated database can contain the seeded posts before the custom theme is
     * active on the destination. In that case the original seeder cannot locate
     * the bundled image files and the posts arrive without featured media.
     */
    public static function repair_missing_featured_media(): void {
        if ((int) get_option('madelyn_featured_media_repair_version', 0) >= 1) {
            return;
        }

        $asset_dir = get_stylesheet_directory() . '/assets/images';
        $assets = [
            'book' => [
                'shut-up-and-dig' => 'shut-up-and-dig.png',
                'the-veil-beyond-the-walls' => 'veil-beyond-the-walls.png',
                'the-house-that-whispers' => 'house-that-whispers.png',
                'choose-train-your-new-best-friend' => 'choose-train-best-friend.png',
                'unveiling-the-truth' => 'unveiling-the-truth.png',
                'the-chair-the-car-the-chaos-and-the-siamese-cat' => 'chair-car-chaos-cat.png',
                'where-all-prayers-meet' => 'where-all-prayers-meet.png',
            ],
            'post' => [
                'welcome-to-madelyn-days-imaginative-world' => 'journal-dreams.webp',
                'stories-that-begin-in-dreams' => 'journal-stories.webp',
                'a-life-across-cultures-careers-and-stories' => 'journal-cultures.webp',
            ],
        ];

        foreach ($assets as $post_type => $items) {
            foreach ($items as $slug => $filename) {
                $post = get_page_by_path($slug, OBJECT, $post_type);
                if (!$post || has_post_thumbnail($post->ID)) {
                    continue;
                }
                $attachment_id = self::seed_attachment($asset_dir . '/' . $filename, get_the_title($post) . ($post_type === 'book' ? ' book cover' : ''));
                if ($attachment_id) {
                    set_post_thumbnail($post->ID, $attachment_id);
                }
            }
        }

        update_option('madelyn_featured_media_repair_version', 1);
    }

    /**
     * Add newly confirmed purchase destinations without overwriting URLs that
     * an editor has already customized in WordPress.
     */
    public static function sync_known_purchase_urls(): void {
        if ((int) get_option('madelyn_purchase_url_sync_version', 0) >= 1) {
            return;
        }

        $purchase_urls = [
            'the-chair-the-car-the-chaos-and-the-siamese-cat' => 'https://www.amazon.com/CHAIR-CAR-CHAOS-Siamese-Cat/dp/B0FD7FDV51/',
        ];

        foreach ($purchase_urls as $slug => $url) {
            $book = get_page_by_path($slug, OBJECT, 'book');
            if (!$book || get_post_meta($book->ID, 'madelyn_purchase_url', true)) {
                continue;
            }

            update_post_meta($book->ID, 'madelyn_purchase_url', esc_url_raw($url));
            update_post_meta($book->ID, 'madelyn_availability', 'available');
        }

        update_option('madelyn_purchase_url_sync_version', 1);
    }

    private static function seed_attachment(string $source, string $title): int {
        if (!file_exists($source)) {
            return 0;
        }
        $seed_key = basename($source);
        $existing = get_posts(['post_type' => 'attachment', 'post_status' => 'inherit', 'posts_per_page' => 1, 'fields' => 'ids', 'meta_key' => '_madelyn_seed_asset', 'meta_value' => $seed_key]);
        if ($existing) {
            return (int) $existing[0];
        }
        $uploads = wp_upload_dir();
        if (!empty($uploads['error'])) {
            return 0;
        }
        $filename = wp_unique_filename($uploads['path'], $seed_key);
        $target = trailingslashit($uploads['path']) . $filename;
        if (!copy($source, $target)) {
            return 0;
        }
        $filetype = wp_check_filetype($filename, null);
        $attachment_id = wp_insert_attachment(['post_mime_type' => $filetype['type'], 'post_title' => $title, 'post_status' => 'inherit'], $target);
        if (is_wp_error($attachment_id)) {
            return 0;
        }
        require_once ABSPATH . 'wp-admin/includes/image.php';
        wp_update_attachment_metadata($attachment_id, wp_generate_attachment_metadata($attachment_id, $target));
        update_post_meta($attachment_id, '_madelyn_seed_asset', $seed_key);
        update_post_meta($attachment_id, '_wp_attachment_image_alt', $title);
        return (int) $attachment_id;
    }

    public static function register_content_types(): void {
        register_post_type('book', [
            'labels' => self::labels('Book', 'Books'),
            'public' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'books'],
            'show_in_rest' => true,
            'menu_icon' => 'dashicons-book-alt',
            'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'author', 'page-attributes'],
        ]);

        register_taxonomy('book_genre', ['book'], [
            'labels' => ['name' => 'Book Genres', 'singular_name' => 'Book Genre'],
            'public' => true,
            'hierarchical' => true,
            'show_in_rest' => true,
            'rewrite' => ['slug' => 'book-genre'],
        ]);

        register_post_type('event', [
            'labels' => self::labels('Event', 'Events'),
            'public' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'events'],
            'show_in_rest' => true,
            'menu_icon' => 'dashicons-calendar-alt',
            'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'author'],
        ]);

        register_post_type('review', [
            'labels' => self::labels('Review', 'Reviews'),
            'public' => true,
            'has_archive' => false,
            'rewrite' => ['slug' => 'reviews'],
            'show_in_rest' => true,
            'menu_icon' => 'dashicons-star-filled',
            'supports' => ['title', 'editor', 'revisions', 'author', 'page-attributes'],
        ]);
    }

    private static function labels(string $singular, string $plural): array {
        return [
            'name' => $plural,
            'singular_name' => $singular,
            'add_new_item' => "Add New {$singular}",
            'edit_item' => "Edit {$singular}",
            'new_item' => "New {$singular}",
            'view_item' => "View {$singular}",
            'search_items' => "Search {$plural}",
            'not_found' => "No {$plural} found",
            'not_found_in_trash' => "No {$plural} found in Trash",
            'all_items' => "All {$plural}",
            'menu_name' => $plural,
        ];
    }

    public static function register_meta(): void {
        $book_fields = [
            'madelyn_subtitle' => 'string',
            'madelyn_purchase_url' => 'string',
            'madelyn_asin' => 'string',
            'madelyn_publication_date' => 'string',
            'madelyn_availability' => 'string',
            'madelyn_featured' => 'boolean',
        ];
        $event_fields = [
            'madelyn_event_start' => 'string',
            'madelyn_event_end' => 'string',
            'madelyn_event_venue' => 'string',
            'madelyn_event_address' => 'string',
            'madelyn_event_ticket_url' => 'string',
            'madelyn_event_status' => 'string',
        ];
        $review_fields = [
            'madelyn_reviewer' => 'string',
            'madelyn_rating' => 'integer',
            'madelyn_review_source' => 'string',
            'madelyn_review_url' => 'string',
            'madelyn_review_book' => 'integer',
            'madelyn_featured' => 'boolean',
        ];

        self::register_fields('book', $book_fields);
        self::register_fields('event', $event_fields);
        self::register_fields('review', $review_fields);
    }

    private static function register_fields(string $post_type, array $fields): void {
        foreach ($fields as $key => $type) {
            register_post_meta($post_type, $key, [
                'type' => $type,
                'single' => true,
                'show_in_rest' => true,
                'sanitize_callback' => $type === 'boolean' ? 'rest_sanitize_boolean' : ($type === 'integer' ? 'absint' : 'sanitize_text_field'),
                'auth_callback' => static fn(): bool => current_user_can('edit_posts'),
            ]);
        }
    }

    public static function add_meta_boxes(): void {
        add_meta_box('madelyn_book_details', 'Book Details', [self::class, 'book_meta_box'], 'book', 'normal', 'high');
        add_meta_box('madelyn_event_details', 'Event Details', [self::class, 'event_meta_box'], 'event', 'normal', 'high');
        add_meta_box('madelyn_review_details', 'Review Details', [self::class, 'review_meta_box'], 'review', 'normal', 'high');
    }

    private static function nonce(): void {
        wp_nonce_field(self::NONCE_ACTION, self::NONCE_NAME);
    }

    private static function field(int $post_id, string $key, string $label, string $type = 'text', array $options = []): void {
        $value = get_post_meta($post_id, $key, true);
        echo '<p><label for="' . esc_attr($key) . '"><strong>' . esc_html($label) . '</strong></label><br>';
        if ($type === 'select') {
            echo '<select class="widefat" id="' . esc_attr($key) . '" name="' . esc_attr($key) . '">';
            foreach ($options as $option_value => $option_label) {
                echo '<option value="' . esc_attr($option_value) . '" ' . selected($value, $option_value, false) . '>' . esc_html($option_label) . '</option>';
            }
            echo '</select>';
        } elseif ($type === 'checkbox') {
            echo '<input id="' . esc_attr($key) . '" name="' . esc_attr($key) . '" type="checkbox" value="1" ' . checked((bool) $value, true, false) . '> Feature this item';
        } else {
            echo '<input class="widefat" id="' . esc_attr($key) . '" name="' . esc_attr($key) . '" type="' . esc_attr($type) . '" value="' . esc_attr((string) $value) . '">';
        }
        echo '</p>';
    }

    public static function book_meta_box(WP_Post $post): void {
        self::nonce();
        self::field($post->ID, 'madelyn_subtitle', 'Subtitle');
        self::field($post->ID, 'madelyn_purchase_url', 'Amazon purchase URL (automatically creates the QR code)', 'url');
        self::field($post->ID, 'madelyn_asin', 'ISBN or ASIN');
        self::field($post->ID, 'madelyn_publication_date', 'Publication date', 'date');
        self::field($post->ID, 'madelyn_availability', 'Availability', 'select', [
            'available' => 'Available', 'coming-soon' => 'Coming soon', 'unavailable' => 'Unavailable', 'out-of-print' => 'Out of print',
        ]);
        self::field($post->ID, 'madelyn_featured', 'Homepage placement', 'checkbox');
        echo '<p>Paste the book’s full Amazon URL above and update the book. The public book page will automatically display the matching QR code and purchase button; no QR image upload is needed.</p>';
        echo '<p>Use the Featured Image panel for the book cover. Use Book Genres for categorization and Page Attributes for display order.</p>';
    }

    public static function event_meta_box(WP_Post $post): void {
        self::nonce();
        self::field($post->ID, 'madelyn_event_start', 'Start date and time', 'datetime-local');
        self::field($post->ID, 'madelyn_event_end', 'End date and time', 'datetime-local');
        self::field($post->ID, 'madelyn_event_venue', 'Venue');
        self::field($post->ID, 'madelyn_event_address', 'Address');
        self::field($post->ID, 'madelyn_event_ticket_url', 'Ticket or registration URL', 'url');
        self::field($post->ID, 'madelyn_event_status', 'Event status', 'select', [
            'scheduled' => 'Scheduled', 'virtual' => 'Virtual', 'sold-out' => 'Sold out', 'postponed' => 'Postponed', 'cancelled' => 'Cancelled',
        ]);
    }

    public static function review_meta_box(WP_Post $post): void {
        self::nonce();
        self::field($post->ID, 'madelyn_reviewer', 'Reviewer display name');
        self::field($post->ID, 'madelyn_rating', 'Star rating (1–5)', 'number');
        self::field($post->ID, 'madelyn_review_source', 'Source');
        self::field($post->ID, 'madelyn_review_url', 'Original review URL', 'url');
        self::field($post->ID, 'madelyn_review_book', 'Related book ID', 'number');
        self::field($post->ID, 'madelyn_featured', 'Homepage placement', 'checkbox');
    }

    public static function save_meta(int $post_id): void {
        if (!isset($_POST[self::NONCE_NAME]) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST[self::NONCE_NAME])), self::NONCE_ACTION)) {
            return;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $post_type = get_post_type($post_id);
        $allowed = [
            'book' => ['madelyn_subtitle', 'madelyn_purchase_url', 'madelyn_asin', 'madelyn_publication_date', 'madelyn_availability', 'madelyn_featured'],
            'event' => ['madelyn_event_start', 'madelyn_event_end', 'madelyn_event_venue', 'madelyn_event_address', 'madelyn_event_ticket_url', 'madelyn_event_status'],
            'review' => ['madelyn_reviewer', 'madelyn_rating', 'madelyn_review_source', 'madelyn_review_url', 'madelyn_review_book', 'madelyn_featured'],
        ];
        if (!isset($allowed[$post_type])) {
            return;
        }

        foreach ($allowed[$post_type] as $key) {
            if (in_array($key, ['madelyn_featured'], true)) {
                update_post_meta($post_id, $key, isset($_POST[$key]) ? 1 : 0);
                continue;
            }
            if (!isset($_POST[$key])) {
                continue;
            }
            $raw = wp_unslash($_POST[$key]);
            if (str_ends_with($key, '_url')) {
                $value = esc_url_raw($raw);
            } elseif (in_array($key, ['madelyn_rating', 'madelyn_review_book'], true)) {
                $value = absint($raw);
            } else {
                $value = sanitize_text_field($raw);
            }
            update_post_meta($post_id, $key, $value);
        }
    }

    public static function book_columns(array $columns): array {
        $columns['book_availability'] = 'Availability';
        $columns['book_purchase'] = 'Purchase link';
        return $columns;
    }

    public static function book_column_values(string $column, int $post_id): void {
        if ($column === 'book_availability') {
            echo esc_html((string) get_post_meta($post_id, 'madelyn_availability', true));
        }
        if ($column === 'book_purchase') {
            $url = get_post_meta($post_id, 'madelyn_purchase_url', true);
            echo $url ? '<a href="' . esc_url($url) . '" target="_blank" rel="noreferrer">Open</a>' : 'Missing';
        }
    }
}

Madelyn_Day_Content::boot();
register_activation_hook(__FILE__, [Madelyn_Day_Content::class, 'activate']);
register_deactivation_hook(__FILE__, [Madelyn_Day_Content::class, 'deactivate']);
