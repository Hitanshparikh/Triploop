import { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { ITINERARY_DAYS } from '../../data/seedData';

const BADGE_COLORS = {
  blue: 'bg-accent/15 text-accent-light',
  purple: 'bg-purple/15 text-purple-light',
  cyan: 'bg-cyan/15 text-cyan',
  orange: 'bg-orange/15 text-orange',
  green: 'bg-green/15 text-green',
};

export default function ItineraryBuilder() {
  const [activeDay, setActiveDay] = useState(0);

  return (
    <motion.div
      initial={{ opacity: 0, y: 15 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ duration: 0.5, delay: 0.4 }}
      className="glass-card-static p-5"
    >
      <div className="flex items-center gap-2 text-sm font-medium text-text-secondary mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" className="text-accent">
          <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
          <line x1="16" x2="16" y1="2" y2="6"/>
          <line x1="8" x2="8" y1="2" y2="6"/>
          <line x1="3" x2="21" y1="10" y2="10"/>
        </svg>
        Itinerary Builder
      </div>

      {/* Day tabs */}
      <div className="flex gap-1.5 mb-4 flex-wrap">
        {ITINERARY_DAYS.map((_, i) => (
          <motion.button
            key={i}
            onClick={() => setActiveDay(i)}
            whileHover={{ scale: 1.05 }}
            whileTap={{ scale: 0.95 }}
            className={`px-3 py-1.5 rounded-xl text-xs font-medium border transition-all duration-200 cursor-pointer
              ${activeDay === i
                ? 'bg-accent-dim border-accent/30 text-accent-light'
                : 'border-white/7 text-text-dim hover:text-text-secondary hover:bg-white/5'
              }`}
          >
            Day {i + 1}
          </motion.button>
        ))}
      </div>

      {/* Activities */}
      <AnimatePresence mode="wait">
        <motion.div
          key={activeDay}
          initial={{ opacity: 0, x: 10 }}
          animate={{ opacity: 1, x: 0 }}
          exit={{ opacity: 0, x: -10 }}
          transition={{ duration: 0.25 }}
        >
          {ITINERARY_DAYS[activeDay].map((act, i) => (
            <motion.div
              key={i}
              initial={{ opacity: 0, y: 8 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: i * 0.06 }}
              className="flex items-center gap-3 py-2.5 border-b border-white/5 last:border-b-0 group cursor-pointer"
            >
              <span className="text-[11px] text-text-dim w-10 shrink-0 font-medium">
                {act.time}
              </span>
              <div
                className="w-9 h-9 rounded-xl flex items-center justify-center text-base shrink-0 group-hover:scale-110 transition-transform"
                style={{ background: act.bg }}
              >
                {act.emoji}
              </div>
              <div className="min-w-0 flex-1">
                <div className="text-sm font-medium text-text-primary truncate group-hover:text-accent-light transition-colors">
                  {act.name}
                </div>
                <div className="text-[11px] text-text-dim mt-0.5">{act.detail}</div>
              </div>
              <span className={`px-2.5 py-0.5 rounded-full text-[10px] font-medium shrink-0 ${BADGE_COLORS[act.badgeColor] || BADGE_COLORS.blue}`}>
                {act.badge}
              </span>
            </motion.div>
          ))}
        </motion.div>
      </AnimatePresence>
    </motion.div>
  );
}
