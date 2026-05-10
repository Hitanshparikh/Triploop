import { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import PageTransition from '../components/layout/PageTransition';

const INITIAL_NOTES = [
  { id: 1, trip: 'Tokyo Expedition', date: 'Jun 13, 2025', title: 'Hotel check-in details - Tokyo', text: 'check in after 2pm, room 302, breakfast included (7-10am)\nDay 3: June 14 2025', category: 'hotel' },
  { id: 2, trip: 'Paris & Rome Adventure', date: 'May 15, 2025', title: 'Hotel Check-in details - Rome stop', text: 'Check-in at Hotel Roma Palazzo, Via Nazionale 243. Confirmation #HR-89201. Late check-in arranged until midnight.', category: 'hotel' },
  { id: 3, trip: 'Tokyo Expedition', date: 'Jun 12, 2025', title: 'Flight booking confirmation', text: 'flight bookings (DEL -> NRT)\nAir India AI-306, Departure 11:30pm, Terminal 3\nLanding NRT 12:15pm next day', category: 'travel' },
  { id: 4, trip: 'Paris & Rome Adventure', date: 'May 14, 2025', title: 'Packing reminder', text: 'Don\'t forget universal power adapter, travel insurance docs, and printed hotel confirmations. Also bring comfortable walking shoes for Colosseum tour.', category: 'general' },
];

export default function TripNotes() {
  const [notes, setNotes] = useState(INITIAL_NOTES);
  const [addingNote, setAddingNote] = useState(false);
  const [newNote, setNewNote] = useState({ title: '', text: '', trip: 'Tokyo Expedition', category: 'general' });
  const [editingId, setEditingId] = useState(null);

  const addNote = () => {
    if (!newNote.title.trim()) return;
    setNotes(prev => [{ id: Date.now(), date: new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }), ...newNote }, ...prev]);
    setNewNote({ title: '', text: '', trip: 'Tokyo Expedition', category: 'general' });
    setAddingNote(false);
  };

  const deleteNote = (id) => setNotes(prev => prev.filter(n => n.id !== id));

  const categoryColors = { hotel: 'bg-purple/15 text-purple-light', travel: 'bg-accent/15 text-accent-light', general: 'bg-white/10 text-text-secondary' };

  return (
    <PageTransition>
      <div className="px-6 lg:px-8 py-6 pb-24 lg:pb-8 max-w-3xl mx-auto">
        <div className="flex items-center justify-between mb-6">
          <div>
            <h1 className="font-heading text-2xl font-extrabold text-text-primary mb-1">Trip Notes</h1>
            <p className="text-sm text-text-secondary">Journal and notes for your trips</p>
          </div>
          <motion.button whileHover={{ y: -2 }} whileTap={{ scale: 0.98 }} onClick={() => setAddingNote(!addingNote)} className="btn-gradient px-4 py-2 rounded-xl text-xs font-medium">
            + Add Note
          </motion.button>
        </div>

        {/* Add note form */}
        <AnimatePresence>
          {addingNote && (
            <motion.div initial={{ opacity: 0, height: 0 }} animate={{ opacity: 1, height: 'auto' }} exit={{ opacity: 0, height: 0 }} className="overflow-hidden mb-4">
              <div className="glass-card-static p-5 space-y-3">
                <div className="grid grid-cols-2 gap-3">
                  <div>
                    <label className="block text-xs text-text-muted mb-1.5">Trip</label>
                    <select value={newNote.trip} onChange={e => setNewNote(p => ({ ...p, trip: e.target.value }))} className="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-text-primary text-sm outline-none cursor-pointer">
                      <option value="Tokyo Expedition" className="bg-deep">Tokyo Expedition</option>
                      <option value="Paris & Rome Adventure" className="bg-deep">Paris & Rome Adventure</option>
                      <option value="Bali Serenity" className="bg-deep">Bali Serenity</option>
                    </select>
                  </div>
                  <div>
                    <label className="block text-xs text-text-muted mb-1.5">Category</label>
                    <select value={newNote.category} onChange={e => setNewNote(p => ({ ...p, category: e.target.value }))} className="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-text-primary text-sm outline-none cursor-pointer">
                      <option value="general" className="bg-deep">General</option>
                      <option value="hotel" className="bg-deep">Hotel</option>
                      <option value="travel" className="bg-deep">Travel</option>
                    </select>
                  </div>
                </div>
                <div>
                  <label className="block text-xs text-text-muted mb-1.5">Title</label>
                  <input type="text" value={newNote.title} onChange={e => setNewNote(p => ({ ...p, title: e.target.value }))} placeholder="Note title..." className="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-text-primary text-sm outline-none placeholder:text-text-dim focus:border-accent/50" />
                </div>
                <div>
                  <label className="block text-xs text-text-muted mb-1.5">Content</label>
                  <textarea value={newNote.text} onChange={e => setNewNote(p => ({ ...p, text: e.target.value }))} rows={4} placeholder="Write your note..." className="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-text-primary text-sm outline-none placeholder:text-text-dim focus:border-accent/50 resize-none" />
                </div>
                <div className="flex gap-2">
                  <motion.button whileTap={{ scale: 0.98 }} onClick={addNote} className="btn-gradient px-5 py-2 rounded-xl text-xs">Save Note</motion.button>
                  <motion.button whileTap={{ scale: 0.98 }} onClick={() => setAddingNote(false)} className="px-5 py-2 rounded-xl bg-white/5 border border-white/10 text-xs text-text-secondary cursor-pointer">Cancel</motion.button>
                </div>
              </div>
            </motion.div>
          )}
        </AnimatePresence>

        {/* Notes list */}
        <div className="space-y-3">
          {notes.map((note, i) => (
            <motion.div key={note.id} initial={{ opacity: 0, y: 10 }} animate={{ opacity: 1, y: 0 }} transition={{ delay: i * 0.05 }} className="glass-card-static p-4 hover:border-accent/20 transition-colors">
              <div className="flex items-center justify-between mb-2">
                <div className="flex items-center gap-2">
                  <span className={`text-[10px] px-2 py-0.5 rounded-full ${categoryColors[note.category]}`}>{note.category}</span>
                  <span className="text-[10px] text-text-dim">{note.trip}</span>
                </div>
                <div className="flex items-center gap-2">
                  <span className="text-[10px] text-text-dim">{note.date}</span>
                  <button onClick={() => deleteNote(note.id)} className="text-[10px] text-text-dim hover:text-orange cursor-pointer">✕</button>
                </div>
              </div>
              <h3 className="text-sm font-medium text-text-primary mb-1">{note.title}</h3>
              <p className="text-xs text-text-secondary leading-relaxed whitespace-pre-line">{note.text}</p>
            </motion.div>
          ))}
        </div>
      </div>
    </PageTransition>
  );
}
