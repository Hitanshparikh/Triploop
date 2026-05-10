import { motion } from 'framer-motion';
import { AreaChart, Area, BarChart, Bar, PieChart, Pie, Cell, XAxis, YAxis, Tooltip, ResponsiveContainer, Legend } from 'recharts';
import PageTransition from '../components/layout/PageTransition';
import { ANALYTICS_DATA } from '../data/seedData';
import { useMood } from '../context/MoodContext';

const CustomTooltip = ({ active, payload, label }) => {
  if (!active || !payload) return null;
  return (
    <div className="bg-deep/95 border border-border rounded-xl px-3 py-2 text-xs backdrop-blur-lg">
      <p className="text-text-secondary mb-1 font-medium">{label}</p>
      {payload.map((entry, i) => (
        <p key={i} style={{ color: entry.color }} className="flex items-center gap-1.5">
          <span className="w-2 h-2 rounded-full inline-block" style={{ background: entry.color }} />
          {entry.name}: {typeof entry.value === 'number' ? `₹${(entry.value / 1000).toFixed(0)}k` : entry.value}
        </p>
      ))}
    </div>
  );
};

export default function Analytics() {
  const { mood } = useMood();

  return (
    <PageTransition>
      <div className="px-6 lg:px-8 py-6 pb-24 lg:pb-8">
        <h1 className="font-heading text-2xl font-extrabold text-text-primary mb-1">
          Trip Analytics
        </h1>
        <p className="text-sm text-text-secondary mb-6">
          Deep insights into your travel patterns
        </p>

        {/* Stats row */}
        <div className="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
          {[
            { label: 'Total Spent', value: '₹2.14L', sub: '85.6% of budget', color: '#4C7BFF' },
            { label: 'Avg Daily Cost', value: '₹17.8k', sub: 'per person', color: '#9333EA' },
            { label: 'Health Score', value: mood.score, sub: `${mood.name} mode`, color: mood.color },
            { label: 'Trip Score', value: '4.8/5', sub: 'AI predicted', color: '#34D399' },
          ].map((stat, i) => (
            <motion.div
              key={i}
              initial={{ opacity: 0, y: 15 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: i * 0.08 }}
              className="glass-card-static p-4"
            >
              <div className="text-[11px] text-text-muted mb-2">{stat.label}</div>
              <div className="font-heading text-xl font-extrabold" style={{ color: stat.color }}>
                {stat.value}
              </div>
              <div className="text-[10px] text-text-dim mt-1">{stat.sub}</div>
            </motion.div>
          ))}
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-4">
          {/* Spending over time */}
          <motion.div
            initial={{ opacity: 0, y: 15 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: 0.3 }}
            className="glass-card-static p-5"
          >
            <h3 className="text-sm font-medium text-text-secondary mb-4">Spending Breakdown</h3>
            <ResponsiveContainer width="100%" height={220}>
              <BarChart data={ANALYTICS_DATA.spending}>
                <XAxis dataKey="name" tick={{ fill: '#5C667A', fontSize: 11 }} axisLine={false} tickLine={false} />
                <YAxis tick={{ fill: '#5C667A', fontSize: 11 }} axisLine={false} tickLine={false} tickFormatter={v => `₹${v/1000}k`} />
                <Tooltip content={<CustomTooltip />} />
                <Bar dataKey="flights" stackId="a" fill="#4C7BFF" radius={[0, 0, 0, 0]} name="Flights" />
                <Bar dataKey="hotels" stackId="a" fill="#9333EA" name="Hotels" />
                <Bar dataKey="food" stackId="a" fill="#FB923C" name="Food" />
                <Bar dataKey="activities" stackId="a" fill="#22D3EE" radius={[4, 4, 0, 0]} name="Activities" />
              </BarChart>
            </ResponsiveContainer>
          </motion.div>

          {/* Mood/energy over time */}
          <motion.div
            initial={{ opacity: 0, y: 15 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: 0.4 }}
            className="glass-card-static p-5"
          >
            <h3 className="text-sm font-medium text-text-secondary mb-4">Energy & Mood Forecast</h3>
            <ResponsiveContainer width="100%" height={220}>
              <AreaChart data={ANALYTICS_DATA.moodHistory}>
                <defs>
                  <linearGradient id="gradEnergy" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="5%" stopColor="#4C7BFF" stopOpacity={0.3} />
                    <stop offset="95%" stopColor="#4C7BFF" stopOpacity={0} />
                  </linearGradient>
                  <linearGradient id="gradStress" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="5%" stopColor="#FB923C" stopOpacity={0.3} />
                    <stop offset="95%" stopColor="#FB923C" stopOpacity={0} />
                  </linearGradient>
                  <linearGradient id="gradHappy" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="5%" stopColor="#34D399" stopOpacity={0.3} />
                    <stop offset="95%" stopColor="#34D399" stopOpacity={0} />
                  </linearGradient>
                </defs>
                <XAxis dataKey="day" tick={{ fill: '#5C667A', fontSize: 11 }} axisLine={false} tickLine={false} />
                <YAxis tick={{ fill: '#5C667A', fontSize: 11 }} axisLine={false} tickLine={false} domain={[0, 100]} />
                <Tooltip content={<CustomTooltip />} />
                <Area type="monotone" dataKey="energy" stroke="#4C7BFF" fill="url(#gradEnergy)" strokeWidth={2} name="Energy" />
                <Area type="monotone" dataKey="stress" stroke="#FB923C" fill="url(#gradStress)" strokeWidth={2} name="Stress" />
                <Area type="monotone" dataKey="happiness" stroke="#34D399" fill="url(#gradHappy)" strokeWidth={2} name="Happiness" />
              </AreaChart>
            </ResponsiveContainer>
          </motion.div>

          {/* Budget pie */}
          <motion.div
            initial={{ opacity: 0, y: 15 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: 0.5 }}
            className="glass-card-static p-5"
          >
            <h3 className="text-sm font-medium text-text-secondary mb-4">Budget Allocation</h3>
            <div className="flex items-center justify-center">
              <ResponsiveContainer width="100%" height={220}>
                <PieChart>
                  <Pie
                    data={ANALYTICS_DATA.categoryBreakdown}
                    cx="50%"
                    cy="50%"
                    innerRadius={55}
                    outerRadius={85}
                    paddingAngle={3}
                    dataKey="value"
                  >
                    {ANALYTICS_DATA.categoryBreakdown.map((entry, i) => (
                      <Cell key={i} fill={entry.color} stroke="transparent" />
                    ))}
                  </Pie>
                  <Tooltip content={<CustomTooltip />} />
                  <Legend
                    verticalAlign="bottom"
                    height={36}
                    formatter={(value) => <span className="text-xs text-text-secondary">{value}</span>}
                  />
                </PieChart>
              </ResponsiveContainer>
            </div>
          </motion.div>

          {/* AI insights card */}
          <motion.div
            initial={{ opacity: 0, y: 15 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: 0.6 }}
            className="p-5 rounded-2xl border"
            style={{
              background: `linear-gradient(135deg, rgba(${mood.rgb}, 0.08), rgba(147, 51, 234, 0.05))`,
              borderColor: `rgba(${mood.rgb}, 0.15)`,
            }}
          >
            <h3 className="text-sm font-medium text-text-secondary mb-4">AI Trip Insights</h3>
            <div className="space-y-3">
              {[
                { icon: '⚡', title: 'Energy Warning', text: 'Day 3 shows a significant energy dip. Consider rescheduling Fuji to Day 4 for better recovery.' },
                { icon: '💰', title: 'Budget Optimization', text: 'Switching to a shared Airbnb in Kyoto could save ₹12,000 without sacrificing experience quality.' },
                { icon: '🌤️', title: 'Weather Alert', text: 'Light rain expected Day 2 afternoon. Indoor alternatives (teamLab, Akihabara) auto-suggested.' },
                { icon: '🧘', title: 'Wellness Tip', text: 'Your current pace scores 62% exhaustion. Adding one rest morning drops it to 38%.' },
              ].map((insight, i) => (
                <motion.div
                  key={i}
                  initial={{ opacity: 0, x: -10 }}
                  animate={{ opacity: 1, x: 0 }}
                  transition={{ delay: 0.7 + i * 0.1 }}
                  className="flex gap-3 items-start"
                >
                  <span className="text-lg">{insight.icon}</span>
                  <div>
                    <div className="text-xs font-medium text-text-primary">{insight.title}</div>
                    <div className="text-[11px] text-text-secondary leading-relaxed mt-0.5">{insight.text}</div>
                  </div>
                </motion.div>
              ))}
            </div>
          </motion.div>
        </div>
      </div>
    </PageTransition>
  );
}
