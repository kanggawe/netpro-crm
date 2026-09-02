-- ==========================================================
-- BILL-DASH / NETPRO ISP Management OS - PostgreSQL Schema
-- Complete 33 Tables Definition & Initial Seeds
-- ==========================================================

-- 1. PACKAGES (Katalog Paket Internet)
CREATE TABLE IF NOT EXISTS packages (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    speed_mbps INT NOT NULL,
    price NUMERIC(15,2) NOT NULL,
    default_ppn_mode VARCHAR(20) DEFAULT 'include',
    category VARCHAR(50) DEFAULT 'home',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- 2. CUSTOMERS (Master Pelanggan & PPPoE Credentials)
CREATE TABLE IF NOT EXISTS customers (
    id SERIAL PRIMARY KEY,
    cid VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(150) NOT NULL,
    nik VARCHAR(50) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    email VARCHAR(100),
    address TEXT NOT NULL,
    gps_lat NUMERIC(10,6) DEFAULT -6.2891,
    gps_lng NUMERIC(10,6) DEFAULT 106.9182,
    package_id INT REFERENCES packages(id) ON DELETE SET NULL,
    ppn_scheme VARCHAR(20) DEFAULT 'include',
    auth_method VARCHAR(20) DEFAULT 'pppoe',
    pppoe_user VARCHAR(100),
    pppoe_password VARCHAR(100),
    billing_type VARCHAR(20) DEFAULT 'postpaid',
    billing_cycle_type VARCHAR(30) DEFAULT 'anniversary',
    expired_at TIMESTAMP WITH TIME ZONE NULL,
    status VARCHAR(20) DEFAULT 'active',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- 3. INVOICES (Billing, DPP & PPN 11%)
CREATE TABLE IF NOT EXISTS invoices (
    id SERIAL PRIMARY KEY,
    invoice_no VARCHAR(50) UNIQUE NOT NULL,
    customer_id INT REFERENCES customers(id) ON DELETE CASCADE,
    billing_period VARCHAR(50) NOT NULL,
    dpp_amount NUMERIC(15,2) NOT NULL,
    ppn_amount NUMERIC(15,2) NOT NULL,
    ppn_mode VARCHAR(20) DEFAULT 'include',
    billing_type VARCHAR(20) DEFAULT 'postpaid',
    total_amount NUMERIC(15,2) NOT NULL,
    due_date DATE,
    paid_date DATE,
    payment_method VARCHAR(50),
    status VARCHAR(20) DEFAULT 'unpaid',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- 4. SURVEYS (Survey Lokasi Jaringan)
CREATE TABLE IF NOT EXISTS surveys (
    id SERIAL PRIMARY KEY,
    survey_no VARCHAR(50) UNIQUE NOT NULL,
    customer_name VARCHAR(150) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    address TEXT NOT NULL,
    gps_lat NUMERIC(10,6) DEFAULT -6.2891,
    gps_lng NUMERIC(10,6) DEFAULT 106.9182,
    nearest_odp VARCHAR(100),
    distance_m INT DEFAULT 50,
    tech_name VARCHAR(100),
    status VARCHAR(50) DEFAULT 'APPROVED',
    attenuation VARCHAR(50) DEFAULT '-18.2 dBm',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- 5. WORK_ORDERS (Surat Perintah Kerja & Instalasi)
CREATE TABLE IF NOT EXISTS work_orders (
    id SERIAL PRIMARY KEY,
    wo_no VARCHAR(50) UNIQUE NOT NULL,
    customer_name VARCHAR(150) NOT NULL,
    package_name VARCHAR(100),
    ont_type VARCHAR(100),
    ont_sn VARCHAR(100),
    tech_name VARCHAR(100),
    odp_port VARCHAR(100),
    attenuation VARCHAR(50),
    status VARCHAR(50) DEFAULT 'AKTIF & ONLINE',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- 6. ADDONS & VALUE ADDED SERVICES
CREATE TABLE IF NOT EXISTS addons (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50) DEFAULT 'ADDON PRO',
    price NUMERIC(15,2) NOT NULL,
    description TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- 7. PROMOS & VOUCHER DISKON
CREATE TABLE IF NOT EXISTS promos (
    id SERIAL PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    title VARCHAR(150) NOT NULL,
    discount_amount NUMERIC(15,2) NOT NULL,
    quota INT DEFAULT 100,
    valid_until DATE,
    status VARCHAR(20) DEFAULT 'AKTIF',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- 8. RADIUS NAS (MikroTik Router NAS)
CREATE TABLE IF NOT EXISTS radius_nas (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    ip_address VARCHAR(50) NOT NULL,
    model VARCHAR(100),
    secret VARCHAR(100) NOT NULL,
    active_sessions INT DEFAULT 0,
    status VARCHAR(20) DEFAULT 'ONLINE',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- 9. RADIUS USERS (PPPoE / Hotspot Credentials)
CREATE TABLE IF NOT EXISTS radius_users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(100) NOT NULL,
    customer_name VARCHAR(150),
    profile_name VARCHAR(100),
    ip_address VARCHAR(50),
    nas_name VARCHAR(100),
    status VARCHAR(20) DEFAULT 'ACTIVE',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- 10. RADIUS PROFILES (Queue Bandwidth Limits)
CREATE TABLE IF NOT EXISTS radius_profiles (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    rate_limit VARCHAR(50) NOT NULL,
    burst_limit VARCHAR(50),
    pool_name VARCHAR(50),
    user_count INT DEFAULT 0,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- 11. RADIUS VOUCHERS (Batch Voucher Hotspot)
CREATE TABLE IF NOT EXISTS radius_vouchers (
    id SERIAL PRIMARY KEY,
    batch_code VARCHAR(50) UNIQUE NOT NULL,
    plan_name VARCHAR(100) NOT NULL,
    duration VARCHAR(50),
    qty INT DEFAULT 100,
    price NUMERIC(15,2) NOT NULL,
    status VARCHAR(20) DEFAULT 'GENERATED',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- 12. NOC OUTAGES & FIBER CUT INCIDENTS
CREATE TABLE IF NOT EXISTS noc_outages (
    id SERIAL PRIMARY KEY,
    outage_no VARCHAR(50) UNIQUE NOT NULL,
    location VARCHAR(200) NOT NULL,
    issue_type VARCHAR(100),
    affected_users INT DEFAULT 0,
    tech_name VARCHAR(100),
    status VARCHAR(50) DEFAULT 'IN_PROGRESS',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- 13. TICKETS (Customer Support & Gangguan Lapangan)
CREATE TABLE IF NOT EXISTS tickets (
    id SERIAL PRIMARY KEY,
    ticket_no VARCHAR(50) UNIQUE NOT NULL,
    customer_id INT REFERENCES customers(id) ON DELETE SET NULL,
    category VARCHAR(100),
    priority VARCHAR(20) DEFAULT 'MEDIUM',
    assigned_tech VARCHAR(100),
    sla_minutes INT DEFAULT 120,
    status VARCHAR(50) DEFAULT 'OPEN',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- 14. EMPLOYEES (Manajemen Karyawan & Teknisi)
CREATE TABLE IF NOT EXISTS employees (
    id SERIAL PRIMARY KEY,
    nik VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(100),
    division VARCHAR(100),
    position VARCHAR(100),
    contract_status VARCHAR(50) DEFAULT 'TETAP',
    status VARCHAR(20) DEFAULT 'active',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- 15. LEAVES (Pengajuan Cuti Karyawan)
CREATE TABLE IF NOT EXISTS leaves (
    id SERIAL PRIMARY KEY,
    employee_name VARCHAR(150) NOT NULL,
    division VARCHAR(100),
    leave_type VARCHAR(100),
    start_date DATE,
    end_date DATE,
    duration_days INT,
    reason TEXT,
    status VARCHAR(50) DEFAULT 'PENDING',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- 16. INVENTORY_ITEMS (Gudang Material ONT & Kabel Optik)
CREATE TABLE IF NOT EXISTS inventory_items (
    id SERIAL PRIMARY KEY,
    sku VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(150) NOT NULL,
    stock INT DEFAULT 0,
    unit VARCHAR(20) DEFAULT 'Pcs',
    status VARCHAR(50) DEFAULT 'AMAN',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- 17. CASH_TRANSACTIONS (Buku Kas & Transaksi Masuk/Keluar)
CREATE TABLE IF NOT EXISTS cash_transactions (
    id SERIAL PRIMARY KEY,
    trans_date DATE NOT NULL,
    description TEXT NOT NULL,
    bank_account VARCHAR(100),
    type VARCHAR(10) CHECK (type IN ('in', 'out')),
    amount NUMERIC(15,2) NOT NULL,
    status VARCHAR(50) DEFAULT 'VERIFIED',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- 18. LEADS (Prospek Sales & Calon Pelanggan)
CREATE TABLE IF NOT EXISTS leads (
    id SERIAL PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    address TEXT,
    package_interest VARCHAR(100),
    sales_agent VARCHAR(100),
    status VARCHAR(50) DEFAULT 'NEW LEAD',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- 19. SETTINGS (Konfigurasi Global & Identitas Perusahaan)
CREATE TABLE IF NOT EXISTS settings (
    key VARCHAR(100) PRIMARY KEY,
    value TEXT NOT NULL
);

-- 20. USERS (Admin & RBAC Authentication)
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    full_name VARCHAR(150),
    name VARCHAR(150),
    email VARCHAR(100),
    phone VARCHAR(50),
    division VARCHAR(100),
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'administrator',
    status VARCHAR(20) DEFAULT 'active',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- 21. BRANCHES (Kantor Cabang Operasional ISP)
CREATE TABLE IF NOT EXISTS branches (
    id SERIAL PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(150) NOT NULL,
    address TEXT NOT NULL,
    manager VARCHAR(150),
    subs_count INT DEFAULT 0,
    status VARCHAR(20) DEFAULT 'active',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- 22. COA_ACCOUNTS (Chart of Accounts Akuntansi)
CREATE TABLE IF NOT EXISTS coa_accounts (
    id SERIAL PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(150) NOT NULL,
    category VARCHAR(50) NOT NULL,
    normal_balance VARCHAR(10) DEFAULT 'Debit',
    balance NUMERIC(15,2) DEFAULT 0,
    status VARCHAR(20) DEFAULT 'active',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- 23. JOURNAL_ENTRIES (Jurnal Akuntansi Umum)
CREATE TABLE IF NOT EXISTS journal_entries (
    id SERIAL PRIMARY KEY,
    journal_no VARCHAR(50) NOT NULL,
    trans_date DATE NOT NULL,
    account_code VARCHAR(50) REFERENCES coa_accounts(code) ON DELETE CASCADE,
    description TEXT NOT NULL,
    debit NUMERIC(15,2) DEFAULT 0,
    credit NUMERIC(15,2) DEFAULT 0,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- 24. TAX_RECORDS (Pajak PPh 23 / PPN Bupot)
CREATE TABLE IF NOT EXISTS tax_records (
    id SERIAL PRIMARY KEY,
    bupot_no VARCHAR(50) UNIQUE NOT NULL,
    tax_type VARCHAR(50) NOT NULL,
    vendor_name VARCHAR(150) NOT NULL,
    npwp VARCHAR(50),
    obj_income VARCHAR(150),
    dpp_amount NUMERIC(15,2) NOT NULL,
    rate_percent NUMERIC(5,2) DEFAULT 2,
    tax_amount NUMERIC(15,2) NOT NULL,
    period VARCHAR(50) NOT NULL,
    status VARCHAR(50) DEFAULT 'TERBIT',
    ntpn VARCHAR(100),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- 25. OPEX_EXPENSES (Pengeluaran Operasional)
CREATE TABLE IF NOT EXISTS opex_expenses (
    id SERIAL PRIMARY KEY,
    voucher_no VARCHAR(50) UNIQUE NOT NULL,
    exp_date DATE NOT NULL,
    category VARCHAR(100) NOT NULL,
    vendor_name VARCHAR(150),
    description TEXT NOT NULL,
    amount NUMERIC(15,2) NOT NULL,
    bank_account VARCHAR(100),
    approver VARCHAR(100),
    status VARCHAR(50) DEFAULT 'DISETUJUI',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- 26. ATTENDANCES (Absensi GPS Pegawai)
CREATE TABLE IF NOT EXISTS attendances (
    id SERIAL PRIMARY KEY,
    employee_name VARCHAR(150) NOT NULL,
    division VARCHAR(100),
    shift_type VARCHAR(100),
    clock_in TIME,
    clock_out TIME,
    gps_location TEXT,
    status VARCHAR(50) DEFAULT 'TEPAT WAKTU',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- 27. KPI_INDICATORS (Indikator Kinerja Karyawan)
CREATE TABLE IF NOT EXISTS kpi_indicators (
    id SERIAL PRIMARY KEY,
    division VARCHAR(100) NOT NULL,
    name VARCHAR(150) NOT NULL,
    target VARCHAR(100) NOT NULL,
    weight INT DEFAULT 25,
    method VARCHAR(100),
    status VARCHAR(50) DEFAULT 'AKTIF',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- 28. PERFORMANCE_REVIEWS (Review Nilai Kinerja)
CREATE TABLE IF NOT EXISTS performance_reviews (
    id SERIAL PRIMARY KEY,
    employee_id INT,
    employee_name VARCHAR(150) NOT NULL,
    division VARCHAR(100),
    position VARCHAR(100),
    tech_score NUMERIC(5,2) DEFAULT 90,
    discipline_score NUMERIC(5,2) DEFAULT 90,
    total_score NUMERIC(5,2) DEFAULT 90,
    notes TEXT,
    supervisor_name VARCHAR(150),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- 29. SALARY_COMPONENTS (Komponen Gaji & Tunjangan)
CREATE TABLE IF NOT EXISTS salary_components (
    id SERIAL PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(150) NOT NULL,
    category VARCHAR(50) NOT NULL,
    formula VARCHAR(150),
    borne_by VARCHAR(50) DEFAULT 'Perusahaan',
    status VARCHAR(50) DEFAULT 'AKTIF',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- 30. PAYROLL_RECORDS (Slip Gaji & Penggajian Karyawan)
CREATE TABLE IF NOT EXISTS payroll_records (
    id SERIAL PRIMARY KEY,
    employee_id INT,
    employee_name VARCHAR(150) NOT NULL,
    period VARCHAR(50) NOT NULL,
    basic_salary NUMERIC(15,2) DEFAULT 0,
    allowance NUMERIC(15,2) DEFAULT 0,
    bonus NUMERIC(15,2) DEFAULT 0,
    deductions NUMERIC(15,2) DEFAULT 0,
    thp NUMERIC(15,2) DEFAULT 0,
    status VARCHAR(50) DEFAULT 'APPROVED',
    bank_name VARCHAR(100),
    account_no VARCHAR(100),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- 31. BONUS_CLAIMS (Klaim Poin Insentif BAST)
CREATE TABLE IF NOT EXISTS bonus_claims (
    id SERIAL PRIMARY KEY,
    employee_id INT,
    employee_name VARCHAR(150) NOT NULL,
    role VARCHAR(100),
    bast_no VARCHAR(100),
    points INT DEFAULT 10,
    rate NUMERIC(15,2) DEFAULT 50000,
    total_amount NUMERIC(15,2) DEFAULT 500000,
    status VARCHAR(50) DEFAULT 'TERVERIFIKASI',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- 32. BACKUPS (Snapshot Cadangan Database)
CREATE TABLE IF NOT EXISTS backups (
    id SERIAL PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    filesize VARCHAR(50),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- 33. AUDIT_LOGS (Log Jejak Aktivitas Keamanan Sistem)
CREATE TABLE IF NOT EXISTS audit_logs (
    id SERIAL PRIMARY KEY,
    timestamp TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    username VARCHAR(100) NOT NULL,
    action VARCHAR(100) NOT NULL,
    ip_address VARCHAR(50) DEFAULT '127.0.0.1',
    details TEXT,
    status VARCHAR(50) DEFAULT 'success'
);

-- ==================== INITIAL MASTER SEEDS ====================

INSERT INTO packages (id, name, speed_mbps, price, default_ppn_mode, category) VALUES
(1, 'Home Lite 10M', 10, 100000, 'include', 'home'),
(2, 'Home Basic 20M', 20, 150000, 'include', 'home'),
(3, 'Home Premium 50M', 50, 250000, 'include', 'home'),
(4, 'SOHO Pro 100M', 100, 450000, 'include', 'business')
ON CONFLICT (id) DO NOTHING;

-- Seed packages only, no dummy customers/invoices

INSERT INTO users (id, username, full_name, name, email, phone, division, password, role, status) VALUES
(1, 'superadmin', 'Super Administrator Utama', 'Super Administrator', 'superadmin@netpro.co.id', '0812-9876-5432', 'NOC & Core Infrastructure', '$2y$10$abcdefghijklmnopqrstuu', 'super admin', 'active')
ON CONFLICT (id) DO NOTHING;

INSERT INTO settings (key, value) VALUES
('company_name', 'PT NETPRO TELEKOMUNIKASI INDONESIA'),
('company_npwp', '01.234.567.8-901.000'),
('ppn_rate', '11'),
('default_ppn_mode', 'include'),
('company_address', 'Gedung Cyber Lt. 5, Jl. Rasuna Said, Jakarta')
ON CONFLICT (key) DO NOTHING;

-- Synchronize all serial primary key sequences
SELECT setval('packages_id_seq', COALESCE((SELECT MAX(id) FROM packages), 0) + 1, false);
SELECT setval('customers_id_seq', COALESCE((SELECT MAX(id) FROM customers), 0) + 1, false);
SELECT setval('invoices_id_seq', COALESCE((SELECT MAX(id) FROM invoices), 0) + 1, false);
SELECT setval('surveys_id_seq', COALESCE((SELECT MAX(id) FROM surveys), 0) + 1, false);
SELECT setval('work_orders_id_seq', COALESCE((SELECT MAX(id) FROM work_orders), 0) + 1, false);
SELECT setval('addons_id_seq', COALESCE((SELECT MAX(id) FROM addons), 0) + 1, false);
SELECT setval('promos_id_seq', COALESCE((SELECT MAX(id) FROM promos), 0) + 1, false);
SELECT setval('radius_nas_id_seq', COALESCE((SELECT MAX(id) FROM radius_nas), 0) + 1, false);
SELECT setval('radius_users_id_seq', COALESCE((SELECT MAX(id) FROM radius_users), 0) + 1, false);
SELECT setval('radius_profiles_id_seq', COALESCE((SELECT MAX(id) FROM radius_profiles), 0) + 1, false);
SELECT setval('radius_vouchers_id_seq', COALESCE((SELECT MAX(id) FROM radius_vouchers), 0) + 1, false);
SELECT setval('noc_outages_id_seq', COALESCE((SELECT MAX(id) FROM noc_outages), 0) + 1, false);
SELECT setval('tickets_id_seq', COALESCE((SELECT MAX(id) FROM tickets), 0) + 1, false);
SELECT setval('employees_id_seq', COALESCE((SELECT MAX(id) FROM employees), 0) + 1, false);
SELECT setval('leaves_id_seq', COALESCE((SELECT MAX(id) FROM leaves), 0) + 1, false);
SELECT setval('inventory_items_id_seq', COALESCE((SELECT MAX(id) FROM inventory_items), 0) + 1, false);
SELECT setval('cash_transactions_id_seq', COALESCE((SELECT MAX(id) FROM cash_transactions), 0) + 1, false);
SELECT setval('leads_id_seq', COALESCE((SELECT MAX(id) FROM leads), 0) + 1, false);
SELECT setval('users_id_seq', COALESCE((SELECT MAX(id) FROM users), 0) + 1, false);
SELECT setval('branches_id_seq', COALESCE((SELECT MAX(id) FROM branches), 0) + 1, false);
SELECT setval('coa_accounts_id_seq', COALESCE((SELECT MAX(id) FROM coa_accounts), 0) + 1, false);
SELECT setval('journal_entries_id_seq', COALESCE((SELECT MAX(id) FROM journal_entries), 0) + 1, false);
SELECT setval('tax_records_id_seq', COALESCE((SELECT MAX(id) FROM tax_records), 0) + 1, false);
SELECT setval('opex_expenses_id_seq', COALESCE((SELECT MAX(id) FROM opex_expenses), 0) + 1, false);
SELECT setval('attendances_id_seq', COALESCE((SELECT MAX(id) FROM attendances), 0) + 1, false);
SELECT setval('kpi_indicators_id_seq', COALESCE((SELECT MAX(id) FROM kpi_indicators), 0) + 1, false);
SELECT setval('performance_reviews_id_seq', COALESCE((SELECT MAX(id) FROM performance_reviews), 0) + 1, false);
SELECT setval('salary_components_id_seq', COALESCE((SELECT MAX(id) FROM salary_components), 0) + 1, false);
SELECT setval('payroll_records_id_seq', COALESCE((SELECT MAX(id) FROM payroll_records), 0) + 1, false);
SELECT setval('bonus_claims_id_seq', COALESCE((SELECT MAX(id) FROM bonus_claims), 0) + 1, false);
SELECT setval('backups_id_seq', COALESCE((SELECT MAX(id) FROM backups), 0) + 1, false);
SELECT setval('audit_logs_id_seq', COALESCE((SELECT MAX(id) FROM audit_logs), 0) + 1, false);
