import { createContext, useContext, useState, useCallback } from 'react';
import { MOODS } from '../data/seedData';

const MoodContext = createContext(null);

export function MoodProvider({ children }) {
  const [currentMood, setCurrentMood] = useState('adventure');

  const mood = MOODS[currentMood];

  const changeMood = useCallback((newMood) => {
    if (MOODS[newMood]) {
      setCurrentMood(newMood);
    }
  }, []);

  return (
    <MoodContext.Provider value={{ currentMood, mood, changeMood, allMoods: MOODS }}>
      {children}
    </MoodContext.Provider>
  );
}

export function useMood() {
  const context = useContext(MoodContext);
  if (!context) throw new Error('useMood must be used within MoodProvider');
  return context;
}
