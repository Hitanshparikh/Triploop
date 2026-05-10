import { motion } from 'framer-motion';
import { useMood } from '../../context/MoodContext';

export default function Background() {
  const { mood } = useMood();

  return (
    <div className="fixed inset-0 pointer-events-none overflow-hidden z-0">
      {/* Grid overlay */}
      <div className="absolute inset-0 bg-grid opacity-100" />

      {/* Animated orbs that respond to mood */}
      <motion.div
        className="absolute rounded-full"
        style={{
          width: 500,
          height: 500,
          top: -150,
          right: -100,
          background: `radial-gradient(circle, ${mood.color}22, transparent 70%)`,
          filter: 'blur(80px)',
        }}
        animate={{
          opacity: [0.12, 0.22, 0.12],
          scale: [1, 1.1, 1],
        }}
        transition={{ duration: 6, repeat: Infinity, ease: 'easeInOut' }}
      />
      <motion.div
        className="absolute rounded-full"
        style={{
          width: 350,
          height: 350,
          bottom: -80,
          left: -60,
          background: `radial-gradient(circle, #9333EA22, transparent 70%)`,
          filter: 'blur(80px)',
        }}
        animate={{
          opacity: [0.12, 0.22, 0.12],
          scale: [1, 1.08, 1],
        }}
        transition={{ duration: 8, repeat: Infinity, ease: 'easeInOut', delay: 2 }}
      />
      <motion.div
        className="absolute rounded-full"
        style={{
          width: 220,
          height: 220,
          top: '45%',
          left: '35%',
          background: `radial-gradient(circle, #06B6D422, transparent 70%)`,
          filter: 'blur(80px)',
        }}
        animate={{
          opacity: [0.1, 0.18, 0.1],
          scale: [1, 1.12, 1],
        }}
        transition={{ duration: 10, repeat: Infinity, ease: 'easeInOut', delay: 4 }}
      />
    </div>
  );
}
