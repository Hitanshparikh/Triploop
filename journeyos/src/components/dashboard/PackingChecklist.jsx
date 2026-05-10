import { useState, useCallback, useMemo } from 'react';
import { motion } from 'framer-motion';

const PACKING_CATEGORIES = {
  'Clothing': ['Casual Shirts', 'Trousers / jeans', 'Comfortable walking shoes', 'light jacket / windbreaker'],
  'Electronics': ['Phone charger', 'Earphone / headphones', 'Universal power adapter'],
  'Documents': ['Passport', 'flight Tickets (printed)', 'hotel booking confirmation', 'Travel insurance'],
};

export default function PackingChecklist() {
  const [checked, setChecked] = useState(new Set());

  const allItems = useMemo(() => {
    const items = [];
    Object.entries(PACKING_CATEGORIES).forEach(([cat, list]) => {
      list.forEach((item, i) => items.push({ id: `${cat}-${i}`, name: item, category: cat }));
    });
    return items;
  }, []);

  const toggle = useCallback((id) => {
    setChecked(prev => { const n = new Set(prev); n.has(id) ? n.delete(id) : n.add(id); return n; });
  }, []);

  const resetAll = () => setChecked(new Set());

  const total = allItems.length;
  const packed = checked.size;
  const pct = Math.round(packed / total * 100);

  return (
    <motion.div initial={{ opacity: 0, y: 15 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.5, delay: 0.5 }} className="glass-card-static p-5">
      <div className="flex items-center justify-between mb-4">
        <div className="flex items-center gap-2 text-sm font-medium text-text-secondary">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" className="text-accent">
            <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/>
          </svg>
          Packing Checklist
        </div>
        <div className="flex gap-2">
          <button onClick={resetAll} className="text-[10px] text-text-dim hover:text-orange cursor-pointer transition-colors">Reset all</button>
          <button className="text-[10px] text-accent-light hover:text-accent cursor-pointer transition-colors">Share Checklist</button>
        </div>
      </div>

      {/* Progress */}
      <div className="flex items-center gap-3 mb-4">
        <div className="flex-1 h-1.5 bg-white/7 rounded-full overflow-hidden">
          <motion.div className="h-full rounded-full bg-gradient-to-r from-green to-cyan" animate={{ width: `${pct}%` }} transition={{ duration: 0.4 }} />
        </div>
        <span className="text-xs font-medium text-green">Progress: {packed}/{total} items packed</span>
      </div>

      {/* Categories */}
      {Object.entries(PACKING_CATEGORIES).map(([category, items]) => (
        <div key={category} className="mb-4 last:mb-0">
          <div className="text-[10px] uppercase tracking-wider text-text-muted font-medium mb-2">{category}</div>
          <div className="grid grid-cols-2 gap-2">
            {items.map((item, i) => {
              const id = `${category}-${i}`;
              const isChecked = checked.has(id);
              return (
                <motion.div key={id} onClick={() => toggle(id)} whileHover={{ scale: 1.02 }} whileTap={{ scale: 0.98 }} className={`flex items-center gap-2 px-3 py-2 rounded-xl cursor-pointer transition-all duration-200 border ${isChecked ? 'border-green/30 bg-green/5' : 'border-white/6 bg-white/[0.03] hover:bg-white/[0.06]'}`}>
                  <div className={`w-4 h-4 rounded shrink-0 flex items-center justify-center text-[10px] transition-all border ${isChecked ? 'bg-green border-green text-white' : 'border-white/20'}`}>{isChecked && '✓'}</div>
                  <span className={`text-xs transition-all ${isChecked ? 'text-text-dim line-through' : 'text-text-secondary'}`}>{item}</span>
                </motion.div>
              );
            })}
          </div>
        </div>
      ))}
    </motion.div>
  );
}
