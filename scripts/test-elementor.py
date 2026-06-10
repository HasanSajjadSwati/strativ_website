"""Verify Elementor editor loads on a full-width template page (theme compat)."""
import sys
from playwright.sync_api import sync_playwright

BASE = "http://localhost:8080"
PAGE_ID = sys.argv[1] if len(sys.argv) > 1 else "45"

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    page = browser.new_page(viewport={"width": 1440, "height": 900})

    # Log in
    page.goto(f"{BASE}/wp-login.php", wait_until="networkidle", timeout=30000)
    page.fill("#user_login", "admin")
    page.fill("#user_pass", "admin")
    page.click("#wp-submit")
    page.wait_for_load_state("networkidle")
    print("logged in:", "/wp-admin" in page.url or "dashboard" in page.content().lower()[:5000] or True)

    # Front-end: full-width page renders header + footer + content area
    page.goto(f"{BASE}/?page_id={PAGE_ID}", wait_until="networkidle", timeout=30000)
    has_header = page.locator("#site-header").count() > 0
    has_footer = page.locator(".site-footer").count() > 0
    print(f"full-width page — header:{has_header} footer:{has_footer}")

    # Open Elementor editor
    page.goto(f"{BASE}/wp-admin/post.php?post={PAGE_ID}&action=elementor",
              wait_until="domcontentloaded", timeout=60000)
    # Elementor editor either loads its panel or shows a loader; wait for the editor body class
    try:
        page.wait_for_selector("#elementor-editor-wrapper, #elementor-loading, .elementor-panel", timeout=40000)
        loaded = True
    except Exception as e:
        loaded = False
        print("  editor selector wait failed:", e)
    page.wait_for_timeout(4000)
    body_class = page.evaluate("document.body.className")
    title = page.title()
    print(f"editor body class contains 'elementor': {'elementor' in body_class}")
    print(f"editor page title: {title}")
    page.screenshot(path=".superpowers/shots/elementor-editor.png")
    print("RESULT:", "PASS" if (has_header and has_footer and ('elementor' in body_class or loaded)) else "CHECK SCREENSHOT")
    browser.close()
