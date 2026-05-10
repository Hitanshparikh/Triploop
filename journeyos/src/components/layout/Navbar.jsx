import { NavLink, useNavigate } from 'react-router-dom';
import { motion } from 'framer-motion';
import { useState } from 'react';

const navItems = [
  { path: '/', label: 'Dashboard' },
  { path: '/trips', label: 'My Trips' },
  { path: '/explore', label: 'Explore' },
  { path: '/community', label: 'Community' },
  { path: '/memories', label: 'Memories' },
];

const moreItems = [
  { path: '/notes', label: 'Trip Notes' },
  { path: '/invoices', label: 'Invoices' },
  { path: '/budget', label: 'Budget' },
  { path: '/analytics', label: 'Analytics' },
  { path: '/room', label: 'Trip Room' },
  { path: '/admin', label: 'Admin' },
];

export default function Navbar() {
  const navigate = useNavigate();
  const [showMore, setShowMore] = useState(false);

  return (
    <motion.nav
      initial={{ y: -20, opacity: 0 }}
      animate={{ y: 0, opacity: 1 }}
      transition={{ duration: 0.5, ease: 'easeOut' }}
      className="relative z-50 flex items-center justify-between px-4 lg:px-8 py-3"
    >
      {/* Logo */}
      <motion.div className="cursor-pointer select-none" onClick={() => navigate('/')} whileHover={{ scale: 1.02 }} whileTap={{ scale: 0.98 }}>
        <span className="font-heading text-xl font-extrabold tracking-tight">
          Traveloop
        </span>
      </motion.div>

      {/* Nav tabs */}
      <div className="hidden lg:flex items-center gap-0.5">
        {navItems.map((item) => (
          <NavLink key={item.path} to={item.path} end={item.path === '/'} className={({ isActive }) => `px-3 py-2 rounded-full text-xs font-medium transition-all duration-200 border border-transparent ${isActive ? 'bg-accent-dim border-accent/30 text-accent-light' : 'text-text-secondary hover:text-text-primary hover:bg-white/5'}`}>
            {item.label}
          </NavLink>
        ))}

        {/* More dropdown */}
        <div className="relative">
          <button onClick={() => setShowMore(!showMore)} className="px-3 py-2 rounded-full text-xs font-medium text-text-secondary hover:text-text-primary hover:bg-white/5 transition-all cursor-pointer">
            More ▾
          </button>
          {showMore && (
            <motion.div initial={{ opacity: 0, y: 5 }} animate={{ opacity: 1, y: 0 }} className="absolute top-full right-0 mt-1 py-1 w-40 bg-deep/95 backdrop-blur-xl border border-border rounded-xl shadow-xl z-50" onMouseLeave={() => setShowMore(false)}>
              {moreItems.map((item) => (
                <NavLink key={item.path} to={item.path} onClick={() => setShowMore(false)} className={({ isActive }) => `block px-4 py-2 text-xs transition-colors ${isActive ? 'text-accent-light bg-accent/10' : 'text-text-secondary hover:text-text-primary hover:bg-white/5'}`}>
                  {item.label}
                </NavLink>
              ))}
            </motion.div>
          )}
        </div>
      </div>

      {/* Right side */}
      <div className="flex items-center gap-3">
        <motion.button whileHover={{ scale: 1.1 }} whileTap={{ scale: 0.95 }} className="relative w-9 h-9 rounded-full flex items-center justify-center text-text-muted hover:text-text-primary hover:bg-white/5 transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
            <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
          </svg>
          <span className="absolute top-1 right-1.5 w-2 h-2 bg-orange rounded-full animate-blink" />
        </motion.button>

        <motion.div whileHover={{ scale: 1.05 }} whileTap={{ scale: 0.95 }} onClick={() => navigate('/profile')} className="w-9 h-9 rounded-full bg-gradient-to-br from-accent to-purple flex items-center justify-center text-xs font-semibold cursor-pointer select-none">
          RK
        </motion.div>
      </div>

      {/* Mobile bottom nav */}
      <div className="lg:hidden fixed bottom-0 left-0 right-0 z-50 flex items-center justify-around py-2 px-1 bg-void/90 backdrop-blur-xl border-t border-border">
        {[...navItems.slice(0, 4), { path: '/profile', label: 'Profile' }].map((item) => (
          <NavLink key={item.path} to={item.path} end={item.path === '/'} className={({ isActive }) => `flex flex-col items-center gap-0.5 px-2 py-1.5 rounded-xl text-[9px] font-medium transition-all ${isActive ? 'text-accent-light bg-accent-dim' : 'text-text-muted'}`}>
            {item.label}
          </NavLink>
        ))}
      </div>
    </motion.nav>
  );
}
