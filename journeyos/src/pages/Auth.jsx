import { useState } from 'react';
import { motion } from 'framer-motion';
import { useNavigate } from 'react-router-dom';

export default function Auth() {
  const [isLogin, setIsLogin] = useState(true);
  const [form, setForm] = useState({
    username: '', password: '', firstName: '', lastName: '',
    email: '', phone: '', city: '', country: '', additionalInfo: '', photo: null,
  });
  const navigate = useNavigate();

  const set = (k, v) => setForm(p => ({ ...p, [k]: v }));

  const handleSubmit = (e) => {
    e.preventDefault();
    navigate('/');
  };

  return (
    <div className="min-h-screen flex items-center justify-center px-4 relative overflow-y-auto py-10">
      <div className="absolute inset-0 bg-grid opacity-100 pointer-events-none" />
      <motion.div className="absolute w-[500px] h-[500px] rounded-full top-[-200px] right-[-100px] opacity-15 pointer-events-none" style={{ background: 'radial-gradient(circle, #4C7BFF, transparent 70%)', filter: 'blur(80px)' }} animate={{ opacity: [0.1, 0.2, 0.1], scale: [1, 1.1, 1] }} transition={{ duration: 6, repeat: Infinity }} />
      <motion.div className="absolute w-[350px] h-[350px] rounded-full bottom-[-100px] left-[-80px] opacity-15 pointer-events-none" style={{ background: 'radial-gradient(circle, #9333EA, transparent 70%)', filter: 'blur(80px)' }} animate={{ opacity: [0.1, 0.2, 0.1] }} transition={{ duration: 8, repeat: Infinity, delay: 2 }} />

      <motion.div initial={{ opacity: 0, y: 30, scale: 0.95 }} animate={{ opacity: 1, y: 0, scale: 1 }} transition={{ duration: 0.6 }} className="relative z-10 w-full max-w-lg">
        <div className="text-center mb-8">
          <motion.div initial={{ opacity: 0, y: -10 }} animate={{ opacity: 1, y: 0 }} transition={{ delay: 0.2 }} className="font-heading text-3xl font-extrabold tracking-tight mb-2">
            Traveloop
          </motion.div>
          <p className="text-text-secondary text-sm">AI-powered emotional travel operating system</p>
        </div>

        <div className="glass-card-static p-6">
          {/* Tabs */}
          <div className="flex gap-1 bg-white/[0.04] rounded-xl p-1 mb-6">
            {['Login', 'Register'].map((tab, i) => (
              <button key={tab} onClick={() => setIsLogin(i === 0)} className={`flex-1 py-2.5 rounded-lg text-sm font-medium transition-all cursor-pointer ${(i === 0 ? isLogin : !isLogin) ? 'bg-accent/15 text-accent-light' : 'text-text-muted hover:text-text-secondary'}`}>{tab}</button>
            ))}
          </div>

          <form onSubmit={handleSubmit} className="space-y-4">
            {isLogin ? (
              <>
                <div>
                  <label className="block text-xs text-text-muted mb-1.5">Username</label>
                  <input type="text" value={form.username} onChange={e => set('username', e.target.value)} placeholder="Enter username" className="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-text-primary text-sm outline-none placeholder:text-text-dim focus:border-accent/50 transition-colors" />
                </div>
                <div>
                  <label className="block text-xs text-text-muted mb-1.5">Password</label>
                  <input type="password" value={form.password} onChange={e => set('password', e.target.value)} placeholder="••••••••" className="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-text-primary text-sm outline-none placeholder:text-text-dim focus:border-accent/50 transition-colors" />
                </div>
                <motion.button whileHover={{ y: -2 }} whileTap={{ scale: 0.98 }} type="submit" className="btn-gradient w-full py-3.5 rounded-xl text-sm">
                  Login →
                </motion.button>
              </>
            ) : (
              <>
                {/* Photo upload */}
                <div className="flex flex-col items-center mb-2">
                  <label className="block text-xs text-text-muted mb-2">Profile Photo</label>
                  <div className="relative">
                    <div className="w-20 h-20 rounded-full bg-white/5 border-2 border-dashed border-white/20 flex items-center justify-center text-2xl cursor-pointer hover:border-accent/50 transition-colors">
                      {form.photo ? '✓' : '📷'}
                    </div>
                    <input type="file" accept="image/*" onChange={e => set('photo', e.target.files[0])} className="absolute inset-0 opacity-0 cursor-pointer" />
                  </div>
                </div>

                <div className="grid grid-cols-2 gap-3">
                  <div>
                    <label className="block text-xs text-text-muted mb-1.5">First Name</label>
                    <input type="text" value={form.firstName} onChange={e => set('firstName', e.target.value)} placeholder="Rahul" className="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-text-primary text-sm outline-none placeholder:text-text-dim focus:border-accent/50 transition-colors" />
                  </div>
                  <div>
                    <label className="block text-xs text-text-muted mb-1.5">Last Name</label>
                    <input type="text" value={form.lastName} onChange={e => set('lastName', e.target.value)} placeholder="Kumar" className="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-text-primary text-sm outline-none placeholder:text-text-dim focus:border-accent/50 transition-colors" />
                  </div>
                </div>

                <div>
                  <label className="block text-xs text-text-muted mb-1.5">Email Address</label>
                  <input type="email" value={form.email} onChange={e => set('email', e.target.value)} placeholder="rahul@traveloop.com" className="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-text-primary text-sm outline-none placeholder:text-text-dim focus:border-accent/50 transition-colors" />
                </div>

                <div>
                  <label className="block text-xs text-text-muted mb-1.5">Phone Number</label>
                  <input type="tel" value={form.phone} onChange={e => set('phone', e.target.value)} placeholder="+91 98765 43210" className="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-text-primary text-sm outline-none placeholder:text-text-dim focus:border-accent/50 transition-colors" />
                </div>

                <div className="grid grid-cols-2 gap-3">
                  <div>
                    <label className="block text-xs text-text-muted mb-1.5">City</label>
                    <input type="text" value={form.city} onChange={e => set('city', e.target.value)} placeholder="Mumbai" className="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-text-primary text-sm outline-none placeholder:text-text-dim focus:border-accent/50 transition-colors" />
                  </div>
                  <div>
                    <label className="block text-xs text-text-muted mb-1.5">Country</label>
                    <input type="text" value={form.country} onChange={e => set('country', e.target.value)} placeholder="India" className="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-text-primary text-sm outline-none placeholder:text-text-dim focus:border-accent/50 transition-colors" />
                  </div>
                </div>

                <div>
                  <label className="block text-xs text-text-muted mb-1.5">Username</label>
                  <input type="text" value={form.username} onChange={e => set('username', e.target.value)} placeholder="Choose a username" className="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-text-primary text-sm outline-none placeholder:text-text-dim focus:border-accent/50 transition-colors" />
                </div>

                <div>
                  <label className="block text-xs text-text-muted mb-1.5">Password</label>
                  <input type="password" value={form.password} onChange={e => set('password', e.target.value)} placeholder="••••••••" className="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-text-primary text-sm outline-none placeholder:text-text-dim focus:border-accent/50 transition-colors" />
                </div>

                <div>
                  <label className="block text-xs text-text-muted mb-1.5">Additional Information</label>
                  <textarea value={form.additionalInfo} onChange={e => set('additionalInfo', e.target.value)} placeholder="Tell us about your travel preferences..." rows={3} className="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-text-primary text-sm outline-none placeholder:text-text-dim focus:border-accent/50 transition-colors resize-none" />
                </div>

                <motion.button whileHover={{ y: -2 }} whileTap={{ scale: 0.98 }} type="submit" className="btn-gradient w-full py-3.5 rounded-xl text-sm">
                  Register →
                </motion.button>
              </>
            )}
          </form>

          <div className="flex items-center gap-3 my-5">
            <div className="flex-1 h-px bg-white/7" />
            <span className="text-[10px] text-text-dim uppercase tracking-wider">or continue with</span>
            <div className="flex-1 h-px bg-white/7" />
          </div>

          <div className="flex gap-3">
            {['Google', 'GitHub'].map(provider => (
              <motion.button key={provider} whileHover={{ y: -2 }} whileTap={{ scale: 0.98 }} onClick={() => navigate('/')} className="flex-1 py-2.5 rounded-xl bg-white/5 border border-white/10 text-sm text-text-secondary hover:bg-white/8 hover:text-text-primary transition-all cursor-pointer">
                {provider}
              </motion.button>
            ))}
          </div>
        </div>
      </motion.div>
    </div>
  );
}
