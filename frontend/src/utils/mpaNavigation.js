/**
 * Multi-Page Application (MPA) URL Resolver
 */
export const ROUTE_PAGE_MAP = {
  dashboard: '/dashboard.html',
  crm: '/crm.html',
  billing: '/billing.html',
  radius: '/radius.html',
  noc: '/noc.html',
  tickets: '/tickets.html',
  finance: '/finance.html',
  inventory: '/inventory.html',
  marketing: '/inventory.html',
  kalkulator: '/billing.html',
  hr: '/hr.html',
  kinerja: '/hr.html',
  payroll: '/hr.html',
  laporan: '/finance.html',
  pengaturan: '/settings.html',
};

export const getMpaUrl = (routeId) => {
  if (!routeId) return '/dashboard.html?tab=dashboard-utama';
  const prefix = routeId.split('-')[0];
  const targetHtml = ROUTE_PAGE_MAP[prefix] || '/dashboard.html';
  return `${targetHtml}?tab=${routeId}`;
};

export const getCurrentRouteFromUrl = (defaultRoute = 'dashboard-utama') => {
  if (typeof window === 'undefined') return defaultRoute;
  const params = new URLSearchParams(window.location.search);
  const tab = params.get('tab');
  if (tab) return tab;

  // Infer from pathname
  const path = window.location.pathname.toLowerCase();
  if (path.includes('login')) return 'login';
  if (path.includes('crm')) return 'crm-daftar';
  if (path.includes('billing')) return 'billing-daftar';
  if (path.includes('radius')) return 'radius-sessions';
  if (path.includes('noc')) return 'noc-monitoring';
  if (path.includes('tickets')) return 'tickets-list';
  if (path.includes('finance')) return 'finance-kas';
  if (path.includes('inventory')) return 'inventory-barang';
  if (path.includes('hr')) return 'hr-karyawan';
  if (path.includes('settings')) return 'pengaturan-sistem';
  
  return defaultRoute;
};

export const checkAuthOrRedirect = () => {
  if (typeof window === 'undefined') return true;
  const isLoginPage = window.location.pathname.includes('login.html');
  const token = localStorage.getItem('netpro_token');
  
  if (!token && !isLoginPage) {
    window.location.href = '/login.html';
    return false;
  }
  if (token && isLoginPage) {
    window.location.href = '/dashboard.html';
    return false;
  }
  return true;
};
