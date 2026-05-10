import { useState, useCallback } from 'react';
import { motion } from 'framer-motion';
import { PACKING_ITEMS } from '../../data/seedData';

export default function PackingChecklist() {
  const [checked, setChecked] = useState(new Set());

  const toggle = useCallback((i) => {
    setChecked(prev => {
      const next = new Set(prev);
      if (next.has(i)) next.delete(i); else next.add(i);
      return next;
    });
  }, []);

  const pct = Math.round(checked.size / PACKING_ITEMS.length * 100);

  return (
    <motion.div
      initial={{ opacity: 0, y: 15 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ duration: 0.5, delay: 0.5 }}
      className="glass-card-static p-5"
    >
      <div className="flex items-center gap-2 text-sm font-medium text-text-secondary mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" className="text-accent">
          <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/>
          <path d="M3 6h18"/>
          <path d="M16 10a4 4 0 0 1-8 0"/>
        </svg>
        Packing Checklist — Tokyo Trip
      </div>

      <div className="grid grid-cols-2 gap-2">
        {PACKING_ITEMS.map((item, i) => {
          const isChecked = checked.has(i);
          return (
            <motion.div
              key={i}
              onClick={() => toggle(i)}
              whileHover={{ scale: 1.02 }}
              whileTap={{ scale: 0.98 }}
              className={`flex items-center gap-2 px-3 py-2 rounded-xl cursor-pointer transition-all duration-200 border
                ${isChecked
                  ? 'border-green/30 bg-green/5'
                  : 'border-white/6 bg-white/[0.03] hover:bg-white/[0.06]'
                }`}
            >
              <div className={`w-4 h-4 rounded shrink-0 flex items-center justify-center text-[10px] transition-all border
                ${isChecked
                  ? 'bg-green border-green text-white'
                  : 'border-white/20'
                }`}
              >
                {isChecked && '✓'}
              </div>
              <span className={`text-xs transition-all ${isChecked ? 'text-text-dim line-through' : 'text-text-secondary'}`}>
                {item}
              </span>
            </motion.div>
          );
        })}
      </div>

      {/* Progress bar */}
      <div className="flex items-center gap-3 mt-4">
        <div className="flex-1 h-1.5 bg-white/7 rounded-full overflow-hidden">
          <motion.div
            className="h-full rounded-full bg-gradient-to-r from-green to-cyan"
            animate={{ width: `${pct}%` }}
            transition={{ duration: 0.4 }}
          />
        </div>
        <span className="text-xs font-medium text-green">{pct}% packed</span>
      </div>
    </motion.div>
  );
}
