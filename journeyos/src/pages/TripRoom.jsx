import { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import PageTransition from '../components/layout/PageTransition';
import { COLLAB_MEMBERS } from '../data/seedData';

const CHAT_MESSAGES = [
  { user: 'PS', text: 'Can we swap Akihabara to Day 4?', time: '2:15 PM', reactions: { '❤️': 2 } },
  { user: 'AD', text: 'The Fuji day trip looks exhausting — add recovery time?', time: '2:18 PM', reactions: { '👍': 3 } },
  { user: 'RK', text: 'AI flagged Day 3 as high fatigue. Running simulation again.', time: '2:20 PM', reactions: { '🔥': 2 } },
  { user: 'SM', text: 'Ryokan onsen on Day 6 is non-negotiable 🧖‍♀️', time: '2:22 PM', reactions: { '❤️': 4 } },
];

const VOTE_ITEMS = [
  { activity: 'TeamLab Planets', votes: { up: 4, down: 0 }, userVoted: 'up' },
  { activity: 'Golden Gai Night', votes: { up: 3, down: 1 }, userVoted: null },
  { activity: 'Fuji Day Trip', votes: { up: 2, down: 2 }, userVoted: null },
  { activity: 'Tsukiji Market', votes: { up: 4, down: 0 }, userVoted: 'up' },
];

export default function TripRoom() {
  const [messages, setMessages] = useState(CHAT_MESSAGES);
  const [newMsg, setNewMsg] = useState('');
  const [votes, setVotes] = useState(VOTE_ITEMS);

  const sendMessage = () => {
    if (!newMsg.trim()) return;
    setMessages(prev => [...prev, { user: 'RK', text: newMsg, time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }), reactions: {} }]);
    setNewMsg('');
  };

  const castVote = (idx, dir) => {
    setVotes(prev => prev.map((v, i) => {
      if (i !== idx) return v;
      const was = v.userVoted;
      if (was === dir) return { ...v, userVoted: null, votes: { ...v.votes, [dir]: v.votes[dir] - 1 } };
      const nv = { ...v.votes };
      if (was) nv[was]--;
      nv[dir]++;
      return { ...v, userVoted: dir, votes: nv };
    }));
  };

  return (
    <PageTransition>
      <div className="px-6 lg:px-8 py-6 pb-24 lg:pb-8">
        <div className="flex items-center justify-between mb-6">
          <div>
            <h1 className="font-heading text-2xl font-extrabold text-text-primary mb-1">Tokyo Expedition Room</h1>
            <p className="text-sm text-text-secondary">Collaborative planning · 4 members online</p>
          </div>
          <div className="flex -space-x-2">
            {COLLAB_MEMBERS.map(m => (
              <div key={m.id} className="w-8 h-8 rounded-full flex items-center justify-center text-xs font-semibold border-2 border-void" style={{ background: m.color }} title={m.name}>{m.avatar}</div>
            ))}
          </div>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-4">
          {/* Chat */}
          <div className="glass-card-static p-5 flex flex-col" style={{ minHeight: 500 }}>
            <div className="flex items-center gap-2 text-sm font-medium text-text-secondary mb-4">
              <span className="w-2 h-2 rounded-full bg-green animate-blink" />Live Chat
            </div>
            <div className="flex-1 overflow-y-auto space-y-3 mb-4">
              {messages.map((msg, i) => {
                const member = COLLAB_MEMBERS.find(m => m.avatar === msg.user) || { color: '#4C7BFF' };
                const isMe = msg.user === 'RK';
                return (
                  <motion.div key={i} initial={{ opacity: 0, y: 10 }} animate={{ opacity: 1, y: 0 }} transition={{ delay: i * 0.05 }} className={`flex gap-3 ${isMe ? 'flex-row-reverse' : ''}`}>
                    <div className="w-7 h-7 rounded-full flex items-center justify-center text-[10px] font-semibold shrink-0" style={{ background: member.color }}>{msg.user}</div>
                    <div className={`max-w-[75%] ${isMe ? 'text-right' : ''}`}>
                      <div className={`inline-block px-3 py-2 rounded-2xl text-sm ${isMe ? 'bg-accent/15 text-accent-light' : 'bg-white/5 text-text-primary'}`}>{msg.text}</div>
                      <div className="flex items-center gap-2 mt-1">
                        <span className="text-[10px] text-text-dim">{msg.time}</span>
                        {Object.entries(msg.reactions).map(([emoji, count]) => (<span key={emoji} className="text-[10px] bg-white/5 px-1.5 py-0.5 rounded-full">{emoji} {count}</span>))}
                      </div>
                    </div>
                  </motion.div>
                );
              })}
            </div>
            <div className="flex gap-2">
              <input type="text" value={newMsg} onChange={e => setNewMsg(e.target.value)} onKeyDown={e => e.key === 'Enter' && sendMessage()} placeholder="Type a message..." className="flex-1 bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-text-primary outline-none placeholder:text-text-dim focus:border-accent/50" />
              <motion.button whileHover={{ scale: 1.05 }} whileTap={{ scale: 0.95 }} onClick={sendMessage} className="btn-gradient px-5 py-2.5 rounded-xl text-sm">Send</motion.button>
            </div>
          </div>

          {/* Sidebar */}
          <div className="flex flex-col gap-4">
            <div className="glass-card-static p-5">
              <h3 className="text-sm font-medium text-text-secondary mb-4">Vote on Activities</h3>
              <div className="space-y-3">
                {votes.map((item, i) => (
                  <div key={i} className="flex items-center gap-3 py-2 border-b border-white/5 last:border-0">
                    <div className="flex-1">
                      <div className="text-sm text-text-primary">{item.activity}</div>
                      <div className="flex gap-2 mt-1"><span className="text-[10px] text-green">👍 {item.votes.up}</span><span className="text-[10px] text-orange">👎 {item.votes.down}</span></div>
                    </div>
                    <div className="flex gap-1">
                      <motion.button whileTap={{ scale: 1.2 }} onClick={() => castVote(i, 'up')} className={`w-8 h-8 rounded-lg flex items-center justify-center text-sm cursor-pointer ${item.userVoted === 'up' ? 'bg-green/20' : 'bg-white/5 hover:bg-white/10'}`}>👍</motion.button>
                      <motion.button whileTap={{ scale: 1.2 }} onClick={() => castVote(i, 'down')} className={`w-8 h-8 rounded-lg flex items-center justify-center text-sm cursor-pointer ${item.userVoted === 'down' ? 'bg-orange/20' : 'bg-white/5 hover:bg-white/10'}`}>👎</motion.button>
                    </div>
                  </div>
                ))}
              </div>
            </div>
            <div className="glass-card-static p-5">
              <h3 className="text-sm font-medium text-text-secondary mb-4">Budget Split</h3>
              {COLLAB_MEMBERS.map(m => (
                <div key={m.id} className="flex items-center gap-3 mb-2.5">
                  <div className="w-6 h-6 rounded-full flex items-center justify-center text-[9px] font-semibold shrink-0" style={{ background: m.color }}>{m.avatar}</div>
                  <div className="flex-1">
                    <div className="flex justify-between mb-0.5"><span className="text-xs text-text-secondary">{m.name}</span><span className="text-xs text-text-dim">₹53.6k</span></div>
                    <div className="h-1 bg-white/7 rounded-full overflow-hidden"><motion.div className="h-full rounded-full" style={{ background: m.color }} initial={{ width: 0 }} animate={{ width: '25%' }} transition={{ duration: 0.6 }} /></div>
                  </div>
                </div>
              ))}
              <div className="mt-3 pt-3 border-t border-white/5 flex justify-between"><span className="text-xs text-text-dim">Total</span><span className="text-xs font-medium text-text-primary">₹2,14,500</span></div>
            </div>
          </div>
        </div>
      </div>
    </PageTransition>
  );
}
