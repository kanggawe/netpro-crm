import React from 'react';

export default function KpiCard({ title, value, subtitle, icon: Icon, color = 'red', trend }) {
  const colorMap = {
    red: {
      bg: 'bg-red-50 text-red-600 border-red-100',
      badge: 'bg-red-100 text-red-700',
    },
    emerald: {
      bg: 'bg-emerald-50 text-emerald-600 border-emerald-100',
      badge: 'bg-emerald-100 text-emerald-700',
    },
    blue: {
      bg: 'bg-blue-50 text-blue-600 border-blue-100',
      badge: 'bg-blue-100 text-blue-700',
    },
    amber: {
      bg: 'bg-amber-50 text-amber-600 border-amber-100',
      badge: 'bg-amber-100 text-amber-700',
    },
  };

  const scheme = colorMap[color] || colorMap.red;

  return (
    <div className="bg-white rounded-xl p-5 border border-slate-200 shadow-sm hover:shadow-md transition">
      <div className="flex items-center justify-between mb-2">
        <span className="text-xs font-semibold text-slate-500 uppercase tracking-wider">{title}</span>
        {Icon && (
          <div className={`p-2 rounded-lg border ${scheme.bg}`}>
            <Icon className="w-4 h-4" />
          </div>
        )}
      </div>
      <div>
        <h3 className="text-2xl font-black text-slate-900 tracking-tight">{value}</h3>
        {subtitle && <p className="text-xs text-slate-400 mt-0.5">{subtitle}</p>}
        {trend && (
          <span className="inline-flex items-center text-[11px] font-semibold text-emerald-600 mt-2">
            {trend}
          </span>
        )}
      </div>
    </div>
  );
}
