import { motion } from 'framer-motion';
import { useMood } from '../../context/MoodContext';

const METRIC_LABELS = ['Exhaustion', 'Budget burn', 'Crowd risk', 'Weather risk', 'Travel fatigue'];
const METRIC_COLORS = ['#FB923C', '#4C7BFF', '#22D3EE', '#34D399', '#A78BFA'];

export default function TripSimulation() {
  const { mood } = useMood();
  const metrics = Object.values(mood.metrics);
  const score = mood.score;
  const circumference = 289;
  const offset = Math.round(circumference - (circumference * score / 100));

  return (
    <motion.div
      initial={{ opacity: 0, y: 15 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ duration: 0.5, delay: 0.5 }}
      className="glass-card-static p-5"
    >
      <div className="flex items-center gap-2 text-sm font-medium text-text-secondary mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" className="text-accent">
          <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
        </svg>
        Trip Health Simulation
      </div>

      {/* Score ring */}
      <div className="flex justify-center py-2">
        <svg viewBox="0 0 110 110" width={120} height={120}>
          <circle cx="55" cy="55" r="46" fill="none" stroke="rgba(255,255,255,0.06)" strokeWidth="9" />
          <motion.circle
            cx="55" cy="55" r="46"
            fill="none"
            strokeWidth="9"
            strokeLinecap="round"
            transform="rotate(-90 55 55)"
            strokeDasharray={circumference}
            animate={{
              strokeDashoffset: offset,
              stroke: mood.color,
            }}
            transition={{ duration: 0.8, ease: 'easeOut' }}
          />
          <motion.text
            x="55" y="51"
            textAnchor="middle"
            fill="#E8EAF0"
            fontSize="24"
            fontWeight="700"
            fontFamily="'Space Grotesk', sans-serif"
            key={score}
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
          >
            {score}
          </motion.text>
          <text
            x="55" y="65"
            textAnchor="middle"
            fill="#5C667A"
            fontSize="9"
            fontFamily="'Inter', sans-serif"
          >
            health score
          </text>
        </svg>
      </div>

      {/* Metrics */}
      <div className="flex flex-col gap-2.5 mt-4">
        {METRIC_LABELS.map((label, i) => (
          <div key={label} className="flex items-center gap-2.5">
            <span className="text-xs text-text-secondary w-24 shrink-0">{label}</span>
            <div className="flex-1 h-1 bg-white/7 rounded-full overflow-hidden">
              <motion.div
                className="h-full rounded-full"
                style={{ background: METRIC_COLORS[i] }}
                animate={{ width: `${metrics[i]}%` }}
                transition={{ duration: 0.7, ease: 'easeOut' }}
              />
            </div>
            <motion.span
              className="text-xs text-text-dim w-8 text-right"
              key={metrics[i]}
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
            >
              {metrics[i]}%
            </motion.span>
          </div>
        ))}
      </div>
    </motion.div>
  );
}
