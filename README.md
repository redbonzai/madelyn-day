# Madelyn Day WordPress site

A local WordPress project for Madelyn Day with owner-managed books, blog posts, events, and reviews.

## Start the local site

The default local runtime uses the official WordPress Playground CLI and Node.js 24. The included `.nvmrc` selects the tested version when NVM is installed.

```bash
./bin/start-local.sh
```

The script loads NVM, installs Node.js 24.19.0 when necessary, puts that Node
binary first on `PATH`, installs missing dependencies, repairs the native
WordPress Playground dependency when necessary, and starts the site. Stop the
foreground server with `Ctrl+C`.

- Website: <http://localhost:8080>
- Administration: <http://localhost:8080/wp-admin>

WordPress Playground supplies its local administrator session. The project blueprint automatically activates **Madelyn Day Content**, activates the **Madelyn Day** theme, configures the site title, and enables readable permalinks.

The Playground site persists locally between runs. It is intended for development; the finished site will later be migrated to managed WordPress hosting.

## Optional Docker runtime

Once Docker Desktop is working, the production-like MariaDB runtime can be initialized with:

```bash
./bin/setup.sh
```

Docker administrator details are stored in `.env`. Change the placeholder password and email before using this project outside local development.

Stop the containers without deleting content:

```bash
./bin/stop.sh
```

## Content management

- **Books:** catalog details, cover image, genre, publication information, availability, purchase destination, and ordering.
- **Posts:** WordPress’s native blog editor, drafts, revisions, preview, scheduling, categories, and tags.
- **Events:** event details, venue, dates, status, ticket destination, featured image, draft, and publishing controls.
- **Reviews:** reviewer, rating, source, associated book, featured state, and publishing controls.
- **Contact:** a configurable recipient and a visitor form that prepares a message in the visitor's default email application.
- **Newsletter:** a provider-neutral signup destination configured in the theme Customizer; see `EMAIL_MARKETING_PLAN.md`.

Set the public contact recipient and newsletter URL under **Appearance → Customize → Contact & newsletter**. If no contact override is entered, the form uses the WordPress administration email from **Settings → General**.

### Add or update a book’s Amazon link and QR code

1. In WordPress, open **Books → All Books** and select the book.
2. In **Book Details**, paste the Amazon product URL into **Amazon purchase URL (automatically creates the QR code)**.
3. Set **Availability** to **Available**, then select **Update**.

The public book page automatically generates its QR code and Amazon purchase button from that URL. No QR image needs to be created or uploaded. Clearing the URL removes both elements.

## Project structure

- `wp-content/themes/madelyn-day/` — public-facing custom theme.
- `wp-content/plugins/madelyn-content/` — portable Books, Events, and Reviews content model.
- `compose.yaml` — local WordPress and MariaDB services.
- `bin/setup.sh` — repeatable local installation and activation.

Content types live in a plugin rather than the theme so the records remain available if the visual theme changes later.
