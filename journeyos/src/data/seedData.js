// ============================================
// JourneyOS — Seed Data
// ============================================

export const MOODS = {
  adventure: {
    key: 'adventure',
    label: '🔥 Bold',
    name: 'Adventure',
    score: 75,
    subtitle: 'Tokyo · 18 days away · Adventure mode',
    metrics: { exhaustion: 62, budget: 48, crowd: 35, weather: 20, fatigue: 44 },
    ai: '<strong>Adventure mode active.</strong> Day 3 Fuji + Hakone is peak intensity — consider adding a recovery morning on Day 4 before Akihabara for sustained energy.',
    color: '#FB923C',
    rgb: '251,146,60',
    gradient: 'from-orange-500 to-amber-500',
  },
  romantic: {
    key: 'romantic',
    label: '💕 Soft',
    name: 'Romantic',
    score: 88,
    subtitle: 'Tokyo · 18 days away · Romantic mode',
    metrics: { exhaustion: 30, budget: 55, crowd: 25, weather: 22, fatigue: 28 },
    ai: '<strong>Romantic mode.</strong> Shinjuku Gyoen at golden hour and a private ryokan onsen for two are top picks. I\'ve softened the pace significantly.',
    color: '#F472B6',
    rgb: '244,114,182',
    gradient: 'from-pink-400 to-rose-500',
  },
  healing: {
    key: 'healing',
    label: '🌿 Calm',
    name: 'Solo Healing',
    score: 92,
    subtitle: 'Tokyo · 18 days away · Healing mode',
    metrics: { exhaustion: 20, budget: 40, crowd: 18, weather: 15, fatigue: 22 },
    ai: '<strong>Solo healing mode.</strong> Crowd-heavy spots deprioritized. Sunrise temple visits and journaling blocks added every morning.',
    color: '#818CF8',
    rgb: '129,140,248',
    gradient: 'from-indigo-400 to-blue-500',
  },
  spiritual: {
    key: 'spiritual',
    label: '🕊️ Sacred',
    name: 'Spiritual',
    score: 85,
    subtitle: 'Tokyo · 18 days away · Spiritual mode',
    metrics: { exhaustion: 28, budget: 38, crowd: 20, weather: 18, fatigue: 25 },
    ai: '<strong>Spiritual journey.</strong> Meiji Shrine, Fushimi Inari at dawn, Zen gardens weighted heavily. Silent reflection time blocked each evening.',
    color: '#A78BFA',
    rgb: '167,139,250',
    gradient: 'from-violet-400 to-purple-500',
  },
  luxury: {
    key: 'luxury',
    label: '✨ Elite',
    name: 'Luxury',
    score: 82,
    subtitle: 'Tokyo · 18 days away · Luxury mode',
    metrics: { exhaustion: 35, budget: 85, crowd: 40, weather: 22, fatigue: 32 },
    ai: '<strong>Luxury mode.</strong> Hotels upgraded to 5-star ryokans. Michelin dining added. Budget at 85% — want me to optimize?',
    color: '#22D3EE',
    rgb: '34,211,238',
    gradient: 'from-cyan-400 to-teal-500',
  },
  party: {
    key: 'party',
    label: '🎉 Wild',
    name: 'Party',
    score: 68,
    subtitle: 'Tokyo · 18 days away · Party mode',
    metrics: { exhaustion: 78, budget: 72, crowd: 85, weather: 18, fatigue: 70 },
    ai: '<strong>Party mode unlocked.</strong> Roppongi nightlife, Golden Gai crawls, and Shibuya late-night spots prioritized. Recovery mornings auto-scheduled.',
    color: '#F43F5E',
    rgb: '244,63,94',
    gradient: 'from-rose-500 to-red-500',
  },
  workcation: {
    key: 'workcation',
    label: '💼 Focus',
    name: 'Workcation',
    score: 79,
    subtitle: 'Tokyo · 18 days away · Workcation mode',
    metrics: { exhaustion: 42, budget: 45, crowd: 30, weather: 20, fatigue: 38 },
    ai: '<strong>Workcation set.</strong> Mornings free for deep work. Co-working in Shibuya + Kyoto pinned. Afternoon activities capped at 3 hours.',
    color: '#34D399',
    rgb: '52,211,153',
    gradient: 'from-emerald-400 to-green-500',
  },
  solo: {
    key: 'solo',
    label: '🧭 Free',
    name: 'Solo Explorer',
    score: 80,
    subtitle: 'Tokyo · 18 days away · Solo mode',
    metrics: { exhaustion: 45, budget: 35, crowd: 30, weather: 22, fatigue: 40 },
    ai: '<strong>Solo explorer mode.</strong> Off-the-beaten-path spots prioritized. Local neighborhoods weighted over tourist zones. Maximum freedom in schedule.',
    color: '#8B5CF6',
    rgb: '139,92,246',
    gradient: 'from-purple-500 to-violet-600',
  },
};

export const ITINERARY_DAYS = [
  [
    { time: '07:30', emoji: '☀️', bg: '#FB923C22', name: 'Sunrise at Senso-ji Temple', detail: 'Asakusa · 45 min walk', badge: 'Culture', badgeColor: 'orange' },
    { time: '10:00', emoji: '🏯', bg: '#4C7BFF22', name: 'Imperial Palace Gardens', detail: 'Chiyoda · skip crowds before noon', badge: 'Landmark', badgeColor: 'blue' },
    { time: '13:30', emoji: '🍜', bg: '#9333EA22', name: 'Ichiran Ramen — solo booth', detail: 'Harajuku · AI pick', badge: 'Dining', badgeColor: 'purple' },
    { time: '16:00', emoji: '🎮', bg: '#22D3EE22', name: 'Akihabara explorer walk', detail: 'Electric Town · high energy', badge: 'Adventure', badgeColor: 'cyan' },
  ],
  [
    { time: '08:00', emoji: '⛩️', bg: '#9333EA22', name: 'Meiji Shrine — forest path', detail: 'Harajuku · peaceful morning', badge: 'Spiritual', badgeColor: 'purple' },
    { time: '11:00', emoji: '🛍️', bg: '#FB923C22', name: 'Takeshita Street market', detail: 'Harajuku · budget ₹3,000', badge: 'Shopping', badgeColor: 'orange' },
    { time: '15:00', emoji: '🌆', bg: '#4C7BFF22', name: 'Shibuya Sky observation', detail: 'Book 1 day ahead · dusk views', badge: 'Views', badgeColor: 'blue' },
    { time: '19:00', emoji: '🍣', bg: '#22D3EE22', name: 'Tsukiji market dinner', detail: 'Best tuna · ₹2,500', badge: 'Dining', badgeColor: 'cyan' },
  ],
  [
    { time: '06:00', emoji: '🗻', bg: '#34D39922', name: 'Fuji day trip — early bus', detail: 'Shinjuku station · 2h ride', badge: 'Epic', badgeColor: 'green' },
    { time: '11:30', emoji: '♨️', bg: '#9333EA22', name: 'Hakone onsen soak', detail: 'Ryokan style · book ahead', badge: 'Wellness', badgeColor: 'purple' },
    { time: '16:00', emoji: '🚄', bg: '#4C7BFF22', name: 'Shinkansen back to Tokyo', detail: '⚠️ High fatigue day — AI flag', badge: 'Transit', badgeColor: 'blue' },
  ],
  [
    { time: '09:00', emoji: '🏙️', bg: '#4C7BFF22', name: 'teamLab Planets — immersive', detail: 'Toyosu · book 3 days ahead', badge: 'Art', badgeColor: 'blue' },
    { time: '13:00', emoji: '🥢', bg: '#FB923C22', name: 'Depachika food hall', detail: 'Isetan Shinjuku · ₹1,800', badge: 'Dining', badgeColor: 'orange' },
    { time: '17:00', emoji: '🌸', bg: '#9333EA22', name: 'Shinjuku Gyoen stroll', detail: 'Golden hour · free entry', badge: 'Nature', badgeColor: 'purple' },
    { time: '20:00', emoji: '🍻', bg: '#22D3EE22', name: 'Golden Gai bar hop', detail: 'Old Tokyo nightlife · ₹2,000', badge: 'Nightlife', badgeColor: 'cyan' },
  ],
  [
    { time: '08:00', emoji: '🎋', bg: '#34D39922', name: 'Depart for Kyoto — Shinkansen', detail: '280km/h · 2h 15min', badge: 'Transit', badgeColor: 'green' },
    { time: '11:00', emoji: '⛩️', bg: '#9333EA22', name: 'Fushimi Inari — 1000 gates', detail: '2h moderate · sunrise photos', badge: 'Iconic', badgeColor: 'purple' },
    { time: '15:00', emoji: '🍵', bg: '#4C7BFF22', name: 'Matcha ceremony — Gion', detail: 'Traditional tea master', badge: 'Culture', badgeColor: 'blue' },
  ],
];

export const BUDGET_ITEMS = [
  { label: 'Flights', percent: 72, amount: '₹72k', color: '#4C7BFF' },
  { label: 'Hotels', percent: 55, amount: '₹55k', color: '#9333EA' },
  { label: 'Food', percent: 38, amount: '₹38k', color: '#FB923C' },
  { label: 'Activities', percent: 30, amount: '₹30k', color: '#22D3EE' },
  { label: 'Shopping', percent: 20, amount: '₹20k', color: '#34D399' },
];

export const TRIPS = [
  { id: 1, name: 'Tokyo Expedition', dates: 'Jun 12–24, 2025', emoji: '🗾', days: 12, cities: 4, budget: '₹2.1L', status: 'upcoming', progress: 30, friends: 3, mood: 'adventure' },
  { id: 2, name: 'Bali Serenity', dates: 'Aug 3–10, 2025', emoji: '🌴', days: 8, cities: 2, budget: '₹85k', status: 'planning', progress: 10, friends: 2, mood: 'healing' },
  { id: 3, name: 'Europe Odyssey', dates: 'Sep 18–Oct 2', emoji: '🏰', days: 15, cities: 6, budget: '₹3.5L', status: 'planning', progress: 5, friends: 4, mood: 'adventure' },
  { id: 4, name: 'Rajasthan Road Trip', dates: 'Nov 8–15', emoji: '🏜️', days: 7, cities: 4, budget: '₹45k', status: 'planning', progress: 0, friends: 1, mood: 'solo' },
  { id: 5, name: 'Kerala Backwaters', dates: 'Feb 2025', emoji: '🌊', days: 5, cities: 2, budget: '₹30k', status: 'done', progress: 100, friends: 2, mood: 'romantic' },
  { id: 6, name: 'Kyoto Spiritual', dates: 'Apr 2025', emoji: '⛩️', days: 6, cities: 3, budget: '₹1.8L', status: 'done', progress: 100, friends: 0, mood: 'spiritual' },
];

export const DESTINATIONS = [
  { id: 1, name: 'Tokyo', country: 'Japan', emoji: '🗾', category: 'asia', tags: ['adventure', 'culture'], rating: 4.9, price: '₹1.2L', saved: true },
  { id: 2, name: 'Kyoto', country: 'Japan', emoji: '⛩️', category: 'asia', tags: ['spiritual', 'culture'], rating: 4.8, price: '₹95k', saved: false },
  { id: 3, name: 'Bali', country: 'Indonesia', emoji: '🌴', category: 'asia', tags: ['spiritual', 'healing'], rating: 4.7, price: '₹85k', saved: false },
  { id: 4, name: 'Santorini', country: 'Greece', emoji: '🌅', category: 'europe', tags: ['luxury', 'romantic'], rating: 4.9, price: '₹2.5L', saved: false },
  { id: 5, name: 'Paris', country: 'France', emoji: '🗼', category: 'europe', tags: ['luxury', 'romantic'], rating: 4.8, price: '₹2.2L', saved: false },
  { id: 6, name: 'Patagonia', country: 'Argentina', emoji: '🏔️', category: 'americas', tags: ['adventure'], rating: 4.9, price: '₹3.5L', saved: false },
  { id: 7, name: 'Varanasi', country: 'India', emoji: '🕯️', category: 'asia', tags: ['spiritual'], rating: 4.6, price: '₹15k', saved: false },
  { id: 8, name: 'Amalfi Coast', country: 'Italy', emoji: '🏖️', category: 'europe', tags: ['luxury', 'romantic'], rating: 4.8, price: '₹2.8L', saved: false },
  { id: 9, name: 'Nepal', country: 'Nepal', emoji: '🏔️', category: 'asia', tags: ['adventure', 'spiritual'], rating: 4.7, price: '₹40k', saved: false },
  { id: 10, name: 'Dubai', country: 'UAE', emoji: '✨', category: 'asia', tags: ['luxury', 'party'], rating: 4.5, price: '₹1.5L', saved: false },
  { id: 11, name: 'Prague', country: 'Czech Republic', emoji: '🏰', category: 'europe', tags: ['adventure', 'culture'], rating: 4.7, price: '₹1.8L', saved: false },
  { id: 12, name: 'Rishikesh', country: 'India', emoji: '🧘', category: 'asia', tags: ['spiritual', 'healing'], rating: 4.8, price: '₹12k', saved: false },
  { id: 13, name: 'Maldives', country: 'Maldives', emoji: '🏝️', category: 'asia', tags: ['luxury', 'romantic'], rating: 4.9, price: '₹3L', saved: false },
  { id: 14, name: 'Iceland', country: 'Iceland', emoji: '🌋', category: 'europe', tags: ['adventure'], rating: 4.8, price: '₹4L', saved: false },
  { id: 15, name: 'Goa', country: 'India', emoji: '🏖️', category: 'asia', tags: ['party', 'healing'], rating: 4.4, price: '₹20k', saved: false },
];

export const MEMORIES = [
  {
    id: 1,
    date: 'May 2, 2024 · Kyoto',
    title: 'A ramen shop no map knew about',
    text: 'On the third afternoon in Kyoto, wandering away from the tourist trail, you stumbled into a tiny ramen-ya down an unmarked alley. The owner had been running it for 41 years. No menu — just one bowl, made the old way.',
    icons: ['🍜', '📸', '🌧️'],
    likes: 24,
    mood: 'adventure',
  },
  {
    id: 2,
    date: 'May 5, 2024 · Arashiyama',
    title: 'The bamboo forest at golden hour',
    text: 'Every guidebook says go early. You went at 5:30pm instead. The bamboo turned amber. There were maybe four other people. The light through the stalks felt like breathing inside a cathedral.',
    icons: ['🎋', '🌅', '🦌'],
    likes: 41,
    mood: 'healing',
  },
  {
    id: 3,
    date: 'May 8, 2024 · Osaka',
    title: 'Dotonbori at midnight',
    text: 'The neon reflections on the Dotonbori canal hit different at midnight. Street food in hand, surrounded by three languages at once, you felt the city\'s pulse for the first time.',
    icons: ['🌃', '🦞', '🎆'],
    likes: 18,
    mood: 'party',
  },
  {
    id: 4,
    date: 'May 10, 2024 · Nara',
    title: 'A deer stole your map',
    text: 'Literally. A Nara deer casually pulled the printed map from your hand and ate a corner of it. You navigated by instinct and found the Kasuga Grand Shrine without the map. Better that way.',
    icons: ['🦌', '📄', '⛩️'],
    likes: 87,
    mood: 'solo',
  },
  {
    id: 5,
    date: 'Apr 28, 2024 · Tokyo',
    title: 'First morning in Shinjuku',
    text: 'The alarm went off at 5am but jetlag had you up at 4. You walked through silent Shinjuku streets to a konbini, got an onigiri and canned coffee, and watched the city slowly wake up from a park bench.',
    icons: ['🌅', '🏙️', '☕'],
    likes: 56,
    mood: 'solo',
  },
];

export const PACKING_ITEMS = [
  'Passport & visa', 'Travel insurance docs', 'JR Pass', 'Pocket WiFi',
  'Yen cash (¥30k)', 'Universal adapter', 'Umbrella (always)', 'Walking shoes',
  'Light jacket', 'Sunscreen SPF50', 'Portable charger', 'Translation app',
  'Camera & SD cards', 'Medicines kit', 'Hotel confirmations', 'Neck pillow',
];

export const GLOBE_CITIES = [
  { name: 'Tokyo', lat: 35.6762, lon: 139.6503, color: '#4C7BFF', days: 'Day 1-5' },
  { name: 'Kyoto', lat: 35.0116, lon: 135.7681, color: '#A78BFA', days: 'Day 6-9' },
  { name: 'Osaka', lat: 34.6937, lon: 135.5023, color: '#22D3EE', days: 'Day 10-11' },
  { name: 'Nara', lat: 34.6851, lon: 135.8048, color: '#34D399', days: 'Day 12' },
];

export const ANALYTICS_DATA = {
  spending: [
    { name: 'Week 1', flights: 72000, hotels: 18000, food: 8000, activities: 5000 },
    { name: 'Week 2', flights: 0, hotels: 20000, food: 12000, activities: 15000 },
    { name: 'Week 3', flights: 0, hotels: 17000, food: 10000, activities: 10000 },
  ],
  moodHistory: [
    { day: 'Day 1', energy: 85, stress: 30, happiness: 90 },
    { day: 'Day 2', energy: 78, stress: 25, happiness: 88 },
    { day: 'Day 3', energy: 45, stress: 75, happiness: 60 },
    { day: 'Day 4', energy: 70, stress: 35, happiness: 82 },
    { day: 'Day 5', energy: 80, stress: 20, happiness: 92 },
    { day: 'Day 6', energy: 75, stress: 30, happiness: 85 },
    { day: 'Day 7', energy: 65, stress: 40, happiness: 78 },
  ],
  categoryBreakdown: [
    { name: 'Flights', value: 72000, color: '#4C7BFF' },
    { name: 'Hotels', value: 55000, color: '#9333EA' },
    { name: 'Food', value: 38000, color: '#FB923C' },
    { name: 'Activities', value: 30000, color: '#22D3EE' },
    { name: 'Shopping', value: 19500, color: '#34D399' },
  ],
};

export const COLLAB_MEMBERS = [
  { id: 1, name: 'Rahul K', avatar: 'RK', color: '#4C7BFF' },
  { id: 2, name: 'Priya S', avatar: 'PS', color: '#F472B6' },
  { id: 3, name: 'Amit D', avatar: 'AD', color: '#22D3EE' },
  { id: 4, name: 'Sara M', avatar: 'SM', color: '#34D399' },
];
