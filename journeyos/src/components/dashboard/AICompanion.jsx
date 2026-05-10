import { motion } from 'framer-motion';
import { useMood } from '../../context/MoodContext';

export default function AICompanion() {
  const { mood } = useMood();

  return (
    <motion.div
      initial={{ opacity: 0, y: 15 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ duration: 0.5, delay: 0.7 }}
      className="p-5 rounded-2xl border"
      style={{
        background: `linear-gradient(135deg, rgba(${mood.rgb}, 0.1), rgba(147, 51, 234, 0.07))`,
        borderColor: `rgba(${mood.rgb}, 0.2)`,
      }}
    >
      <div className="flex items-center gap-2 mb-3">
        <span
          className="w-2 h-2 rounded-full animate-blink"
          style={{ background: mood.color }}
        />
        <span className="text-xs font-medium" style={{ color: mood.color }}>
          AI Travel Companion
        </span>
      </div>

      <motion.div
        key={mood.key}
        initial={{ opacity: 0 }}
        animate={{ opacity: 1 }}
        transition={{ duration: 0.4 }}
        className="text-sm text-text-secondary leading-relaxed"
        dangerouslySetInnerHTML={{ __html: mood.ai }}
      />

      <motion.button
        whileHover={{ y: -2, boxShadow: `0 8px 30px rgba(${mood.rgb}, 0.35)` }}
        whileTap={{ scale: 0.98 }}
        className="w-full mt-4 py-3 rounded-xl font-semibold text-sm text-white cursor-pointer transition-all"
        style={{
          background: `linear-gradient(135deg, ${mood.color}, #7C3AED)`,
        }}
      >
        Ask AI to optimize trip ↗
      </motion.button>
    </motion.div>
  );
}
