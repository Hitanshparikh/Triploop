import { useState } from 'react';
import { motion } from 'framer-motion';
import PageTransition from '../components/layout/PageTransition';
import { ITINERARY_DAYS, BUDGET_ITEMS } from '../data/seedData';

const DAY_BUDGETS = [
  { day: 'Day 1', items: [{ name: 'Senso-ji Temple entry', cost: 0 }, { name: 'Imperial Palace tour', cost: 500 }, { name: 'Ichiran Ramen lunch', cost: 1200 }, { name: 'Akihabara shopping', cost: 3000 }], total: 4700 },
  { day: 'Day 2', items: [{ name: 'Meiji Shrine donation', cost: 200 }, { name: 'Takeshita Street', cost: 3000 }, { name: 'Shibuya Sky tickets', cost: 2500 }, { name: 'Tsukiji dinner', cost: 2500 }], total: 8200 },
  { day: 'Day 3', items: [{ name: 'Fuji bus ticket', cost: 3500 }, { name: 'Hakone onsen', cost: 5000 }, { name: 'Shinkansen return', cost: 0 }], total: 8500 },
  { day: 'Day 4', items: [{ name: 'TeamLab tickets', cost: 3200 }, { name: 'Depachika lunch', cost: 1800 }, { name: 'Gyoen entry', cost: 500 }, { name: 'Golden Gai drinks', cost: 2000 }], total: 7500 },
  { day: 'Day 5', items: [{ name: 'Shinkansen to Kyoto', cost: 0 }, { name: 'Fushimi Inari', cost: 0 }, { name: 'Matcha ceremony', cost: 2500 }], total: 2500 },
];

export default function BudgetView() {
  const [viewMode, setViewMode] = useState('day');

  const totalBudget = 250000;
  const totalSpent = 214500;
  const remaining = totalBudget - totalSpent;

  return (
    <PageTransition>
      <div className="px-6 lg:px-8 py-6 pb-24 lg:pb-8 max-w-4xl mx-auto">
        <h1 className="font-heading text-2xl font-extrabold text-text-primary mb-1">Itinerary Budget View</h1>
        <p className="text-sm text-text-secondary mb-5">Trip: Tokyo Expedition · Detailed budget breakdown</p>

        {/* Toggle view */}
        <div className="flex gap-2 mb-5">
          <motion.button whileTap={{ scale: 0.95 }} onClick={() => setViewMode('day')} className={`px-4 py-2 rounded-xl text-xs font-medium cursor-pointer border transition-all ${viewMode === 'day' ? 'bg-accent-dim border-accent/30 text-accent-light' : 'border-white/8 text-text-muted'}`}>by Day</motion.button>
          <motion.button whileTap={{ scale: 0.95 }} onClick={() => setViewMode('stop')} className={`px-4 py-2 rounded-xl text-xs font-medium cursor-pointer border transition-all ${viewMode === 'stop' ? 'bg-accent-dim border-accent/30 text-accent-light' : 'border-white/8 text-text-muted'}`}>by Stop</motion.button>
        </div>

        {/* Summary card */}
        <div className="glass-card-static p-5 mb-4">
          <div className="text-[11px] uppercase tracking-[1.5px] text-text-muted font-medium mb-3">Budget Insights</div>
          <div className="grid grid-cols-3 gap-4">
            <div className="text-center">
              <div className="font-heading text-lg font-extrabold text-accent">₹{(totalBudget / 1000).toFixed(0)}k</div>
              <div className="text-[10px] text-text-dim">Total Budget</div>
            </div>
            <div className="text-center">
              <div className="font-heading text-lg font-extrabold text-orange">₹{(totalSpent / 1000).toFixed(0)}k</div>
              <div className="text-[10px] text-text-dim">Total Spent</div>
            </div>
            <div className="text-center">
              <div className={`font-heading text-lg font-extrabold ${remaining >= 0 ? 'text-green' : 'text-orange'}`}>₹{(remaining / 1000).toFixed(0)}k</div>
              <div className="text-[10px] text-text-dim">Remaining</div>
            </div>
          </div>
          {/* Category bars */}
          <div className="mt-4 space-y-2">
            {BUDGET_ITEMS.map(item => (
              <div key={item.label} className="flex items-center gap-2">
                <span className="text-xs text-text-secondary w-20">{item.label}</span>
                <div className="flex-1 h-1.5 bg-white/7 rounded-full overflow-hidden">
                  <motion.div className="h-full rounded-full" style={{ background: item.color }} initial={{ width: 0 }} animate={{ width: `${item.percent}%` }} transition={{ duration: 0.6 }} />
                </div>
                <span className="text-xs text-text-dim w-10 text-right">{item.amount}</span>
              </div>
            ))}
          </div>
        </div>

        {/* Day/stop breakdown */}
        {viewMode === 'day' ? (
          <div className="space-y-3">
            {DAY_BUDGETS.map((day, i) => (
              <motion.div key={day.day} initial={{ opacity: 0, y: 10 }} animate={{ opacity: 1, y: 0 }} transition={{ delay: i * 0.05 }} className="glass-card-static p-4">
                <div className="flex items-center justify-between mb-3">
                  <h3 className="text-sm font-medium text-text-primary">{day.day}</h3>
                  <span className="text-sm font-heading font-bold text-accent">₹{day.total.toLocaleString()}</span>
                </div>
                {day.items.map((item, j) => (
                  <div key={j} className="flex items-center justify-between py-1.5 border-b border-white/5 last:border-0">
                    <span className="text-xs text-text-secondary">{item.name}</span>
                    <span className="text-xs text-text-dim">{item.cost === 0 ? 'Included' : `₹${item.cost.toLocaleString()}`}</span>
                  </div>
                ))}
              </motion.div>
            ))}
          </div>
        ) : (
          <div className="space-y-3">
            {[
              { stop: 'Tokyo', nights: '5 nights', budget: 12000, items: ['Hotel booking', 'Local transport', 'Activities'] },
              { stop: 'Kyoto', nights: '3 nights', budget: 9000, items: ['Hotel booking', 'Temple passes', 'Tea ceremony'] },
              { stop: 'Osaka', nights: '2 nights', budget: 8000, items: ['Hotel booking', 'Street food tour'] },
              { stop: 'Nara', nights: '1 night', budget: 5000, items: ['Day trip', 'Deer park'] },
            ].map((stop, i) => (
              <motion.div key={stop.stop} initial={{ opacity: 0, y: 10 }} animate={{ opacity: 1, y: 0 }} transition={{ delay: i * 0.05 }} className="glass-card-static p-4">
                <div className="flex items-center justify-between mb-2">
                  <h3 className="text-sm font-medium text-text-primary">{stop.stop}</h3>
                  <span className="text-xs text-text-dim">{stop.nights}</span>
                </div>
                <div className="text-sm font-heading font-bold text-accent mb-2">₹{stop.budget.toLocaleString()}</div>
                {stop.items.map((item, j) => (
                  <div key={j} className="text-xs text-text-secondary py-1">• {item}</div>
                ))}
              </motion.div>
            ))}
          </div>
        )}
      </div>
    </PageTransition>
  );
}
