import { motion } from 'framer-motion';
import { useMood } from '../../context/MoodContext';
import { useGreeting } from '../../hooks/useGreeting';

export default function HeroSection() {
  const { mood } = useMood();
  const greeting = useGreeting();

  return (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ duration: 0.6, delay: 0.1 }}
      className="px-6 lg:px-8 pt-6 pb-4"
    >
      <motion.div
        className="text-xs font-medium tracking-widest uppercase mb-2"
        style={{ color: mood.color }}
        key={mood.key}
        initial={{ opacity: 0, x: -10 }}
        animate={{ opacity: 1, x: 0 }}
        transition={{ duration: 0.3 }}
      >
        {greeting}, Rahul ✦
      </motion.div>

      <h1 className="font-heading text-3xl lg:text-4xl font-extrabold leading-tight tracking-tight">
        Your next{' '}
        <motion.em
          className="not-italic"
          style={{ color: mood.color }}
          key={mood.key + '-em'}
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          transition={{ duration: 0.4 }}
        >
          adventure
        </motion.em>
        <br />
        starts here.
      </h1>

      <motion.p
        className="text-text-secondary text-sm mt-2"
        key={mood.subtitle}
        initial={{ opacity: 0 }}
        animate={{ opacity: 1 }}
        transition={{ duration: 0.4, delay: 0.1 }}
      >
        {mood.subtitle}
      </motion.p>
    </motion.div>
  );
}
