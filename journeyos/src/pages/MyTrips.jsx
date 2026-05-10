import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { motion, AnimatePresence } from 'framer-motion';
import PageTransition from '../components/layout/PageTransition';
import { TRIPS } from '../data/seedData';

const STATUS_TABS = [
  { key: 'all', label: 'All' },
  { key: 'upcoming', label: 'Up-coming' },
  { key: 'planning', label: 'Ongoing' },
  { key: 'done', label: 'Completed' },
];

const STATUS_STYLES = {
  upcoming: { label: 'Up-coming', bg: 'bg-green/20', text: 'text-green' },
  planning: { label: 'Ongoing', bg: 'bg-accent/20', text: 'text-accent-light' },
  done: { label: 'Completed', bg: 'bg-white/10', text: 'text-text-secondary' },
};

export default function MyTrips() {
  const navigate = useNavigate();
  const [statusFilter, setStatusFilter] = useState('all');
  const [selectedId, setSelectedId] = useState(null);

  const filtered = TRIPS.filter(t => statusFilter === 'all' || t.status === statusFilter);
  const selected = TRIPS.find(t => t.id === selectedId);

  return (
    <PageTransition>
      <div className="px-6 lg:px-8 py-6 pb-24 lg:pb-8">
        <div className="flex items-center justify-between mb-6">
          <div>
            <div className="text-[11px] uppercase tracking-[1.5px] text-text-muted font-medium mb-2">My Trips</div>
            <h1 className="font-heading text-2xl font-extrabold text-text-primary">All Journeys</h1>
          </div>
          <motion.button whileHover={{ y: -2 }} whileTap={{ scale: 0.98 }} onClick={() => navigate('/create-trip')} className="btn-gradient px-4 py-2.5 rounded-xl text-sm font-semibold">
            Plan a new trip
          </motion.button>
        </div>

        {/* Status tabs */}
        <div className="flex gap-2 mb-5 flex-wrap">
          {STATUS_TABS.map(tab => (
            <motion.button key={tab.key} whileTap={{ scale: 0.95 }} onClick={() => setStatusFilter(tab.key)} className={`px-4 py-2 rounded-xl text-xs font-medium cursor-pointer transition-all border ${statusFilter === tab.key ? 'bg-accent-dim border-accent/30 text-accent-light' : 'border-white/8 text-text-muted hover:text-text-secondary'}`}>
              {tab.label}
            </motion.button>
          ))}
        </div>

        {/* Preplanned trips section */}
        {statusFilter === 'all' && (
          <div className="mb-5">
            <div className="text-[11px] uppercase tracking-[1.5px] text-text-muted font-medium mb-3">Preplanned Trips</div>
            <div className="flex gap-3 overflow-x-auto pb-2">
              {[
                { name: 'Golden Triangle', emoji: '🕌', days: 7, cities: 3, price: '₹25k' },
                { name: 'Kerala Explorer', emoji: '🌊', days: 5, cities: 3, price: '₹18k' },
                { name: 'Japan Classic', emoji: '🗾', days: 12, cities: 4, price: '₹2L' },
              ].map((trip, i) => (
                <motion.div key={i} whileHover={{ y: -3, scale: 1.02 }} className="shrink-0 w-40 glass-card-static p-3 cursor-pointer">
                  <div className="text-2xl mb-1">{trip.emoji}</div>
                  <div className="text-xs font-medium text-text-primary">{trip.name}</div>
                  <div className="text-[10px] text-text-dim">{trip.days} days · {trip.cities} cities · {trip.price}</div>
                </motion.div>
              ))}
            </div>
          </div>
        )}

        {/* Trip cards */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
          {filtered.map((trip, i) => {
            const status = STATUS_STYLES[trip.status];
            const isSelected = selectedId === trip.id;
            return (
              <motion.div key={trip.id} initial={{ opacity: 0, y: 15 }} animate={{ opacity: 1, y: 0 }} transition={{ delay: i * 0.05 }} whileHover={{ y: -3 }} onClick={() => setSelectedId(isSelected ? null : trip.id)} className={`rounded-2xl overflow-hidden cursor-pointer transition-all border ${isSelected ? 'border-accent bg-accent/8' : 'border-white/7 bg-white/[0.03] hover:border-accent/30'}`}>
                <div className="h-28 flex items-center justify-center text-5xl bg-white/[0.02] relative">
                  {trip.emoji}
                  <span className={`absolute top-2 right-2 px-2.5 py-0.5 rounded-full text-[10px] font-medium ${status.bg} ${status.text}`}>{status.label}</span>
                </div>
                <div className="p-3">
                  <div className="text-sm font-medium text-text-primary mb-0.5">{trip.name}</div>
                  <div className="text-xs text-text-dim mb-1">{trip.dates} · {trip.cities} cities</div>
                  <div className="text-[10px] text-text-muted">created by Rahul · {trip.friends} friends</div>
                  <div className="flex gap-3 mt-2">
                    <span className="text-[11px] text-text-muted"><span className="text-text-secondary font-medium">{trip.days}</span> days</span>
                    <span className="text-[11px] text-text-muted font-medium text-text-secondary">{trip.budget}</span>
                  </div>
                  {/* Short overview */}
                  <div className="mt-2 text-[10px] text-text-dim italic">Short overview of the trip</div>
                </div>
              </motion.div>
            );
          })}
        </div>

        {/* Detail panel */}
        <AnimatePresence>
          {selected && (
            <motion.div initial={{ opacity: 0, y: 20, height: 0 }} animate={{ opacity: 1, y: 0, height: 'auto' }} exit={{ opacity: 0, y: 10, height: 0 }} className="mt-4 overflow-hidden">
              <div className="glass-card-static p-6">
                <div className="flex items-center gap-2 mb-3">
                  <button onClick={() => setSelectedId(null)} className="text-xs text-text-muted hover:text-text-primary cursor-pointer">← back to My Trips</button>
                </div>
                <div className="text-3xl mb-2">{selected.emoji}</div>
                <h2 className="font-heading text-xl font-extrabold text-text-primary mb-1">{selected.name}</h2>
                <p className="text-sm text-text-secondary mb-5">{selected.dates} · {selected.friends} friends synced</p>
                <div className="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
                  {[{ v: selected.days, l: 'Days' }, { v: selected.cities, l: 'Cities' }, { v: selected.budget, l: 'Budget' }, { v: selected.friends, l: 'Friends' }].map((s, i) => (
                    <div key={i} className="bg-white/[0.04] rounded-xl p-3 text-center">
                      <div className="font-heading text-lg font-extrabold text-text-primary">{s.v}</div>
                      <div className="text-[10px] text-text-dim mt-0.5">{s.l}</div>
                    </div>
                  ))}
                </div>
                <div className="text-xs text-text-secondary mb-1">Planning progress</div>
                <div className="h-1.5 bg-white/7 rounded-full overflow-hidden mb-1">
                  <motion.div className="h-full rounded-full bg-gradient-to-r from-accent to-purple" initial={{ width: 0 }} animate={{ width: `${selected.progress}%` }} transition={{ duration: 0.6 }} />
                </div>
                <div className="flex justify-between text-[10px] text-text-dim mb-4">
                  <span>{selected.progress}% complete</span><span>{100 - selected.progress}% remaining</span>
                </div>
                <div className="flex gap-2">
                  <motion.button whileHover={{ y: -2 }} className="btn-gradient flex-1 py-3 rounded-xl text-sm">Open with AI ↗</motion.button>
                  <motion.button whileHover={{ y: -2 }} onClick={() => navigate('/budget')} className="flex-1 py-3 rounded-xl bg-white/5 border border-white/10 text-sm text-text-secondary cursor-pointer">View Full Budget</motion.button>
                </div>
              </div>
            </motion.div>
          )}
        </AnimatePresence>
      </div>
    </PageTransition>
  );
}
