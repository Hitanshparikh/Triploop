import { useState } from 'react';
import { motion } from 'framer-motion';
import PageTransition from '../components/layout/PageTransition';

const INVOICES = [
  {
    id: 'INV-xyz-30290', trip: 'Paris & Rome Adventure', date: 'May 20, 2025', status: 'pending',
    travelers: ['James', 'Arjun', 'Jerry', 'Cristina'],
    items: [
      { desc: 'flight bookings (DEL -> PAR)', qty: '4 tickets', unitCost: 21000, amount: 84000 },
      { desc: 'hotel booking paris', qty: '3 nights', unitCost: 12000, amount: 36000 },
      { desc: 'hotel booking rome', qty: '3 nights', unitCost: 9000, amount: 27000 },
      { desc: 'Paragliding', qty: '4 persons', unitCost: 3000, amount: 12000 },
      { desc: 'travel insurance', qty: '4 persons', unitCost: 1050, amount: 4200 },
    ],
    subtotal: 163200, tax: 8160, discount: 0, grandTotal: 171360,
  },
  {
    id: 'INV-abc-40125', trip: 'Tokyo Expedition', date: 'Jun 1, 2025', status: 'paid',
    travelers: ['Rahul', 'Priya', 'Amit'],
    items: [
      { desc: 'flight bookings (DEL -> NRT)', qty: '3 tickets', unitCost: 24000, amount: 72000 },
      { desc: 'Hotel booking Tokyo', qty: '5 nights', unitCost: 11000, amount: 55000 },
      { desc: 'JR Pass', qty: '3 passes', unitCost: 12500, amount: 37500 },
    ],
    subtotal: 164500, tax: 8225, discount: 5000, grandTotal: 167725,
  },
];

export default function ExpenseInvoice() {
  const [search, setSearch] = useState('');
  const [selectedId, setSelectedId] = useState(null);
  const [statuses, setStatuses] = useState(() => {
    const s = {};
    INVOICES.forEach(inv => s[inv.id] = inv.status);
    return s;
  });

  const filtered = INVOICES.filter(inv => !search || inv.id.toLowerCase().includes(search.toLowerCase()) || inv.trip.toLowerCase().includes(search.toLowerCase()));
  const selected = INVOICES.find(inv => inv.id === selectedId);

  const markPaid = (id) => setStatuses(prev => ({ ...prev, [id]: 'paid' }));

  return (
    <PageTransition>
      <div className="px-6 lg:px-8 py-6 pb-24 lg:pb-8">
        <h1 className="font-heading text-2xl font-extrabold text-text-primary mb-1">Expense & Invoices</h1>
        <p className="text-sm text-text-secondary mb-5">Manage trip invoices, billing, and expense tracking</p>

        {/* Search */}
        <input type="text" value={search} onChange={e => setSearch(e.target.value)} placeholder="Search invoices..." className="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-text-primary text-sm outline-none placeholder:text-text-dim focus:border-accent/50 transition-colors mb-4" />

        {/* Invoice list */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-4">
          {filtered.map((inv, i) => (
            <motion.div key={inv.id} initial={{ opacity: 0, y: 10 }} animate={{ opacity: 1, y: 0 }} transition={{ delay: i * 0.05 }} onClick={() => setSelectedId(selectedId === inv.id ? null : inv.id)} className={`glass-card-static p-4 cursor-pointer transition-all ${selectedId === inv.id ? 'border-accent' : 'hover:border-accent/20'}`}>
              <div className="flex items-center justify-between mb-2">
                <span className="text-xs text-accent-light font-mono">{inv.id}</span>
                <span className={`text-[10px] px-2.5 py-0.5 rounded-full font-medium ${statuses[inv.id] === 'paid' ? 'bg-green/15 text-green' : 'bg-orange/15 text-orange'}`}>{statuses[inv.id]}</span>
              </div>
              <div className="text-sm font-medium text-text-primary mb-1">{inv.trip}</div>
              <div className="text-[10px] text-text-dim">Generated: {inv.date}</div>
              <div className="text-sm font-heading font-bold text-text-primary mt-2">₹{inv.grandTotal.toLocaleString()}</div>
            </motion.div>
          ))}
        </div>

        {/* Selected invoice detail */}
        {selected && (
          <motion.div initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }} className="glass-card-static p-6">
            <div className="flex items-start justify-between mb-5">
              <div>
                <h3 className="font-heading text-lg font-bold text-text-primary">Invoice Detail</h3>
                <div className="text-xs text-text-dim mt-1">Invoice ID: <span className="text-accent-light font-mono">{selected.id}</span></div>
                <div className="text-xs text-text-dim">Generated: {selected.date}</div>
                <div className="text-xs text-text-dim mt-2">Payment Status: <span className={statuses[selected.id] === 'paid' ? 'text-green' : 'text-orange'}>{statuses[selected.id]}</span></div>
              </div>
              <div className="text-right">
                <div className="text-xs text-text-muted mb-1">Traveler Details</div>
                {selected.travelers.map(t => <div key={t} className="text-xs text-text-secondary">{t}</div>)}
              </div>
            </div>

            <h4 className="text-sm font-medium text-text-primary mb-1">Trip: {selected.trip}</h4>

            {/* Items table */}
            <div className="overflow-x-auto mt-3">
              <table className="w-full text-xs">
                <thead>
                  <tr className="border-b border-white/10">
                    <th className="text-left py-2 text-text-muted font-medium">#</th>
                    <th className="text-left py-2 text-text-muted font-medium">Description</th>
                    <th className="text-left py-2 text-text-muted font-medium">Qty/Details</th>
                    <th className="text-right py-2 text-text-muted font-medium">Unit Cost</th>
                    <th className="text-right py-2 text-text-muted font-medium">Amount</th>
                  </tr>
                </thead>
                <tbody>
                  {selected.items.map((item, i) => (
                    <tr key={i} className="border-b border-white/5">
                      <td className="py-2.5 text-text-dim">{i + 1}</td>
                      <td className="py-2.5 text-text-primary">{item.desc}</td>
                      <td className="py-2.5 text-text-secondary">{item.qty}</td>
                      <td className="py-2.5 text-text-secondary text-right">₹{item.unitCost.toLocaleString()}</td>
                      <td className="py-2.5 text-text-primary text-right font-medium">₹{item.amount.toLocaleString()}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>

            {/* Totals */}
            <div className="border-t border-white/10 mt-3 pt-3 space-y-1.5 max-w-xs ml-auto">
              <div className="flex justify-between text-xs"><span className="text-text-muted">Subtotal</span><span className="text-text-secondary">₹{selected.subtotal.toLocaleString()}</span></div>
              <div className="flex justify-between text-xs"><span className="text-text-muted">Tax (5%)</span><span className="text-text-secondary">₹{selected.tax.toLocaleString()}</span></div>
              <div className="flex justify-between text-xs"><span className="text-text-muted">Discount</span><span className="text-green">-₹{selected.discount.toLocaleString()}</span></div>
              <div className="flex justify-between text-sm font-semibold border-t border-white/10 pt-2 mt-2"><span className="text-text-primary">Grand Total</span><span className="text-text-primary">₹{selected.grandTotal.toLocaleString()}</span></div>
            </div>

            {/* Action buttons */}
            <div className="flex flex-wrap gap-2 mt-5">
              {statuses[selected.id] !== 'paid' && (
                <motion.button whileHover={{ y: -1 }} whileTap={{ scale: 0.98 }} onClick={() => markPaid(selected.id)} className="btn-gradient px-5 py-2.5 rounded-xl text-xs">Mark as Paid</motion.button>
              )}
              <motion.button whileHover={{ y: -1 }} whileTap={{ scale: 0.98 }} className="px-5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-xs text-text-secondary hover:text-text-primary cursor-pointer">Download Invoice</motion.button>
              <motion.button whileHover={{ y: -1 }} whileTap={{ scale: 0.98 }} className="px-5 py-2.5 rounded-xl bg-white/5 border border-white/10 text-xs text-text-secondary hover:text-text-primary cursor-pointer">Export as PDF</motion.button>
            </div>
          </motion.div>
        )}
      </div>
    </PageTransition>
  );
}
