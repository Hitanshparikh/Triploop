import { useState } from 'react';
import { motion } from 'framer-motion';
import PageTransition from '../components/layout/PageTransition';

const USERS = [
  { id: 1, name: 'Rahul Kumar', email: 'rahul@traveloop.com', trips: 6, status: 'active', avatar: 'RK', color: '#4C7BFF' },
  { id: 2, name: 'Priya Sharma', email: 'priya@gmail.com', trips: 4, status: 'active', avatar: 'PS', color: '#F472B6' },
  { id: 3, name: 'Amit Desai', email: 'amit@outlook.com', trips: 8, status: 'active', avatar: 'AD', color: '#22D3EE' },
  { id: 4, name: 'Sara Mirza', email: 'sara@gmail.com', trips: 3, status: 'inactive', avatar: 'SM', color: '#34D399' },
  { id: 5, name: 'James Wilson', email: 'james@yahoo.com', trips: 12, status: 'active', avatar: 'JW', color: '#FB923C' },
];

const POPULAR_CITIES = [
  { name: 'Tokyo', visits: 342, trend: '+12%', emoji: '🗾' },
  { name: 'Paris', visits: 298, trend: '+8%', emoji: '🗼' },
  { name: 'Bali', visits: 265, trend: '+22%', emoji: '🌴' },
  { name: 'Dubai', visits: 231, trend: '+5%', emoji: '✨' },
  { name: 'Goa', visits: 189, trend: '+15%', emoji: '🏖️' },
];

const POPULAR_ACTIVITIES = [
  { name: 'City Walking Tours', count: 1240, emoji: '🚶' },
  { name: 'Scuba Diving', count: 856, emoji: '🤿' },
  { name: 'Paragliding', count: 742, emoji: '🪂' },
  { name: 'Temple Visits', count: 698, emoji: '⛩️' },
  { name: 'Food Tours', count: 634, emoji: '🍜' },
];

export default function AdminPanel() {
  const [activeTab, setActiveTab] = useState('users');

  return (
    <PageTransition>
      <div className="px-6 lg:px-8 py-6 pb-24 lg:pb-8">
        <h1 className="font-heading text-2xl font-extrabold text-text-primary mb-1">Admin Panel</h1>
        <p className="text-sm text-text-secondary mb-6">Manage users, view analytics, and monitor platform activity</p>

        {/* Tabs */}
        <div className="flex gap-2 mb-6 flex-wrap">
          {['users', 'cities', 'activities', 'analytics'].map(tab => (
            <motion.button key={tab} whileTap={{ scale: 0.95 }} onClick={() => setActiveTab(tab)} className={`px-4 py-2 rounded-xl text-sm font-medium capitalize cursor-pointer transition-all border ${activeTab === tab ? 'bg-accent-dim border-accent/30 text-accent-light' : 'border-white/8 text-text-muted hover:text-text-secondary'}`}>
              {tab === 'users' ? 'Manage Users' : tab === 'cities' ? 'Popular Cities' : tab === 'activities' ? 'Popular Activities' : 'User Trends & Analytics'}
            </motion.button>
          ))}
        </div>

        {/* Manage Users */}
        {activeTab === 'users' && (
          <div className="glass-card-static p-5">
            <div className="flex items-center justify-between mb-4">
              <h3 className="text-sm font-medium text-text-secondary">Manage Users</h3>
              <span className="text-xs text-text-dim">{USERS.length} users</span>
            </div>
            <p className="text-xs text-text-muted mb-4 leading-relaxed">This section is responsible for managing users and their actions. View all trips made by users and manage account status.</p>
            <div className="space-y-2">
              {USERS.map((user, i) => (
                <motion.div key={user.id} initial={{ opacity: 0, x: -10 }} animate={{ opacity: 1, x: 0 }} transition={{ delay: i * 0.05 }} className="flex items-center gap-3 py-3 px-3 rounded-xl hover:bg-white/[0.03] transition-colors">
                  <div className="w-9 h-9 rounded-full flex items-center justify-center text-xs font-semibold shrink-0" style={{ background: user.color }}>{user.avatar}</div>
                  <div className="flex-1 min-w-0">
                    <div className="text-sm text-text-primary font-medium">{user.name}</div>
                    <div className="text-[10px] text-text-dim truncate">{user.email}</div>
                  </div>
                  <span className="text-xs text-text-muted">{user.trips} trips</span>
                  <span className={`text-[10px] px-2 py-0.5 rounded-full ${user.status === 'active' ? 'bg-green/15 text-green' : 'bg-white/10 text-text-dim'}`}>{user.status}</span>
                  <button className="text-xs text-accent-light hover:text-accent cursor-pointer">View</button>
                </motion.div>
              ))}
            </div>
          </div>
        )}

        {/* Popular Cities */}
        {activeTab === 'cities' && (
          <div className="glass-card-static p-5">
            <h3 className="text-sm font-medium text-text-secondary mb-2">Popular Cities</h3>
            <p className="text-xs text-text-muted mb-4">Cities where users are visiting most based on current trends.</p>
            <div className="space-y-3">
              {POPULAR_CITIES.map((city, i) => (
                <motion.div key={city.name} initial={{ opacity: 0, y: 10 }} animate={{ opacity: 1, y: 0 }} transition={{ delay: i * 0.05 }} className="flex items-center gap-3 py-2">
                  <span className="text-xl w-8">{city.emoji}</span>
                  <div className="flex-1">
                    <div className="text-sm text-text-primary font-medium">{city.name}</div>
                    <div className="h-1.5 bg-white/7 rounded-full overflow-hidden mt-1">
                      <motion.div className="h-full rounded-full bg-accent" initial={{ width: 0 }} animate={{ width: `${(city.visits / 342) * 100}%` }} transition={{ duration: 0.6, delay: i * 0.1 }} />
                    </div>
                  </div>
                  <span className="text-xs text-text-dim">{city.visits} visits</span>
                  <span className="text-xs text-green">{city.trend}</span>
                </motion.div>
              ))}
            </div>
          </div>
        )}

        {/* Popular Activities */}
        {activeTab === 'activities' && (
          <div className="glass-card-static p-5">
            <h3 className="text-sm font-medium text-text-secondary mb-2">Popular Activities</h3>
            <p className="text-xs text-text-muted mb-4">Activities users are doing most based on current trend data.</p>
            <div className="space-y-3">
              {POPULAR_ACTIVITIES.map((act, i) => (
                <motion.div key={act.name} initial={{ opacity: 0, y: 10 }} animate={{ opacity: 1, y: 0 }} transition={{ delay: i * 0.05 }} className="flex items-center gap-3 py-2">
                  <span className="text-xl w-8">{act.emoji}</span>
                  <div className="flex-1">
                    <div className="text-sm text-text-primary">{act.name}</div>
                  </div>
                  <span className="text-xs text-text-secondary">{act.count} users</span>
                </motion.div>
              ))}
            </div>
          </div>
        )}

        {/* Analytics */}
        {activeTab === 'analytics' && (
          <div className="glass-card-static p-5">
            <h3 className="text-sm font-medium text-text-secondary mb-2">User Trends and Analytics</h3>
            <p className="text-xs text-text-muted mb-4">Major focus on providing analysis across various data points and giving useful insights.</p>
            <div className="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
              {[
                { label: 'Total Users', value: '1,247', color: '#4C7BFF' },
                { label: 'Active Trips', value: '89', color: '#22D3EE' },
                { label: 'Avg Trip Duration', value: '8.3 days', color: '#FB923C' },
                { label: 'Avg Budget', value: '₹1.2L', color: '#34D399' },
              ].map((s, i) => (
                <div key={i} className="bg-white/[0.04] rounded-xl p-4 text-center">
                  <div className="font-heading text-xl font-extrabold" style={{ color: s.color }}>{s.value}</div>
                  <div className="text-[10px] text-text-dim mt-1">{s.label}</div>
                </div>
              ))}
            </div>
          </div>
        )}
      </div>
    </PageTransition>
  );
}
