import React from 'react';
import { CheckCircle2, AlertCircle, Info, X } from 'lucide-react';

export default function Toast({ toast, onClose }) {
  if (!toast) return null;

  const isSuccess = toast.type === 'success';
  const isError = toast.type === 'error';

  return (
    <div className="fixed bottom-6 right-6 z-50 animate-bounce-in max-w-md">
      <div
        className={`p-4 rounded-xl shadow-2xl border backdrop-blur-xl flex items-start space-x-3 ${
          isSuccess
            ? 'bg-emerald-950/90 border-emerald-500/40 text-emerald-100 shadow-emerald-900/30'
            : isError
            ? 'bg-red-950/90 border-red-500/40 text-red-100 shadow-red-900/30'
            : 'bg-slate-900/90 border-white/10 text-slate-100'
        }`}
      >
        <div className="mt-0.5">
          {isSuccess && <CheckCircle2 className="w-5 h-5 text-emerald-400" />}
          {isError && <AlertCircle className="w-5 h-5 text-red-400" />}
          {!isSuccess && !isError && <Info className="w-5 h-5 text-blue-400" />}
        </div>
        <div className="flex-1 pr-2">
          <h4 className="text-sm font-bold capitalize">{toast.title || (isSuccess ? 'Sukses' : 'Perhatian')}</h4>
          <p className="text-xs text-slate-300 mt-0.5">{toast.message}</p>
        </div>
        <button onClick={onClose} className="text-slate-400 hover:text-white">
          <X className="w-4 h-4" />
        </button>
      </div>
    </div>
  );
}
