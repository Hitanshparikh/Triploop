import PageTransition from '../components/layout/PageTransition';
import HeroSection from '../components/dashboard/HeroSection';
import MoodEngine from '../components/dashboard/MoodEngine';
import GlobeVisualization from '../components/dashboard/GlobeVisualization';
import ItineraryBuilder from '../components/dashboard/ItineraryBuilder';
import TripSimulation from '../components/dashboard/TripSimulation';
import BudgetPlanner from '../components/dashboard/BudgetPlanner';
import AICompanion from '../components/dashboard/AICompanion';
import PackingChecklist from '../components/dashboard/PackingChecklist';

export default function Dashboard() {
  return (
    <PageTransition>
      <HeroSection />
      <MoodEngine />

      <div className="grid grid-cols-1 lg:grid-cols-[1fr_340px] gap-4 px-6 lg:px-8 pb-24 lg:pb-8">
        {/* Left column */}
        <div className="flex flex-col gap-4">
          <GlobeVisualization />
          <ItineraryBuilder />
          <PackingChecklist />
        </div>

        {/* Right column */}
        <div className="flex flex-col gap-4">
          <TripSimulation />
          <BudgetPlanner />
          <AICompanion />
        </div>
      </div>
    </PageTransition>
  );
}
