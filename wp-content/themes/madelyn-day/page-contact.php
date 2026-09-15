<?php
get_header();
$recipient = sanitize_email((string) get_theme_mod('madelyn_contact_email', get_option('admin_email')));
?>
<main class="contact-page">
    <div class="shell contact-layout">
        <header>
            <p class="eyebrow">Contact Madelyn</p>
            <h1>Let’s Start a Conversation</h1>
            <p>For reader notes, event invitations, book inquiries, and other messages, complete the form and your device will prepare an email for you.</p>
            <p class="form-note">Your email application will open with the details filled in. Review the message and press Send there to deliver it.</p>
        </header>
        <form class="contact-card" id="madelyn-contact-form" data-recipient="<?php echo esc_attr($recipient); ?>">
            <div class="form-grid">
                <div class="form-field"><label for="contact-name">Name</label><input id="contact-name" name="name" type="text" autocomplete="name" required></div>
                <div class="form-field"><label for="contact-company">Company <span class="form-note">(optional)</span></label><input id="contact-company" name="company" type="text" autocomplete="organization"></div>
                <div class="form-field full"><label for="contact-email">Email</label><input id="contact-email" name="email" type="email" autocomplete="email" required></div>
                <div class="form-field full"><label for="contact-topic">What’s this about?</label><select id="contact-topic" name="topic" required><option value="">Choose a topic</option><option>Reader message</option><option>Book inquiry</option><option>Event or speaking invitation</option><option>Media or interview request</option><option>Rights or publishing</option><option>Something else</option></select></div>
                <div class="form-field full"><label for="contact-message">How can Madelyn help?</label><textarea id="contact-message" name="message" minlength="10" required></textarea></div>
                <div class="form-field full"><button class="button" type="submit">Prepare email</button><p class="form-note">Opens your default email app. Nothing is stored by this website.</p><p class="form-status" id="contact-status" aria-live="polite"></p></div>
            </div>
        </form>
    </div>
</main>
<?php get_footer(); ?>
