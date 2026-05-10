/**
 * JourneyOS AI — Landing Page JavaScript
 */
document.addEventListener('DOMContentLoaded', () => {

  // --- Scroll-based nav styling ---
  const nav = document.getElementById('mainNav');
  window.addEventListener('scroll', () => {
    nav.classList.toggle('scrolled', window.scrollY > 50);
  });

  // --- Particle System ---
  const particlesEl = document.getElementById('particles');
  if (particlesEl) {
    for (let i = 0; i < 40; i++) {
      const p = document.createElement('div');
      p.className = 'particle';
      p.style.left = Math.random() * 100 + '%';
      p.style.top = (60 + Math.random() * 40) + '%';
      p.style.setProperty('--tx', (Math.random() - 0.5) * 200 + 'px');
      p.style.setProperty('--ty', -(200 + Math.random() * 400) + 'px');
      p.style.animationDelay = Math.random() * 8 + 's';
      p.style.animationDuration = (6 + Math.random() * 6) + 's';
      const colors = ['#00D4FF', '#A855F7', '#FF6B35', '#3B82F6', '#10B981'];
      p.style.background = colors[Math.floor(Math.random() * colors.length)];
      p.style.width = p.style.height = (2 + Math.random() * 3) + 'px';
      particlesEl.appendChild(p);
    }
  }

  // --- Counter Animation ---
  const counters = document.querySelectorAll('[data-count]');
  const animateCounter = (el) => {
    const target = parseInt(el.dataset.count);
    const duration = 2000;
    const start = performance.now();
    const step = (now) => {
      const progress = Math.min((now - start) / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 3);
      el.textContent = Math.floor(eased * target).toLocaleString();
      if (progress < 1) requestAnimationFrame(step);
      else el.textContent = target.toLocaleString();
    };
    requestAnimationFrame(step);
  };
  const counterObserver = new IntersectionObserver((entries) => {
    entries.forEach(e => { if (e.isIntersecting) { animateCounter(e.target); counterObserver.unobserve(e.target); }});
  }, { threshold: 0.5 });
  counters.forEach(c => counterObserver.observe(c));

  // --- Scroll Reveal ---
  const reveals = document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale');
  const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); }});
  }, { threshold: 0.15, rootMargin: '0px 0px -50px 0px' });
  reveals.forEach(el => revealObserver.observe(el));

  // --- Mood Engine Demo ---
  const moodData = {
    adventure: { title: 'Adventure Mode', desc: 'Bold colors, faster transitions, intense activity suggestions, and adrenaline-pumping destinations.', color: '#FF6B35', cards: [{ e: '🏔️', t: 'Hiking Machu Picchu' }, { e: '🤿', t: 'Scuba in Great Barrier Reef' }, { e: '🪂', t: 'Skydiving in Dubai' }] },
    romantic: { title: 'Romantic Mode', desc: 'Soft pinks, gentle animations, sunset dinners, and intimate experiences for two.', color: '#EC4899', cards: [{ e: '🌅', t: 'Sunset in Santorini' }, { e: '🍷', t: 'Wine Tasting in Tuscany' }, { e: '🛶', t: 'Gondola Ride in Venice' }] },
    healing: { title: 'Healing Mode', desc: 'Softer gradients, calmer motion, peaceful destinations, and sunrise wellness activities.', color: '#10B981', cards: [{ e: '🧖', t: 'Hot Springs in Iceland' }, { e: '🏖️', t: 'Yoga Retreat in Bali' }, { e: '🌸', t: 'Cherry Blossoms in Kyoto' }] },
    luxury: { title: 'Luxury Mode', desc: 'Gold accents, premium suggestions, five-star experiences, and VIP treatment everywhere.', color: '#F59E0B', cards: [{ e: '🏨', t: 'Burj Al Arab Suite' }, { e: '🍽️', t: 'Michelin Star Dining' }, { e: '🚁', t: 'Helicopter Tour NYC' }] },
    party: { title: 'Party Mode', desc: 'Vibrant purples, energetic animations, nightlife hotspots, and social experiences.', color: '#8B5CF6', cards: [{ e: '🎶', t: 'Ibiza Beach Club' }, { e: '🍸', t: 'Rooftop Bars in Bangkok' }, { e: '🎪', t: 'Rio Carnival' }] },
    spiritual: { title: 'Spiritual Mode', desc: 'Deep indigos, meditative pace, sacred sites, and transformative journeys.', color: '#6366F1', cards: [{ e: '🕉️', t: 'Temples of Varanasi' }, { e: '⛩️', t: 'Shrines of Kyoto' }, { e: '🏔️', t: 'Meditation in Nepal' }] },
    productivity: { title: 'Productivity Mode', desc: 'Clean blues, efficient routing, co-working cafés, and optimized schedules.', color: '#3B82F6', cards: [{ e: '💻', t: 'Cafés of Lisbon' }, { e: '📶', t: 'Co-work in Chiang Mai' }, { e: '🏙️', t: 'Startup Hub Berlin' }] },
    solo: { title: 'Solo Exploration', desc: 'Electric cyan, discovery-first pacing, hidden gems, and serendipitous encounters.', color: '#00D4FF', cards: [{ e: '🌍', t: 'Backpacking SE Asia' }, { e: '🚶', t: 'Walking Tour Rome' }, { e: '📷', t: 'Street Photography Tokyo' }] }
  };

  document.querySelectorAll('.mood-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.mood-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const mood = btn.dataset.mood;
      const data = moodData[mood];
      if (!data) return;
      document.getElementById('moodTitle').textContent = data.title;
      document.getElementById('moodDesc').textContent = data.desc;
      const badge = document.getElementById('moodBadge');
      badge.style.background = data.color + '26';
      badge.style.color = data.color;
      const cardsEl = document.getElementById('moodCards');
      cardsEl.innerHTML = data.cards.map(c =>
        `<div class="mood-demo-card" style="border-color:${data.color}44;"><span>${c.e}</span><p>${c.t}</p></div>`
      ).join('');
    });
  });

  // --- Smooth scroll for anchor links ---
  document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', (e) => {
      e.preventDefault();
      const target = document.querySelector(a.getAttribute('href'));
      if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
  });

});
