import { BrowserRouter, Routes, Route, useLocation } from 'react-router-dom';
import { AnimatePresence } from 'framer-motion';
import { MoodProvider } from './context/MoodContext';
import Background from './components/layout/Background';
import Navbar from './components/layout/Navbar';
import Dashboard from './pages/Dashboard';
import MyTrips from './pages/MyTrips';
import Explore from './pages/Explore';
import Memories from './pages/Memories';
import Analytics from './pages/Analytics';
import TripRoom from './pages/TripRoom';
import Auth from './pages/Auth';

function AppRoutes() {
  const location = useLocation();
  const isAuth = location.pathname === '/auth';

  return (
    <>
      {!isAuth && <Navbar />}
      <AnimatePresence mode="wait">
        <Routes location={location} key={location.pathname}>
          <Route path="/auth" element={<Auth />} />
          <Route path="/" element={<Dashboard />} />
          <Route path="/trips" element={<MyTrips />} />
          <Route path="/explore" element={<Explore />} />
          <Route path="/memories" element={<Memories />} />
          <Route path="/analytics" element={<Analytics />} />
          <Route path="/room" element={<TripRoom />} />
        </Routes>
      </AnimatePresence>
    </>
  );
}

export default function App() {
  return (
    <BrowserRouter>
      <MoodProvider>
        <div className="min-h-screen bg-void relative">
          <Background />
          <div className="relative z-10">
            <AppRoutes />
          </div>
        </div>
      </MoodProvider>
    </BrowserRouter>
  );
}
