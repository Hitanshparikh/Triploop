import { useState, useMemo } from 'react';
import { motion } from 'framer-motion';
import PageTransition from '../components/layout/PageTransition';
import { DESTINATIONS } from '../data/seedData';

const FILTERS = [
  { key: 'all', label: 'All' },
  { key: 'asia', label: 'Asia' },
  { key: 'europe', label: 'Europe' },
  { key: 'americas', label: 'Americas' },
  { key: 'adventure', label: 'Adventure' },
  { key: 'spiritual', label: 'Spiritual' },
  { key: 'luxury', label: 'Luxury' },
  { key: 'romantic', label: 'Romantic' },
];

export default function Explore() {
  const [search, setSearch] = useState('');
  const [filter, setFilter] = useState('all');
  const [savedSet, setSavedSet] = useState(new Set(DESTINATIONS.filter(d => d.saved).map(d => d.id)));

  const filtered = useMemo(() => {
    return DESTINATIONS.filter(d => {
      const matchFilter = filter === 'all' || d.category === filter || d.tags.includes(filter);
      const matchSearch = !search || d.name.toLowerCase().includes(search.toLowerCase()) || d.country.toLowerCase().includes(search.toLowerCase());
      return matchFilter && matchSearch;
    });
  }, [search, filter]);

  const toggleSave = (id) => {
    setSavedSet(prev => {
      const next = new Set(prev);
      if (next.has(id)) next.delete(id); else next.add(id);
      return next;
    });
  };

  return (
    <PageTransition>
      <div className="px-6 lg:px-8 py-6 pb-24 lg:pb-8">
        <h1 className="font-heading text-2xl font-extrabold text-text-primary mb-5">
          Explore Destinations
        </h1>

        {/* Search */}
        <div className="mb-4">
          <input
            type="text"
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            placeholder="Search cities, countries, experiences..."
            className="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-text-primary text-sm font-body outline-none placeholder:text-text-dim focus:border-accent/50 transition-colors"
          />
        </div>

        {/* Filters */}
        <div className="flex flex-wrap gap-2 mb-5">
          {FILTERS.map(f => (
            <motion.button
              key={f.key}
              onClick={() => setFilter(f.key)}
              whileHover={{ scale: 1.05 }}
              whileTap={{ scale: 0.95 }}
              className={`px-3.5 py-1.5 rounded-full text-xs font-medium border transition-all cursor-pointer
                ${filter === f.key
                  ? 'bg-accent-dim border-accent/30 text-accent-light'
                  : 'border-white/8 text-text-muted bg-white/[0.03] hover:bg-white/[0.07] hover:text-text-secondary'
                }`}
            >
              {f.label}
            </motion.button>
          ))}
        </div>

        {/* Grid */}
        {filtered.length === 0 ? (
          <div className="text-text-dim text-sm py-10 text-center">No destinations match — try a different filter.</div>
        ) : (
          <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
            {filtered.map((dest, i) => {
              const isSaved = savedSet.has(dest.id);
              return (
                <motion.div
                  key={dest.id}
                  initial={{ opacity: 0, y: 15 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ delay: i * 0.03 }}
                  whileHover={{ y: -4, scale: 1.02 }}
                  className={`rounded-2xl overflow-hidden cursor-pointer transition-all border
                    ${isSaved
                      ? 'border-green/40 bg-white/[0.03]'
                      : 'border-white/7 bg-white/[0.03] hover:border-accent/30'
                    }`}
                >
                  <div className="h-24 flex items-center justify-center text-4xl bg-white/[0.02]">
                    {dest.emoji}
                  </div>
                  <div className="p-3">
                    <div className="text-sm font-medium text-text-primary mb-0.5">{dest.name}</div>
                    <div className="text-[11px] text-text-dim mb-2">{dest.country}</div>
                    <div className="flex items-center justify-between">
                      <div className="flex items-center gap-1">
                        <span className="text-xs text-orange">★ {dest.rating}</span>
                        <span className="text-[10px] text-text-dim ml-1">{dest.price}</span>
                      </div>
                      <button
                        onClick={(e) => { e.stopPropagation(); toggleSave(dest.id); }}
                        className={`text-[10px] px-2.5 py-1 rounded-full font-medium transition-all cursor-pointer
                          ${isSaved
                            ? 'bg-green/15 text-green'
                            : 'bg-accent/15 text-accent-light hover:bg-accent/25'
                          }`}
                      >
                        {isSaved ? 'Saved' : '+ Save'}
                      </button>
                    </div>
                  </div>
                </motion.div>
              );
            })}
          </div>
        )}
      </div>
    </PageTransition>
  );
}
