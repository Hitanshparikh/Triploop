import { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { useNavigate } from 'react-router-dom';
import PageTransition from '../components/layout/PageTransition';
import { DESTINATIONS } from '../data/seedData';

const SUGGESTIONS = [
  { name: 'Eiffel Tower Visit', type: 'Landmark', emoji: '🗼' },
  { name: 'Seine River Cruise', type: 'Activity', emoji: '🚢' },
  { name: 'Colosseum Tour', type: 'Landmark', emoji: '🏛️' },
  { name: 'Pasta Making Class', type: 'Activity', emoji: '🍝' },
  { name: 'Local Market Walk', type: 'Activity', emoji: '🛒' },
  { name: 'Sunset Paragliding', type: 'Adventure', emoji: '🪂' },
];

export default function CreateTrip() {
  const navigate = useNavigate();
  const [sections, setSections] = useState([
    { id: 1, place: '', startDate: '', endDate: '', budget: '', activities: [] }
  ]);

  const addSection = () => {
    setSections(prev => [...prev, {
      id: prev.length + 1, place: '', startDate: '', endDate: '', budget: '', activities: []
    }]);
  };

  const updateSection = (idx, key, value) => {
    setSections(prev => prev.map((s, i) => i === idx ? { ...s, [key]: value } : s));
  };

  const removeSection = (idx) => {
    if (sections.length <= 1) return;
    setSections(prev => prev.filter((_, i) => i !== idx));
  };

  const toggleActivity = (sIdx, actName) => {
    setSections(prev => prev.map((s, i) => {
      if (i !== sIdx) return s;
      const has = s.activities.includes(actName);
      return { ...s, activities: has ? s.activities.filter(a => a !== actName) : [...s.activities, actName] };
    }));
  };

  const handleCreate = () => {
    navigate('/trips');
  };

  return (
    <PageTransition>
      <div className="px-6 lg:px-8 py-6 pb-24 lg:pb-8 max-w-4xl mx-auto">
        <div className="flex items-center justify-between mb-6">
          <div>
            <h1 className="font-heading text-2xl font-extrabold text-text-primary mb-1">
              Plan a New Trip
            </h1>
            <p className="text-sm text-text-secondary">Build your perfect multi-city itinerary</p>
          </div>
          <motion.button whileHover={{ scale: 1.05 }} whileTap={{ scale: 0.95 }} onClick={() => navigate(-1)} className="text-sm text-text-muted hover:text-text-primary transition-colors cursor-pointer">
            ← back to My Trips
          </motion.button>
        </div>

        {/* Trip name */}
        <div className="glass-card-static p-5 mb-4">
          <label className="block text-xs text-text-muted mb-1.5 uppercase tracking-wider">Trip Name</label>
          <input type="text" placeholder="Trip to Europe Adventure" className="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-text-primary text-sm outline-none placeholder:text-text-dim focus:border-accent/50 transition-colors" />
        </div>

        {/* Sections */}
        <AnimatePresence>
          {sections.map((section, idx) => (
            <motion.div
              key={section.id}
              initial={{ opacity: 0, y: 20 }}
              animate={{ opacity: 1, y: 0 }}
              exit={{ opacity: 0, y: -20, height: 0 }}
              transition={{ delay: idx * 0.05 }}
              className="glass-card-static p-5 mb-4"
            >
              <div className="flex items-center justify-between mb-4">
                <h3 className="text-sm font-medium text-accent-light">Section {idx + 1}</h3>
                {sections.length > 1 && (
                  <button onClick={() => removeSection(idx)} className="text-xs text-text-dim hover:text-orange transition-colors cursor-pointer">Remove</button>
                )}
              </div>

              <div className="space-y-3">
                <div>
                  <label className="block text-xs text-text-muted mb-1.5">Select a Place</label>
                  <select
                    value={section.place}
                    onChange={e => updateSection(idx, 'place', e.target.value)}
                    className="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-text-primary text-sm outline-none focus:border-accent/50 transition-colors appearance-none cursor-pointer"
                  >
                    <option value="" className="bg-deep">Choose destination...</option>
                    {DESTINATIONS.map(d => (
                      <option key={d.id} value={d.name} className="bg-deep">{d.emoji} {d.name}, {d.country}</option>
                    ))}
                  </select>
                </div>

                <div className="grid grid-cols-2 gap-3">
                  <div>
                    <label className="block text-xs text-text-muted mb-1.5">Start Date</label>
                    <input type="date" value={section.startDate} onChange={e => updateSection(idx, 'startDate', e.target.value)} className="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-text-primary text-sm outline-none focus:border-accent/50 transition-colors" />
                  </div>
                  <div>
                    <label className="block text-xs text-text-muted mb-1.5">End Date</label>
                    <input type="date" value={section.endDate} onChange={e => updateSection(idx, 'endDate', e.target.value)} className="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-text-primary text-sm outline-none focus:border-accent/50 transition-colors" />
                  </div>
                </div>

                <div>
                  <label className="block text-xs text-text-muted mb-1.5">Budget for this Section (₹)</label>
                  <input type="number" value={section.budget} onChange={e => updateSection(idx, 'budget', e.target.value)} placeholder="e.g. 50000" className="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-text-primary text-sm outline-none placeholder:text-text-dim focus:border-accent/50 transition-colors" />
                </div>

                {/* Suggestions */}
                <div>
                  <label className="block text-xs text-text-muted mb-2">Suggestions for Places to Visit / Activities</label>
                  <div className="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    {SUGGESTIONS.map(sug => {
                      const isSelected = section.activities.includes(sug.name);
                      return (
                        <motion.div
                          key={sug.name}
                          whileHover={{ scale: 1.03 }}
                          whileTap={{ scale: 0.97 }}
                          onClick={() => toggleActivity(idx, sug.name)}
                          className={`flex items-center gap-2 px-3 py-2 rounded-xl text-xs cursor-pointer transition-all border ${
                            isSelected ? 'border-accent/40 bg-accent/10 text-accent-light' : 'border-white/7 bg-white/[0.03] text-text-secondary hover:bg-white/5'
                          }`}
                        >
                          <span>{sug.emoji}</span>
                          <div>
                            <div className="font-medium">{sug.name}</div>
                            <div className="text-[10px] text-text-dim">{sug.type}</div>
                          </div>
                        </motion.div>
                      );
                    })}
                  </div>
                </div>
              </div>
            </motion.div>
          ))}
        </AnimatePresence>

        {/* Add section */}
        <motion.button
          whileHover={{ y: -2 }}
          whileTap={{ scale: 0.98 }}
          onClick={addSection}
          className="w-full py-3 rounded-xl border border-dashed border-white/15 text-sm text-text-muted hover:text-accent-light hover:border-accent/30 transition-all cursor-pointer mb-4"
        >
          + Add another Section
        </motion.button>

        {/* Create button */}
        <motion.button
          whileHover={{ y: -2 }}
          whileTap={{ scale: 0.98 }}
          onClick={handleCreate}
          className="btn-gradient w-full py-4 rounded-xl text-sm font-semibold"
        >
          Create Trip →
        </motion.button>
      </div>
    </PageTransition>
  );
}
