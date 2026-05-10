import { useState } from 'react';
import { motion } from 'framer-motion';
import PageTransition from '../components/layout/PageTransition';
import { MEMORIES } from '../data/seedData';

export default function Memories() {
  const [liked, setLiked] = useState(new Set());

  const toggleLike = (id) => {
    setLiked(prev => {
      const next = new Set(prev);
      if (next.has(id)) next.delete(id); else next.add(id);
      return next;
    });
  };

  return (
    <PageTransition>
      <div className="px-6 lg:px-8 py-6 pb-24 lg:pb-8">
        <h1 className="font-heading text-2xl font-extrabold text-text-primary mb-1">
          Memory Timeline
        </h1>
        <p className="text-sm text-text-secondary mb-6">
          AI-curated cinematic travel stories
        </p>

        <div className="flex flex-col">
          {MEMORIES.map((memory, i) => {
            const isLiked = liked.has(memory.id);
            return (
              <motion.div
                key={memory.id}
                initial={{ opacity: 0, x: -20 }}
                animate={{ opacity: 1, x: 0 }}
                transition={{ delay: i * 0.1 }}
                className="flex gap-4 pb-6 last:pb-0"
              >
                {/* Timeline line */}
                <div className="flex flex-col items-center shrink-0">
                  <div className="w-3 h-3 rounded-full bg-accent shrink-0 mt-1" />
                  {i < MEMORIES.length - 1 && (
                    <div className="w-px flex-1 bg-accent/20 mt-1" />
                  )}
                </div>

                {/* Content card */}
                <div className="glass-card-static p-4 flex-1">
                  <div className="text-[10px] font-medium text-accent uppercase tracking-wider mb-1">
                    {memory.date}
                  </div>
                  <h3 className="text-sm font-medium text-text-primary mb-2">
                    {memory.title}
                  </h3>
                  <p className="text-xs text-text-secondary leading-relaxed mb-3">
                    {memory.text}
                  </p>

                  {/* Emoji icons */}
                  <div className="flex gap-1.5 mb-3">
                    {memory.icons.map((icon, j) => (
                      <div key={j} className="w-9 h-9 rounded-lg bg-white/5 flex items-center justify-center text-lg">
                        {icon}
                      </div>
                    ))}
                  </div>

                  {/* Like button */}
                  <div className="flex items-center gap-1.5">
                    <motion.button
                      whileTap={{ scale: 1.3 }}
                      onClick={() => toggleLike(memory.id)}
                      className={`text-base cursor-pointer transition-colors ${isLiked ? 'text-orange' : 'text-text-dim hover:text-orange'}`}
                    >
                      ♥
                    </motion.button>
                    <span className="text-xs text-text-dim">
                      {memory.likes + (isLiked ? 1 : 0)}
                    </span>
                  </div>
                </div>
              </motion.div>
            );
          })}
        </div>
      </div>
    </PageTransition>
  );
}
