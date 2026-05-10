import { motion } from 'framer-motion';
import { BUDGET_ITEMS } from '../../data/seedData';

export default function BudgetPlanner() {
  return (
    <motion.div
      initial={{ opacity: 0, y: 15 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ duration: 0.5, delay: 0.6 }}
      className="glass-card-static p-5"
    >
      <div className="flex items-center gap-2 text-sm font-medium text-text-secondary mb-3">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" className="text-accent">
          <path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"/>
          <path d="M3 5v14a2 2 0 0 0 2 2h16v-5"/>
          <path d="M18 12a2 2 0 0 0 0 4h4v-4Z"/>
        </svg>
        Budget Planner
      </div>

      <div className="font-heading text-2xl font-extrabold text-text-primary mb-0.5">
        ₹2,14,500
      </div>
      <div className="text-xs text-text-dim mb-4">
        of ₹2,50,000 · 3 people splitting
      </div>

      <div className="flex flex-col gap-2">
        {BUDGET_ITEMS.map((item, i) => (
          <div key={item.label} className="flex items-center gap-2">
            <span className="text-xs text-text-secondary w-20">{item.label}</span>
            <div className="flex-1 h-1.5 bg-white/7 rounded-full overflow-hidden">
              <motion.div
                className="h-full rounded-full"
                style={{ background: item.color }}
                initial={{ width: 0 }}
                animate={{ width: `${item.percent}%` }}
                transition={{ duration: 0.7, delay: 0.1 * i, ease: 'easeOut' }}
              />
            </div>
            <span className="text-xs text-text-dim w-10 text-right">{item.amount}</span>
          </div>
        ))}
      </div>
    </motion.div>
  );
}
