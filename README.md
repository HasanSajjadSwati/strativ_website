# Strativ — Company Website

A modern, animated, dark red/black WordPress site for **Strativ**, a software house & IT
services company (AI, POS, CMS, HMS, web & mobile). Custom Elementor-compatible theme,
filterable project portfolio, blog, careers, and contact form.

- **Live (local):** http://localhost:8080
- **Admin:** http://localhost:8080/wp-admin — user `admin` / pass `admin`

---

## Running the site locally

This project ships with a **portable, no-admin dev stack** (PHP + MariaDB + WP-CLI) under
`stack/`. Nothing is installed system-wide and no UAC/admin rights are needed.

```powershell
# Start MariaDB + the WordPress dev server (http://localhost:8080)
powershell -ExecutionPolicy Bypass -File scripts\start-dev.ps1

# Stop everything when done
powershell -ExecutionPolicy Bypass -File scripts\stop-dev.ps1
```

Leave the `start-dev` window open while you work — it runs the web server.

---

## Project layout

```
Strativ/
├─ wp/                         WordPress core install (gitignored)
├─ stack/                      Portable PHP, MariaDB, WP-CLI + DB data (gitignored)
├─ wp-content/                 ← version-controlled source, junctioned into wp/wp-content/
│  ├─ themes/strativ/          The custom theme (all design + templates live here)
│  └─ plugins/strativ-core/    Project & Career custom post types
├─ scripts/                    Dev + verification helpers
│  ├─ start-dev.ps1 / stop-dev.ps1
│  ├─ seed-content.php         Re-seeds placeholder content (idempotent)
│  └─ *.py                     Playwright screenshot / verification scripts
└─ docs/superpowers/           Design spec + implementation plan
```

The theme and plugin folders are **NTFS junctions** into the WordPress install, so editing
files in `wp-content/` updates the live site immediately, and only your source (not WP core)
is tracked in git.

---

## Editing content

Everything is placeholder content you can swap from **wp-admin**:

| What | Where in admin |
|------|----------------|
| Projects (portfolio) | **Projects** → edit. Set featured image, category, and the Project Details fields (client, year, tech stack, challenge/solution/results). |
| Project categories | **Projects → Project Categories** (AI, POS, CMS, HMS, Web, Mobile). |
| Careers | **Careers** → edit. Set location, employment type, department. |
| Blog posts | **Posts**. |
| Pages (Home/About/Services/Contact/Careers) | **Pages**. Home, About, Services, Contact and Careers use custom theme templates. |
| Menu | **Appearance → Menus** (the "Primary" menu). |
| Contact form | **Contact → Contact Forms** (Contact Form 7). |

To regenerate all placeholder content from scratch:

```powershell
stack\wp.bat eval-file scripts\seed-content.php --path=wp
```

---

## Editing with Elementor

The theme is Elementor-compatible. Any page can be edited visually:

1. Edit a page → **Edit with Elementor**, or
2. For a blank canvas, set the page template to **Full Width (Elementor)** (Page → Template),
   which removes theme containers so Elementor owns the whole area between header and footer.

The custom theme styling and Elementor coexist — Elementor-built pages keep the site header,
footer, fonts and colors.

---

## Design system

- **Colors:** near-black `#0b0b0d` base, red gradient `#c41230 → #ff1e3c`, glow accents.
- **Fonts:** Space Grotesk (headings) + Inter (body).
- **Animation:** GSAP + ScrollTrigger (bundled locally in the theme) — scroll reveals,
  floating glow orbs, animated counters, magnetic buttons, portfolio filtering. Respects
  `prefers-reduced-motion`.
- All tokens live at the top of `wp-content/themes/strativ/assets/css/main.css`.

---

## Deployment (Docker / Coolify)

The repo is container-ready. A `Dockerfile` bakes the custom theme + plugin onto the official
WordPress image, and `docker-compose.yaml` runs WordPress + MariaDB. On first boot the container
auto-installs WordPress, activates the theme and all plugins, and seeds the placeholder content —
no manual setup.

What persists vs ships:
- **Application code** (theme, plugin) ships *inside the image* — every deploy is reproducible from git.
- **Uploads** (`wp-content/uploads`) and the **database** live in named volumes and survive redeploys.

### Deploy on Coolify

1. In Coolify: **New Resource → Docker Compose**, point it at this Git repo.
2. Coolify detects `docker-compose.yaml` and auto-generates every secret it references
   (`SERVICE_PASSWORD_MYSQL`, `SERVICE_PASSWORD_MYSQLROOT`, `SERVICE_PASSWORD_WPADMIN`,
   `SERVICE_USER_MYSQL`) and a public domain (`SERVICE_FQDN_WORDPRESS`).
3. Deploy. First boot installs + seeds automatically (watch the logs for `[strativ] Init done.`).
4. Your generated admin password is in Coolify under the resource's **Environment Variables**
   (`SERVICE_PASSWORD_WPADMIN`). Log in at `https://<your-domain>/wp-admin` as `admin`.

No `.env` is needed on Coolify — it injects everything.

### Run the container locally

```bash
cp .env.example .env        # fill in passwords
docker compose up --build
```

For local access add a port mapping to the `wordpress` service (Coolify uses its own proxy, so
it's omitted by default):

```yaml
    ports:
      - "8080:80"
```

Then visit http://localhost:8080.

---

## Notes

- The **portable dev stack** under `stack/` (used by `start-dev.ps1`) and the Docker deployment
  are independent. The stack is for local Windows development without Docker; the Dockerfile is
  for shipping.
- The dev DB lives in `stack/data/` and is not committed. Move the project by copying the repo
  and re-running the stack setup, or export/import the database.
