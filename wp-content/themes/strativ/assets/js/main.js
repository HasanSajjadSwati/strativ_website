/* Strativ animations & interactions. */
(function () {
  "use strict";

  document.documentElement.classList.add("js");

  var reduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  var hasGsap = typeof gsap !== "undefined";
  if (hasGsap && typeof ScrollTrigger !== "undefined") gsap.registerPlugin(ScrollTrigger);

  /* ---------- Preloader ---------- */
  window.addEventListener("load", function () {
    var pre = document.getElementById("preloader");
    if (!pre) return;
    if (reduced || !hasGsap) { pre.remove(); return; }
    gsap.to(pre, { opacity: 0, duration: 0.5, delay: 0.3, onComplete: function () { pre.remove(); } });
  });

  /* ---------- Sticky header state ---------- */
  var header = document.getElementById("site-header");
  function onScroll() {
    if (header) header.classList.toggle("is-scrolled", window.scrollY > 24);
  }
  window.addEventListener("scroll", onScroll, { passive: true });
  onScroll();

  /* ---------- Mobile nav ---------- */
  var toggle = document.getElementById("nav-toggle");
  var nav = document.getElementById("site-nav");
  if (toggle && nav) {
    toggle.addEventListener("click", function () {
      var open = nav.classList.toggle("is-open");
      toggle.classList.toggle("is-open", open);
      toggle.setAttribute("aria-expanded", open ? "true" : "false");
      document.body.classList.toggle("nav-locked", open);
    });
    nav.addEventListener("click", function (e) {
      if (e.target.closest("a")) {
        nav.classList.remove("is-open");
        toggle.classList.remove("is-open");
        document.body.classList.remove("nav-locked");
      }
    });
  }

  if (!hasGsap || reduced) {
    document.querySelectorAll("[data-reveal]").forEach(function (el) { el.style.opacity = 1; });
    initCounters(true);
    initFilter();
    return;
  }

  /* ---------- Scroll reveals ---------- */
  document.querySelectorAll("[data-reveal-group]").forEach(function (group) {
    var items = group.querySelectorAll("[data-reveal]");
    gsap.fromTo(items, { opacity: 0, y: 36 }, {
      opacity: 1, y: 0, duration: 0.8, stagger: 0.12, ease: "power3.out",
      scrollTrigger: { trigger: group, start: "top 82%" }
    });
  });
  document.querySelectorAll("[data-reveal]").forEach(function (el) {
    if (el.closest("[data-reveal-group]")) return;
    gsap.fromTo(el, { opacity: 0, y: 36 }, {
      opacity: 1, y: 0, duration: 0.9, ease: "power3.out",
      scrollTrigger: { trigger: el, start: "top 85%" }
    });
  });

  /* ---------- Hero orbs: float + mouse parallax ---------- */
  document.querySelectorAll(".glow-orb").forEach(function (orb, i) {
    gsap.to(orb, {
      y: i % 2 ? 40 : -40, x: i % 2 ? -25 : 25,
      duration: 6 + i * 1.5, repeat: -1, yoyo: true, ease: "sine.inOut"
    });
  });
  var hero = document.querySelector(".hero");
  if (hero) {
    hero.addEventListener("mousemove", function (e) {
      var rx = (e.clientX / window.innerWidth - 0.5) * 2;
      var ry = (e.clientY / window.innerHeight - 0.5) * 2;
      document.querySelectorAll(".glow-orb").forEach(function (orb, i) {
        gsap.to(orb, { xPercent: rx * (6 + i * 4), yPercent: ry * (6 + i * 4), duration: 1.2, ease: "power2.out" });
      });
    });
  }

  /* ---------- Magnetic buttons ---------- */
  document.querySelectorAll(".btn-magnetic").forEach(function (btn) {
    btn.addEventListener("mousemove", function (e) {
      var r = btn.getBoundingClientRect();
      gsap.to(btn, { x: (e.clientX - r.left - r.width / 2) * 0.25, y: (e.clientY - r.top - r.height / 2) * 0.25, duration: 0.3 });
    });
    btn.addEventListener("mouseleave", function () {
      gsap.to(btn, { x: 0, y: 0, duration: 0.4, ease: "elastic.out(1, 0.4)" });
    });
  });

  initCounters(false);
  initFilter();

  /* ---------- Stat counters ---------- */
  function initCounters(instant) {
    document.querySelectorAll("[data-counter]").forEach(function (el) {
      var target = parseInt(el.getAttribute("data-counter"), 10) || 0;
      var suffix = el.getAttribute("data-suffix") || "";
      if (instant) { el.textContent = target + suffix; return; }
      var obj = { v: 0 };
      gsap.to(obj, {
        v: target, duration: 1.8, ease: "power2.out",
        scrollTrigger: { trigger: el, start: "top 85%", once: true },
        onUpdate: function () { el.textContent = Math.round(obj.v) + suffix; }
      });
    });
  }

  /* ---------- Portfolio filter ---------- */
  function initFilter() {
    var bar = document.querySelector(".filter-bar");
    if (!bar) return;
    var cards = document.querySelectorAll(".project-card");
    bar.addEventListener("click", function (e) {
      var btn = e.target.closest(".filter-btn");
      if (!btn) return;
      bar.querySelectorAll(".filter-btn").forEach(function (b) { b.classList.remove("is-active"); });
      btn.classList.add("is-active");
      var cat = btn.getAttribute("data-filter");
      cards.forEach(function (card) {
        var show = cat === "all" || (card.getAttribute("data-cats") || "").split(" ").indexOf(cat) !== -1;
        if (hasGsap && !reduced) {
          gsap.to(card, { opacity: show ? 1 : 0, scale: show ? 1 : 0.96, duration: 0.35, onComplete: function () { card.style.display = show ? "" : "none"; gsap.set(card, { clearProps: "scale" }); if (show) gsap.to(card, { opacity: 1, duration: 0.2 }); } });
        } else {
          card.style.display = show ? "" : "none";
          card.style.opacity = 1;
        }
      });
      if (hasGsap && typeof ScrollTrigger !== "undefined") setTimeout(function () { ScrollTrigger.refresh(); }, 450);
    });
  }
})();
