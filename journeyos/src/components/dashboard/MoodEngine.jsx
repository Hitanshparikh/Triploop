import { motion } from 'framer-motion';
import { useMood } from '../../context/MoodContext';

export default function MoodEngine() {
  const { currentMood, changeMood, allMoods } = useMood();

  const moodKeys = Object.keys(allMoods);

  return (
    <motion.div
      initial={{ opacity: 0, y: 15 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ duration: 0.5, delay: 0.2 }}
      className="px-6 lg:px-8 pb-5"
    >
      <div className="text-[11px] uppercase tracking-[1.5px] text-text-muted font-medium mb-3">
        Travel Mood Engine
      </div>
      <div className="flex flex-wrap gap-2">
        {moodKeys.map((key) => {
          const m = allMoods[key];
          const isActive = currentMood === key;

          return (
            <motion.button
              key={key}
              onClick={() => changeMood(key)}
              whileHover={{ y: -2, scale: 1.02 }}
              whileTap={{ scale: 0.97 }}
              className={`flex items-center gap-2 px-3.5 py-2 rounded-full text-sm font-medium transition-all duration-300 border cursor-pointer
                ${isActive
                  ? 'text-text-primary shadow-lg'
                  : 'text-text-secondary border-white/8 bg-white/[0.03] hover:bg-white/[0.07] hover:text-text-primary'
                }`}
              style={isActive ? {
                borderColor: m.color + '60',
                background: `rgba(${m.rgb}, 0.12)`,
                boxShadow: `0 0 20px rgba(${m.rgb}, 0.15)`,
              } : {}}
            >
              <span
                className="w-2 h-2 rounded-full shrink-0 transition-all duration-300"
                style={{ background: m.color, boxShadow: isActive ? `0 0 8px ${m.color}` : 'none' }}
              />
              {m.name}
            </motion.button>
          );
        })}
      </div>
    </motion.div>
  );
}
