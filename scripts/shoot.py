"""Screenshot Strativ pages at desktop + mobile and capture console errors.

Usage: python scripts/shoot.py <url-path> [<url-path> ...]
Saves PNGs to .superpowers/shots/ and prints any console errors / page errors.
"""
import sys, os, re
from playwright.sync_api import sync_playwright

BASE = "http://localhost:8080"
OUT = os.path.join(os.path.dirname(__file__), "..", ".superpowers", "shots")
os.makedirs(OUT, exist_ok=True)

paths = sys.argv[1:] or ["/"]

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    for path in paths:
        slug = re.sub(r"[^a-z0-9]+", "-", path.strip("/").lower()) or "home"
        for label, w, h in (("desktop", 1440, 900), ("mobile", 390, 844)):
            page = browser.new_page(viewport={"width": w, "height": h})
            errors = []
            page.on("console", lambda m: errors.append(f"[{m.type}] {m.text}") if m.type == "error" else None)
            page.on("pageerror", lambda e: errors.append(f"[pageerror] {e}"))
            url = BASE + path
            page.goto(url, wait_until="networkidle", timeout=30000)
            # Scroll through the page so scroll-triggered reveals fire, like a real user
            page.evaluate("""async () => {
                const step = Math.round(window.innerHeight * 0.35);
                const max = document.body.scrollHeight;
                for (let y = 0; y <= max; y += step) {
                    window.scrollTo(0, y);
                    await new Promise(r => requestAnimationFrame(() => setTimeout(r, 220)));
                }
                window.scrollTo(0, max);
                await new Promise(r => setTimeout(r, 400));
                window.scrollTo(0, 0);
            }""")
            page.wait_for_timeout(1000)  # let reveals/counters settle
            out = os.path.join(OUT, f"{slug}-{label}.png")
            page.screenshot(path=out, full_page=True)
            print(f"OK {url} [{label}] -> {os.path.relpath(out)}")
            if errors:
                for e in errors:
                    print(f"   CONSOLE {e}")
            page.close()
    browser.close()
