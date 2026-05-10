import { useEffect, useRef, useCallback } from 'react';
import { motion } from 'framer-motion';
import { useMood } from '../../context/MoodContext';
import { GLOBE_CITIES } from '../../data/seedData';

export default function GlobeVisualization() {
  const canvasRef = useRef(null);
  const animRef = useRef(null);
  const stateRef = useRef({
    rot: 0,
    tilt: 0.4,
    isDrag: false,
    lastX: 0,
    lastY: 0,
    accentColor: '#4C7BFF',
  });
  const { mood } = useMood();

  useEffect(() => {
    stateRef.current.accentColor = mood.color;
  }, [mood.color]);

  const latlonTo3d = useCallback((lat, lon, r, rot, tilt) => {
    const phi = (90 - lat) * Math.PI / 180;
    const theta = (lon + rot * 180 / Math.PI) * Math.PI / 180;
    return {
      x: r * Math.sin(phi) * Math.cos(theta),
      y: r * Math.cos(phi) * Math.cos(tilt) - r * Math.sin(phi) * Math.sin(theta) * Math.sin(tilt),
      z: r * Math.cos(phi) * Math.sin(tilt) + r * Math.sin(phi) * Math.sin(theta) * Math.cos(tilt),
    };
  }, []);

  const project = useCallback((p, cx, cy) => ({
    x: cx + p.x / 100 * 110,
    y: cy - p.y / 100 * 110,
    visible: p.z > 0,
  }), []);

  const draw = useCallback(() => {
    const canvas = canvasRef.current;
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const s = stateRef.current;
    const W = canvas.width, H = canvas.height;
    const cx = W / 2, cy = H / 2, R = 100;

    ctx.clearRect(0, 0, W, H);

    // Globe body
    ctx.beginPath();
    ctx.arc(cx, cy, R, 0, Math.PI * 2);
    ctx.fillStyle = '#0A1128';
    ctx.fill();

    // Atmosphere glow
    const atm = ctx.createRadialGradient(cx, cy, R * 0.85, cx, cy, R * 1.15);
    atm.addColorStop(0, 'rgba(76,123,255,0)');
    atm.addColorStop(0.7, s.accentColor + '10');
    atm.addColorStop(1, s.accentColor + '28');
    ctx.beginPath();
    ctx.arc(cx, cy, R * 1.15, 0, Math.PI * 2);
    ctx.fillStyle = atm;
    ctx.fill();

    // Border
    ctx.beginPath();
    ctx.arc(cx, cy, R, 0, Math.PI * 2);
    ctx.strokeStyle = 'rgba(76,123,255,.15)';
    ctx.lineWidth = 1;
    ctx.stroke();

    // Grid lines
    for (let la = -75; la <= 75; la += 30) {
      ctx.beginPath();
      let first = true;
      for (let lo = -180; lo <= 180; lo += 5) {
        const p = latlonTo3d(la, lo, R, s.rot, s.tilt);
        const pp = project(p, cx, cy);
        if (pp.visible) {
          if (first) { ctx.moveTo(pp.x, pp.y); first = false; }
          else ctx.lineTo(pp.x, pp.y);
        } else first = true;
      }
      ctx.strokeStyle = 'rgba(76,123,255,.08)';
      ctx.lineWidth = 0.5;
      ctx.stroke();
    }
    for (let lo = -180; lo <= 180; lo += 30) {
      ctx.beginPath();
      let first = true;
      for (let la = -90; la <= 90; la += 5) {
        const p = latlonTo3d(la, lo, R, s.rot, s.tilt);
        const pp = project(p, cx, cy);
        if (pp.visible) {
          if (first) { ctx.moveTo(pp.x, pp.y); first = false; }
          else ctx.lineTo(pp.x, pp.y);
        } else first = true;
      }
      ctx.strokeStyle = 'rgba(76,123,255,.08)';
      ctx.lineWidth = 0.5;
      ctx.stroke();
    }

    // Route arcs
    const pts = GLOBE_CITIES.map(c => ({
      ...c,
      p3: latlonTo3d(c.lat, c.lon, R, s.rot, s.tilt),
    }));
    for (let i = 0; i < pts.length - 1; i++) {
      const a = pts[i], b = pts[i + 1];
      if (a.p3.z > -20 && b.p3.z > -20) {
        const pa = project(a.p3, cx, cy), pb = project(b.p3, cx, cy);
        ctx.beginPath();
        const steps = 30;
        for (let step = 0; step <= steps; step++) {
          const t = step / steps;
          const ilat = a.lat + (b.lat - a.lat) * t;
          const ilon = a.lon + (b.lon - a.lon) * t;
          const arc = Math.sin(t * Math.PI) * 18;
          const ip = latlonTo3d(ilat, ilon, R + arc, s.rot, s.tilt);
          const ipp = project(ip, cx, cy);
          if (ipp.visible) {
            if (step === 0) ctx.moveTo(ipp.x, ipp.y);
            else ctx.lineTo(ipp.x, ipp.y);
          }
        }
        const grad = ctx.createLinearGradient(pa.x, pa.y, pb.x, pb.y);
        grad.addColorStop(0, a.color);
        grad.addColorStop(1, b.color);
        ctx.strokeStyle = grad;
        ctx.lineWidth = 2;
        ctx.setLineDash([4, 3]);
        ctx.stroke();
        ctx.setLineDash([]);
      }
    }

    // City dots
    pts.forEach(c => {
      const pp = project(c.p3, cx, cy);
      if (pp.visible) {
        // Glow
        ctx.beginPath();
        ctx.arc(pp.x, pp.y, 8, 0, Math.PI * 2);
        ctx.fillStyle = c.color + '30';
        ctx.fill();
        // Dot
        ctx.beginPath();
        ctx.arc(pp.x, pp.y, 4, 0, Math.PI * 2);
        ctx.fillStyle = c.color;
        ctx.fill();
        // Label
        ctx.fillStyle = '#DDE2F0';
        ctx.font = "bold 9px 'Inter', sans-serif";
        ctx.fillText(c.name, pp.x + 8, pp.y + 3);
      }
    });

    if (!s.isDrag) s.rot += 0.004;
    animRef.current = requestAnimationFrame(draw);
  }, [latlonTo3d, project]);

  useEffect(() => {
    const canvas = canvasRef.current;
    if (!canvas) return;
    const s = stateRef.current;

    const handleDown = (e) => { s.isDrag = true; s.lastX = e.offsetX; s.lastY = e.offsetY; };
    const handleMove = (e) => {
      if (!s.isDrag) return;
      s.rot -= (e.offsetX - s.lastX) * 0.01;
      s.tilt += (e.offsetY - s.lastY) * 0.005;
      s.tilt = Math.max(-1, Math.min(1, s.tilt));
      s.lastX = e.offsetX; s.lastY = e.offsetY;
    };
    const handleUp = () => { s.isDrag = false; };

    canvas.addEventListener('mousedown', handleDown);
    canvas.addEventListener('mousemove', handleMove);
    canvas.addEventListener('mouseup', handleUp);
    canvas.addEventListener('mouseleave', handleUp);

    animRef.current = requestAnimationFrame(draw);

    return () => {
      if (animRef.current) cancelAnimationFrame(animRef.current);
      canvas.removeEventListener('mousedown', handleDown);
      canvas.removeEventListener('mousemove', handleMove);
      canvas.removeEventListener('mouseup', handleUp);
      canvas.removeEventListener('mouseleave', handleUp);
    };
  }, [draw]);

  return (
    <motion.div
      initial={{ opacity: 0, scale: 0.95 }}
      animate={{ opacity: 1, scale: 1 }}
      transition={{ duration: 0.6, delay: 0.3 }}
      className="glass-card-static p-5"
    >
      <div className="flex items-center gap-2 text-sm font-medium text-text-secondary mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" className="text-accent">
          <circle cx="12" cy="12" r="10"/>
          <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/>
          <path d="M2 12h20"/>
        </svg>
        Multi-city Globe Route — drag to rotate
      </div>

      <div className="flex items-center justify-center" style={{ height: 260 }}>
        <canvas ref={canvasRef} id="globe" width={260} height={260} />
      </div>

      <div className="flex items-center justify-between mt-4 pt-3 border-t border-border">
        {[
          { value: '12', label: 'Days' },
          { value: '4', label: 'Cities' },
          { value: mood.label, label: 'Mood' },
          { value: '3', label: 'Friends' },
          { value: '23', label: 'Activities' },
        ].map((stat, i) => (
          <div key={i} className="flex items-center gap-3">
            {i > 0 && <div className="w-px h-9 bg-border" />}
            <div className="text-center">
              <div className="font-heading text-lg font-extrabold text-text-primary">{stat.value}</div>
              <div className="text-[10px] text-text-dim">{stat.label}</div>
            </div>
          </div>
        ))}
      </div>
    </motion.div>
  );
}
