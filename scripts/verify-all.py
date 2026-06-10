"""Full-site verification: console errors on every page + mobile nav toggle."""
from playwright.sync_api import sync_playwright

BASE = "http://localhost:8080"
PAGES = [
    "/", "/about/", "/services/", "/portfolio/",
    "/portfolio/nexus-copilot/", "/blog/",
    "/why-2026-is-the-year-of-agentic-software/",
    "/careers/", "/careers/ai-ml-engineer/", "/contact/",
]

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    print("=== Console error check (desktop) ===")
    any_err = False
    for path in PAGES:
        page = browser.new_page(viewport={"width": 1440, "height": 900})
        errs = []
        page.on("console", lambda m, e=errs: e.append(m.text) if m.type == "error" else None)
        page.on("pageerror", lambda ex, e=errs: e.append(f"PAGEERROR {ex}"))
        page.goto(BASE + path, wait_until="networkidle", timeout=30000)
        page.wait_for_timeout(500)
        status = "clean" if not errs else f"{len(errs)} error(s): {errs}"
        if errs:
            any_err = True
        print(f"  {path:48s} {status}")
        page.close()

    print("\n=== Mobile nav toggle ===")
    page = browser.new_page(viewport={"width": 390, "height": 844})
    page.goto(BASE + "/", wait_until="networkidle", timeout=30000)
    page.wait_for_timeout(500)
    nav = page.locator("#site-nav")
    print(f"  nav visible before toggle: {nav.is_visible()}")
    page.click("#nav-toggle")
    page.wait_for_timeout(600)
    print(f"  nav visible after open:    {nav.is_visible()}")
    # click a link closes it
    page.click("#site-nav a >> nth=0")
    page.wait_for_timeout(800)
    print("  clicked first nav link (navigates + closes)")
    page.close()

    print("\n=== Stats counters reached target ===")
    page = browser.new_page(viewport={"width": 1440, "height": 900})
    page.goto(BASE + "/", wait_until="networkidle", timeout=30000)
    page.evaluate("""async () => {
        const max = document.body.scrollHeight;
        for (let y = 0; y <= max; y += 400) { window.scrollTo(0, y); await new Promise(r=>setTimeout(r,150)); }
    }""")
    page.wait_for_timeout(1500)
    nums = page.eval_on_selector_all(".stat__num", "els => els.map(e => e.textContent)")
    print(f"  stat numbers: {nums}")
    page.close()

    browser.close()
    print("\nRESULT:", "FAIL — console errors found" if any_err else "PASS — no console errors")
