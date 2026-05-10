import { NavLink, useNavigate } from 'react-router-dom';
import { motion } from 'framer-motion';
import { useState } from 'react';

const navItems = [
  { path: '/', label: 'Dashboard' },
  { path: '/trips', label: 'My Trips' },
  { path: '/explore', label: 'Explore' },
  { path: '/memories', label: 'Memories' },
  { path: '/analytics', label: 'Analytics' },
  { path: '/room', label: 'Trip Room' },
];

export default function Navbar() {
  const navigate = useNavigate();
  const [showProfile, setShowProfile] = useState(false);

  return (
    <motion.nav
      initial={{ y: -20, opacity: 0 }}
      animate={{ y: 0, opacity: 1 }}
      transition={{ duration: 0.5, ease: 'easeOut' }}
      className="relative z-50 flex items-center justify-between px-6 lg:px-8 py-4"
    >
      {/* Logo */}
      <motion.div
        className="cursor-pointer select-none"
        onClick={() => navigate('/')}
        whileHover={{ scale: 1.02 }}
        whileTap={{ scale: 0.98 }}
      >
        <span className="font-heading text-xl font-extrabold tracking-tight">
          Journey<span className="text-accent">OS</span>
        </span>
      </motion.div>

      {/* Nav tabs */}
      <div className="hidden md:flex items-center gap-1">
        {navItems.map((item) => (
          <NavLink
            key={item.path}
            to={item.path}
            end={item.path === '/'}
            className={({ isActive }) =>
              `px-4 py-2 rounded-full text-sm font-medium transition-all duration-200 border border-transparent
              ${isActive
                ? 'bg-accent-dim border-accent/30 text-accent-light'
                : 'text-text-secondary hover:text-text-primary hover:bg-white/5'
              }`
            }
          >
            {({ isActive }) => (
              <motion.span
                initial={false}
                animate={{ scale: isActive ? 1 : 1 }}
              >
                {item.label}
              </motion.span>
            )}
          </NavLink>
        ))}
      </div>

      {/* Right side */}
      <div className="flex items-center gap-3">
        {/* Notification bell */}
        <motion.button
          whileHover={{ scale: 1.1 }}
          whileTap={{ scale: 0.95 }}
          className="relative w-9 h-9 rounded-full flex items-center justify-center text-text-muted hover:text-text-primary hover:bg-white/5 transition-colors"
        >
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
            <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/>
            <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
          </svg>
          <span className="absolute top-1 right-1.5 w-2 h-2 bg-orange rounded-full animate-blink" />
        </motion.button>

        {/* Avatar */}
        <motion.div
          whileHover={{ scale: 1.05 }}
          whileTap={{ scale: 0.95 }}
          onClick={() => setShowProfile(!showProfile)}
          className="w-9 h-9 rounded-full bg-gradient-to-br from-accent to-purple flex items-center justify-center text-xs font-semibold cursor-pointer select-none"
        >
          RK
        </motion.div>
      </div>

      {/* Mobile nav */}
      <div className="md:hidden fixed bottom-0 left-0 right-0 z-50 flex items-center justify-around py-2 px-2 bg-void/90 backdrop-blur-xl border-t border-border">
        {navItems.slice(0, 5).map((item) => (
          <NavLink
            key={item.path}
            to={item.path}
            end={item.path === '/'}
            className={({ isActive }) =>
              `flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl text-[10px] font-medium transition-all
              ${isActive ? 'text-accent-light bg-accent-dim' : 'text-text-muted'}`
            }
          >
            {item.label}
          </NavLink>
        ))}
      </div>
    </motion.nav>
  );
}
