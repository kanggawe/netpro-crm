/**
 * NETPRO CRM - Global JavaScript Application Handlers
 */

// Toggle sidebar accordion dropdown
function toggleSidebarMenu(menuId) {
    const menu = document.getElementById(menuId);
    const arrow = document.getElementById('arrow-' + menuId);
    if (!menu) return;

    const isOpen = menu.classList.contains('open');
    if (isOpen) {
        menu.classList.remove('open');
        if (arrow) arrow.style.transform = 'rotate(0deg)';
    } else {
        menu.classList.add('open');
        if (arrow) arrow.style.transform = 'rotate(180deg)';
    }
}

// Mobile sidebar toggle
function toggleMobileSidebar() {
    const sidebar = document.getElementById('sidebar');
    if (!sidebar) return;
    sidebar.classList.toggle('-translate-x-full');
}

// Search & filter sidebar menu items
function filterSidebarMenu() {
    const input = document.getElementById('sidebarSearch').value.toLowerCase();
    const menuGroups = document.querySelectorAll('.menu-group');

    menuGroups.forEach(group => {
        const text = group.innerText.toLowerCase();
        const submenu = group.querySelector('.submenu-container');
        if (input.trim() === '') {
            group.style.display = '';
        } else {
            if (text.includes(input)) {
                group.style.display = '';
                if (submenu) submenu.classList.add('open');
            } else {
                group.style.display = 'none';
            }
        }
    });
}

// Toast notification pop up
function triggerToast(title, message) {
    const toast = document.getElementById('toastBox');
    const titleEl = document.getElementById('toast-title');
    const descEl = document.getElementById('toast-desc');

    if (!toast) {
        alert(title + ': ' + message);
        return;
    }

    if (titleEl) titleEl.innerText = title;
    if (descEl) descEl.innerText = message;

    toast.classList.remove('hidden');
    toast.classList.remove('translate-y-10');

    setTimeout(() => {
        toast.classList.add('translate-y-10');
        setTimeout(() => toast.classList.add('hidden'), 300);
    }, 3500);
}

// Quick Register Modal controls
function showQuickRegisterModal() {
    const modal = document.getElementById('quickRegModal');
    if (modal) modal.classList.remove('hidden');
}

function hideQuickRegisterModal() {
    const modal = document.getElementById('quickRegModal');
    if (modal) modal.classList.add('hidden');
}

function submitNewCustomerSim(e) {
    e.preventDefault();
    hideQuickRegisterModal();
    triggerToast('Pendaftaran Berhasil', 'Pelanggan baru berhasil didaftarkan.');
}

function toggleNotificationDropdown(e) {
    if (e) e.stopPropagation();
    const dropdown = document.getElementById('notificationDropdown');
    if (!dropdown) return;
    dropdown.classList.toggle('hidden');
}

function toggleNotificationSim(e) {
    toggleNotificationDropdown(e);
}

function markAllNotificationsRead(e) {
    if (e) e.stopPropagation();
    const pingDot = document.getElementById('notif-ping-dot');
    const staticDot = document.getElementById('notif-static-dot');
    if (pingDot) pingDot.style.display = 'none';
    if (staticDot) staticDot.style.display = 'none';
    triggerToast('Notifikasi Sistem', 'Semua notifikasi telah ditandai sebagai dibaca.');
}

function logOutSim() {
    if (confirm('Apakah Anda yakin ingin keluar dari sistem NETPRO CRM?')) {
        triggerToast('Logout Berhasil', 'Sesi admin telah diakhiri.');
    }
}

// Auto-close notification dropdown on outside click or Escape key
document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('notificationDropdown');
    const bellBtn = document.getElementById('notifBellBtn');
    if (dropdown && !dropdown.classList.contains('hidden')) {
        if (!dropdown.contains(e.target) && (!bellBtn || !bellBtn.contains(e.target))) {
            dropdown.classList.add('hidden');
        }
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const dropdown = document.getElementById('notificationDropdown');
        if (dropdown) dropdown.classList.add('hidden');
    }
});

// Auto-center active menu item in sidebar viewport on load
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        const activeItem = document.querySelector('.sidebar-glow-active') || document.querySelector('.submenu-item.sidebar-glow-active');
        const scrollContainer = document.getElementById('sidebarMenuScroll');
        if (activeItem && scrollContainer) {
            activeItem.scrollIntoView({
                behavior: 'smooth',
                block: 'center',
                inline: 'nearest'
            });
        }
    }, 80);
});

