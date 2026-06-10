"""Verify the portfolio category filter hides non-matching cards."""
from playwright.sync_api import sync_playwright

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    page = browser.new_page(viewport={"width": 1440, "height": 900})
    page.goto("http://localhost:8080/portfolio/", wait_until="networkidle", timeout=30000)
    page.wait_for_timeout(800)

    total = page.locator(".project-card").count()
    print(f"total cards: {total}")

    # Click the "AI" filter
    page.click(".filter-btn[data-filter='ai']")
    page.wait_for_timeout(700)

    visible = 0
    for i in range(total):
        card = page.locator(".project-card").nth(i)
        if card.is_visible():
            visible += 1
    print(f"visible after AI filter: {visible}")

    # Back to all
    page.click(".filter-btn[data-filter='all']")
    page.wait_for_timeout(700)
    visible_all = sum(1 for i in range(total) if page.locator(".project-card").nth(i).is_visible())
    print(f"visible after All filter: {visible_all}")

    browser.close()
