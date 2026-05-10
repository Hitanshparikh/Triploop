import { useState } from 'react';
import { motion } from 'framer-motion';
import PageTransition from '../components/layout/PageTransition';

const initialProfile = {
  firstName: 'Rahul', lastName: 'Kumar', email: 'rahul@traveloop.com',
  phone: '+91 98765 43210', city: 'Mumbai', country: 'India',
  username: 'rahulk', additionalInfo: 'Passionate traveler. Love adventure and solo healing trips.',
};

export default function UserProfile() {
  const [editing, setEditing] = useState(false);
  const [profile, setProfile] = useState(initialProfile);
  const [draft, setDraft] = useState(initialProfile);

  const set = (k, v) => setDraft(p => ({ ...p, [k]: v }));

  const save = () => { setProfile(draft); setEditing(false); };
  const cancel = () => { setDraft(profile); setEditing(false); };

  const stats = [
    { label: 'Trips', value: 6, color: '#4C7BFF' },
    { label: 'Cities Visited', value: 18, color: '#9333EA' },
    { label: 'Countries', value: 5, color: '#22D3EE' },
    { label: 'Travel Days', value: 53, color: '#34D399' },
  ];

  return (
    <PageTransition>
      <div className="px-6 lg:px-8 py-6 pb-24 lg:pb-8 max-w-3xl mx-auto">
        <h1 className="font-heading text-2xl font-extrabold text-text-primary mb-6">User Profile</h1>

        {/* Profile header */}
        <div className="glass-card-static p-6 mb-4">
          <div className="flex items-start gap-5">
            <div className="relative">
              <div className="w-20 h-20 rounded-full bg-gradient-to-br from-accent to-purple flex items-center justify-center text-2xl font-bold shrink-0">
                RK
              </div>
              {editing && (
                <button className="absolute -bottom-1 -right-1 w-7 h-7 rounded-full bg-accent flex items-center justify-center text-xs cursor-pointer">📷</button>
              )}
            </div>
            <div className="flex-1">
              <h2 className="font-heading text-xl font-bold text-text-primary">
                {profile.firstName} {profile.lastName}
              </h2>
              <p className="text-sm text-text-secondary">@{profile.username}</p>
              <p className="text-xs text-text-dim mt-1">{profile.city}, {profile.country}</p>
              <p className="text-xs text-text-muted mt-2 leading-relaxed">{profile.additionalInfo}</p>
            </div>
            <motion.button
              whileHover={{ scale: 1.05 }}
              whileTap={{ scale: 0.95 }}
              onClick={() => editing ? save() : setEditing(true)}
              className={`px-4 py-2 rounded-xl text-xs font-medium cursor-pointer transition-all ${editing ? 'btn-gradient text-white' : 'bg-white/5 border border-white/10 text-text-secondary hover:text-text-primary'}`}
            >
              {editing ? 'Save Changes' : 'Edit Profile'}
            </motion.button>
          </div>
        </div>

        {/* Stats */}
        <div className="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
          {stats.map((s, i) => (
            <motion.div key={i} initial={{ opacity: 0, y: 15 }} animate={{ opacity: 1, y: 0 }} transition={{ delay: i * 0.08 }} className="glass-card-static p-4 text-center">
              <div className="font-heading text-xl font-extrabold" style={{ color: s.color }}>{s.value}</div>
              <div className="text-[10px] text-text-dim mt-1">{s.label}</div>
            </motion.div>
          ))}
        </div>

        {/* Editable fields */}
        <div className="glass-card-static p-5">
          <h3 className="text-sm font-medium text-text-secondary mb-4">User Details</h3>
          <div className="space-y-3">
            {[
              { label: 'First Name', key: 'firstName' },
              { label: 'Last Name', key: 'lastName' },
              { label: 'Email Address', key: 'email', type: 'email' },
              { label: 'Phone Number', key: 'phone', type: 'tel' },
              { label: 'Username', key: 'username' },
              { label: 'City', key: 'city' },
              { label: 'Country', key: 'country' },
            ].map(field => (
              <div key={field.key} className="flex items-center gap-4">
                <span className="text-xs text-text-muted w-28 shrink-0">{field.label}</span>
                {editing ? (
                  <input
                    type={field.type || 'text'}
                    value={draft[field.key]}
                    onChange={e => set(field.key, e.target.value)}
                    className="flex-1 bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-text-primary text-sm outline-none focus:border-accent/50 transition-colors"
                  />
                ) : (
                  <span className="text-sm text-text-primary">{profile[field.key]}</span>
                )}
              </div>
            ))}

            <div className="flex items-start gap-4">
              <span className="text-xs text-text-muted w-28 shrink-0 pt-2">Additional Info</span>
              {editing ? (
                <textarea value={draft.additionalInfo} onChange={e => set('additionalInfo', e.target.value)} rows={3} className="flex-1 bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-text-primary text-sm outline-none focus:border-accent/50 transition-colors resize-none" />
              ) : (
                <span className="text-sm text-text-primary leading-relaxed">{profile.additionalInfo}</span>
              )}
            </div>
          </div>

          {editing && (
            <div className="flex gap-3 mt-5">
              <motion.button whileHover={{ y: -1 }} onClick={save} className="btn-gradient flex-1 py-2.5 rounded-xl text-sm">Save</motion.button>
              <motion.button whileHover={{ y: -1 }} onClick={cancel} className="flex-1 py-2.5 rounded-xl bg-white/5 border border-white/10 text-sm text-text-secondary cursor-pointer">Cancel</motion.button>
            </div>
          )}
        </div>
      </div>
    </PageTransition>
  );
}
