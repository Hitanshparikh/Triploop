import { useState, useMemo } from 'react';
import { motion } from 'framer-motion';
import PageTransition from '../components/layout/PageTransition';

const POSTS = [
  { id: 1, user: 'James K', avatar: 'JK', city: 'Paris', title: 'Hidden gems in Montmartre', text: 'Skipped the usual tourist spots and found an amazing bakery tucked behind Sacré-Cœur. The croissants were life-changing. Also, the view from Rue Cortot at sunset is unmatched.', likes: 42, comments: 8, mood: 'adventure', date: 'May 8, 2025', color: '#4C7BFF' },
  { id: 2, user: 'Priya S', avatar: 'PS', city: 'Bali', title: 'Ubud rice terraces at dawn', text: 'Woke up at 4:30am and it was absolutely worth it. No crowds, golden light, and the sound of water flowing through the paddies. Tegallalang is overrated — go to Jatiluwih instead.', likes: 87, comments: 15, mood: 'healing', date: 'Apr 22, 2025', color: '#818CF8' },
  { id: 3, user: 'Amit D', avatar: 'AD', city: 'Tokyo', title: 'Best ramen in Shinjuku', text: 'After trying 12 ramen shops, Fu-unji near Shinjuku station wins. The tsukemen (dipping noodles) is on another level. Go at 11am to avoid the 45-min queue.', likes: 63, comments: 22, mood: 'adventure', date: 'Apr 15, 2025', color: '#FB923C' },
  { id: 4, user: 'Sara M', avatar: 'SM', city: 'Rishikesh', title: 'Meditation retreat experience', text: 'Spent 5 days at Parmarth Niketan ashram. No phone, no agenda, just river sounds and meditation. Changed my perspective on travel completely. Not every trip needs 20 activities.', likes: 104, comments: 31, mood: 'spiritual', date: 'Mar 30, 2025', color: '#A78BFA' },
  { id: 5, user: 'Rahul K', avatar: 'RK', city: 'Kerala', title: 'Backwater houseboat tips', text: 'Book directly at Alleppey dock, not online — you save 40%. The overnight houseboats from Alleppey to Kumarakom are magical. Fresh fish cooked on board is the highlight.', likes: 56, comments: 12, mood: 'romantic', date: 'Feb 14, 2025', color: '#F472B6' },
];

const SORT_OPTIONS = ['Most Recent', 'Most Liked', 'Most Comments'];
const GROUP_OPTIONS = ['All', 'By City', 'By Mood'];
const FILTER_OPTIONS = ['All', 'Adventure', 'Healing', 'Spiritual', 'Romantic', 'Luxury'];

export default function Community() {
  const [search, setSearch] = useState('');
  const [sortBy, setSortBy] = useState('Most Recent');
  const [groupBy, setGroupBy] = useState('All');
  const [filter, setFilter] = useState('All');
  const [liked, setLiked] = useState(new Set());

  const filtered = useMemo(() => {
    let result = [...POSTS];
    if (search) result = result.filter(p => p.title.toLowerCase().includes(search.toLowerCase()) || p.city.toLowerCase().includes(search.toLowerCase()) || p.text.toLowerCase().includes(search.toLowerCase()));
    if (filter !== 'All') result = result.filter(p => p.mood === filter.toLowerCase());
    if (sortBy === 'Most Liked') result.sort((a, b) => b.likes - a.likes);
    if (sortBy === 'Most Comments') result.sort((a, b) => b.comments - a.comments);
    return result;
  }, [search, sortBy, filter]);

  const toggleLike = id => setLiked(prev => { const n = new Set(prev); n.has(id) ? n.delete(id) : n.add(id); return n; });

  return (
    <PageTransition>
      <div className="px-6 lg:px-8 py-6 pb-24 lg:pb-8">
        <h1 className="font-heading text-2xl font-extrabold text-text-primary mb-1">Community</h1>
        <p className="text-sm text-text-secondary mb-5">Share and discover travel experiences from the community</p>

        {/* Search */}
        <input type="text" value={search} onChange={e => setSearch(e.target.value)} placeholder="Search trips, cities, experiences..." className="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-text-primary text-sm outline-none placeholder:text-text-dim focus:border-accent/50 transition-colors mb-4" />

        {/* Controls */}
        <div className="flex flex-wrap gap-3 mb-5">
          <div className="flex items-center gap-2">
            <span className="text-[10px] text-text-dim uppercase tracking-wider">Group by</span>
            <select value={groupBy} onChange={e => setGroupBy(e.target.value)} className="bg-white/5 border border-white/10 rounded-lg px-3 py-1.5 text-xs text-text-secondary outline-none cursor-pointer">
              {GROUP_OPTIONS.map(o => <option key={o} value={o} className="bg-deep">{o}</option>)}
            </select>
          </div>
          <div className="flex items-center gap-2">
            <span className="text-[10px] text-text-dim uppercase tracking-wider">Filter</span>
            <select value={filter} onChange={e => setFilter(e.target.value)} className="bg-white/5 border border-white/10 rounded-lg px-3 py-1.5 text-xs text-text-secondary outline-none cursor-pointer">
              {FILTER_OPTIONS.map(o => <option key={o} value={o} className="bg-deep">{o}</option>)}
            </select>
          </div>
          <div className="flex items-center gap-2">
            <span className="text-[10px] text-text-dim uppercase tracking-wider">Sort by</span>
            <select value={sortBy} onChange={e => setSortBy(e.target.value)} className="bg-white/5 border border-white/10 rounded-lg px-3 py-1.5 text-xs text-text-secondary outline-none cursor-pointer">
              {SORT_OPTIONS.map(o => <option key={o} value={o} className="bg-deep">{o}</option>)}
            </select>
          </div>
        </div>

        {/* Posts */}
        <div className="space-y-4">
          {filtered.length === 0 && <div className="text-text-dim text-sm text-center py-10">No posts match your search.</div>}
          {filtered.map((post, i) => (
            <motion.div key={post.id} initial={{ opacity: 0, y: 15 }} animate={{ opacity: 1, y: 0 }} transition={{ delay: i * 0.05 }} className="glass-card-static p-5 hover:border-accent/20 transition-colors">
              <div className="flex items-center gap-3 mb-3">
                <div className="w-9 h-9 rounded-full flex items-center justify-center text-xs font-semibold shrink-0" style={{ background: post.color }}>{post.avatar}</div>
                <div className="flex-1">
                  <div className="text-sm font-medium text-text-primary">{post.user}</div>
                  <div className="text-[10px] text-text-dim">{post.date} · {post.city}</div>
                </div>
                <span className="text-[10px] px-2.5 py-1 rounded-full bg-white/5 text-text-muted capitalize">{post.mood}</span>
              </div>
              <h3 className="text-sm font-semibold text-text-primary mb-2">{post.title}</h3>
              <p className="text-xs text-text-secondary leading-relaxed mb-3">{post.text}</p>
              <div className="flex items-center gap-4">
                <motion.button whileTap={{ scale: 1.2 }} onClick={() => toggleLike(post.id)} className={`flex items-center gap-1.5 text-xs cursor-pointer transition-colors ${liked.has(post.id) ? 'text-orange' : 'text-text-dim hover:text-orange'}`}>
                  ♥ {post.likes + (liked.has(post.id) ? 1 : 0)}
                </motion.button>
                <span className="text-xs text-text-dim">💬 {post.comments}</span>
              </div>
            </motion.div>
          ))}
        </div>
      </div>
    </PageTransition>
  );
}
