</main>

<footer class="site-footer">
  <div class="container">
    <div class="footer-grid">
      <div class="footer-brand">
        <div class="footer-logo"><img src="/logo.png" alt="OM Computers" /></div>
        <p class="footer-tagline">Pune's trusted partner for computer repair, CCTV, biometric systems, IT support, and digital services.</p>
        <div style="display:flex; gap:12px; margin-top:20px;">
          <a href="<?= e(wa_link('Hi OM Computers!')) ?>" target="_blank" rel="noopener" class="btn-primary" style="font-size:13px; padding:8px 16px;">WhatsApp Us</a>
        </div>
      </div>
      <div class="footer-col">
        <h4>Services</h4>
        <ul>
          <li><a href="/services/device-repair">Device Repair</a></li>
          <li><a href="/services/cctv-surveillance">CCTV &amp; Surveillance</a></li>
          <li><a href="/services/biometric-access">Biometric &amp; Access</a></li>
          <li><a href="/services/it-support-amc">IT Support &amp; AMC</a></li>
          <li><a href="/services/web-digital">Web &amp; Digital</a></li>
          <li><a href="/services/hardware-sales">Hardware Sales</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Quick Links</h4>
        <ul>
          <li><a href="/">Home</a></li>
          <li><a href="/blog">Blog</a></li>
          <li><a href="/parts">Parts List</a></li>
          <li><a href="/contact">Contact</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Contact</h4>
        <div class="footer-contact-item">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
          <a href="<?= e(tel_link()) ?>"><?= e($S['phone']) ?></a>
        </div>
        <div class="footer-contact-item">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          <a href="mailto:<?= e($S['email']) ?>"><?= e($S['email']) ?></a>
        </div>
        <div class="footer-contact-item">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          <span><?= e($S['address']) ?></span>
        </div>
        <div class="footer-contact-item">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          <span><?= e($S['openHours']) ?></span>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      <span>© <?= date('Y') ?> OM Computers. All rights reserved.</span>
      <span>Website by <a href="https://jsntechmark.com" target="_blank" rel="noopener">JSN Techmark</a></span>
    </div>
  </div>
</footer>

<script>
  // Scroll reveal
  (function () {
    if (typeof IntersectionObserver === 'undefined') { document.querySelectorAll('.fade-up').forEach(el=>el.classList.add('visible')); return; }
    var obs = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) { if (en.isIntersecting) { en.target.classList.add('visible'); obs.unobserve(en.target); } });
    }, { threshold: 0.12 });
    document.querySelectorAll('.fade-up').forEach(function (el) { obs.observe(el); });
  })();
  // Mobile drawer
  (function () {
    var t = document.getElementById('navToggle'), d = document.getElementById('mobileDrawer');
    if (!t || !d) return;
    function open(){ d.classList.add('open'); t.setAttribute('aria-expanded','true'); document.body.style.overflow='hidden'; }
    function close(){ d.classList.remove('open'); t.setAttribute('aria-expanded','false'); document.body.style.overflow=''; }
    t.addEventListener('click', open);
    d.querySelectorAll('[data-close]').forEach(function(el){ el.addEventListener('click', close); });
    d.querySelectorAll('a').forEach(function(a){ a.addEventListener('click', close); });
    document.addEventListener('keydown', function(e){ if(e.key==='Escape') close(); });
  })();
  // Navbar scrolled state
  (function () {
    var nav = document.getElementById('main-nav');
    if (!nav) return;
    var onScroll = function(){ nav.classList.toggle('scrolled', window.scrollY > 60); };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  })();
</script>
</body>
</html>
