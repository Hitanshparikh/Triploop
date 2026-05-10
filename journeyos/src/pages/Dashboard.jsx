import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { motion } from 'framer-motion';
import PageTransition from '../components/layout/PageTransition';
import HeroSection from '../components/dashboard/HeroSection';
import MoodEngine from '../components/dashboard/MoodEngine';
import GlobeVisualization from '../components/dashboard/GlobeVisualization';
import ItineraryBuilder from '../components/dashboard/ItineraryBuilder';
import TripSimulation from '../components/dashboard/TripSimulation';
import BudgetPlanner from '../components/dashboard/BudgetPlanner';
import AICompanion from '../components/dashboard/AICompanion';
import PackingChecklist from '../components/dashboard/PackingChecklist';
import { TRIPS, DESTINATIONS } from '../data/seedData';

const TOP_REGIONS = [
  { name: 'Japan', emoji: '🗾', trips: '12k+', color: '#4C7BFF' },
  { name: 'Europe', emoji: '🏰', trips: '8.5k+', color: '#9333EA' },
  { name: 'Bali', emoji: '🌴', trips: '6.2k+', color: '#22D3EE' },
  { name: 'India', emoji: '🇮🇳', trips: '15k+', color: '#FB923C' },
  { name: 'Maldives', emoji: '🏝️', trips: '3.8k+', color: '#34D399' },
];

const STATUS_STYLES = {
  upcoming: { label: 'Up-coming', bg: 'bg-green/20', text: 'text-green' },
  planning: { label: 'Ongoing', bg: 'bg-accent/20', text: 'text-accent-light' },
  done: { label: 'Completed', bg: 'bg-white/10', text: 'text-text-secondary' },
};

export default function Dashboard() {
  const navigate = useNavigate();
  const [search, setSearch] = useState('');
  const [groupBy, setGroupBy] = useState('all');
  const [sortBy, setSortBy] = useState('date');
  const [filter, setFilter] = useState('all');

  const filteredTrips = TRIPS.filter(t => {
    if (filter !== 'all' && t.status !== filter) return false;
    if (search && !t.name.toLowerCase().includes(search.toLowerCase())) return false;
    return true;
  });

  return (
    <PageTransition>
      <HeroSection />
      <MoodEngine />

      {/* Banner image area */}
      <div className="px-6 lg:px-8 mb-5">
        <motion.div
          initial={{ opacity: 0, y: 10 }}
          animate={{ opacity: 1, y: 0 }}
          className="glass-card-static p-6 relative overflow-hidden"
          style={{ background: 'linear-gradient(135deg, rgba(76,123,255,0.12), rgba(147,51,234,0.08))' }}
        >
          <div className="relative z-10">
            <h2 className="font-heading text-lg font-bold text-text-primary mb-1">Discover your next journey</h2>
            <p className="text-xs text-text-secondary mb-3">AI-powered recommendations tailored to your travel mood</p>
            <motion.button whileHover={{ y: -2 }} whileTap={{ scale: 0.98 }} onClick={() => navigate('/create-trip')} className="btn-gradient px-5 py-2.5 rounded-xl text-sm font-semibold">
              Plan a Trip →
            </motion.button>
          </div>
          <div className="absolute right-6 top-1/2 -translate-y-1/2 text-6xl opacity-30">✈️</div>
        </motion.div>
      </div>

      {/* Search, Group, Filter, Sort */}
      <div className="px-6 lg:px-8 mb-4">
        <input type="text" value={search} onChange={e => setSearch(e.target.value)} placeholder="Search bar ......" className="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-text-primary text-sm outline-none placeholder:text-text-dim focus:border-accent/50 transition-colors mb-3" />
        <div className="flex flex-wrap gap-3">
          <div className="flex items-center gap-2">
            <span className="text-[10px] text-text-dim uppercase tracking-wider">Group by</span>
            <select value={groupBy} onChange={e => setGroupBy(e.target.value)} className="bg-white/5 border border-white/10 rounded-lg px-3 py-1.5 text-xs text-text-secondary outline-none cursor-pointer">
              <option value="all" className="bg-deep">All</option>
              <option value="status" className="bg-deep">Status</option>
              <option value="mood" className="bg-deep">Mood</option>
            </select>
          </div>
          <div className="flex items-center gap-2">
            <span className="text-[10px] text-text-dim uppercase tracking-wider">Filter</span>
            <select value={filter} onChange={e => setFilter(e.target.value)} className="bg-white/5 border border-white/10 rounded-lg px-3 py-1.5 text-xs text-text-secondary outline-none cursor-pointer">
              <option value="all" className="bg-deep">All</option>
              <option value="upcoming" className="bg-deep">Upcoming</option>
              <option value="planning" className="bg-deep">Ongoing</option>
              <option value="done" className="bg-deep">Completed</option>
            </select>
          </div>
          <div className="flex items-center gap-2">
            <span className="text-[10px] text-text-dim uppercase tracking-wider">Sort by</span>
            <select value={sortBy} onChange={e => setSortBy(e.target.value)} className="bg-white/5 border border-white/10 rounded-lg px-3 py-1.5 text-xs text-text-secondary outline-none cursor-pointer">
              <option value="date" className="bg-deep">Date</option>
              <option value="name" className="bg-deep">Name</option>
              <option value="budget" className="bg-deep">Budget</option>
            </select>
          </div>
        </div>
      </div>

      {/* Top Regional Selections */}
      <div className="px-6 lg:px-8 mb-5">
        <div className="text-[11px] uppercase tracking-[1.5px] text-text-muted font-medium mb-3">Top Regional Selections</div>
        <div className="flex gap-3 overflow-x-auto pb-2">
          {TOP_REGIONS.map((region, i) => (
            <motion.div key={region.name} initial={{ opacity: 0, x: 20 }} animate={{ opacity: 1, x: 0 }} transition={{ delay: i * 0.05 }} whileHover={{ y: -3, scale: 1.03 }} className="shrink-0 w-28 glass-card-static p-3 text-center cursor-pointer">
              <div className="text-2xl mb-1">{region.emoji}</div>
              <div className="text-xs font-medium text-text-primary">{region.name}</div>
              <div className="text-[10px] text-text-dim">{region.trips} trips</div>
            </motion.div>
          ))}
        </div>
      </div>

      {/* Previous Trips with status */}
      <div className="px-6 lg:px-8 mb-5">
        <div className="text-[11px] uppercase tracking-[1.5px] text-text-muted font-medium mb-3">Previous Trips</div>
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
          {filteredTrips.slice(0, 3).map((trip, i) => {
            const status = STATUS_STYLES[trip.status];
            return (
              <motion.div key={trip.id} initial={{ opacity: 0, y: 10 }} animate={{ opacity: 1, y: 0 }} transition={{ delay: i * 0.05 }} whileHover={{ y: -2 }} onClick={() => navigate('/trips')} className="glass-card-static p-4 cursor-pointer">
                <div className="flex items-center justify-between mb-2">
                  <span className="text-xl">{trip.emoji}</span>
                  <span className={`text-[10px] px-2.5 py-0.5 rounded-full font-medium ${status.bg} ${status.text}`}>{status.label}</span>
                </div>
                <div className="text-sm font-medium text-text-primary mb-0.5">{trip.name}</div>
                <div className="text-[10px] text-text-dim">{trip.dates} · {trip.cities} cities</div>
                <div className="text-xs text-text-muted mt-1">
                  <span className="font-medium text-text-secondary">{trip.budget}</span> · {trip.friends} friends
                </div>
                {/* Short overview */}
                <div className="mt-2 text-[10px] text-text-dim">
                  {trip.progress}% planned · {trip.days} days
                </div>
              </motion.div>
            );
          })}
        </div>
      </div>

      {/* Main dashboard grid */}
      <div className="grid grid-cols-1 lg:grid-cols-[1fr_340px] gap-4 px-6 lg:px-8 pb-24 lg:pb-8">
        <div className="flex flex-col gap-4">
          <GlobeVisualization />
          <ItineraryBuilder />
          <PackingChecklist />
        </div>
        <div className="flex flex-col gap-4">
          <TripSimulation />
          <BudgetPlanner />
          <AICompanion />
        </div>
      </div>
    </PageTransition>
  );
}
