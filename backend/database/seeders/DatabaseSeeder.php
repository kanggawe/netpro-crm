<?php

namespace Database\Seeders;

use App\Models\Addon;
use App\Models\Attendance;
use App\Models\AuditLog;
use App\Models\Backup;
use App\Models\BonusClaim;
use App\Models\Branch;
use App\Models\CashTransaction;
use App\Models\CoaAccount;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\InventoryItem;
use App\Models\Invoice;
use App\Models\JournalEntry;
use App\Models\KpiIndicator;
use App\Models\Lead;
use App\Models\Leave;
use App\Models\NocOutage;
use App\Models\OpexExpense;
use App\Models\Package;
use App\Models\Payment;
use App\Models\PayrollRecord;
use App\Models\PerformanceReview;
use App\Models\Promo;
use App\Models\RadiusAcct;
use App\Models\RadiusNas;
use App\Models\RadiusUser;
use App\Models\SalaryComponent;
use App\Models\Setting;
use App\Models\Survey;
use App\Models\TaxRecord;
use App\Models\Ticket;
use App\Models\User;
use App\Models\WorkOrder;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // =========================================================================
        // 1. SUPERADMIN USER ONLY
        // =========================================================================
        User::firstOrCreate(
            ['username' => 'superadmin'],
            [
                'name' => 'Super Administrator',
                'full_name' => 'Super Administrator Utama NETPRO',
                'email' => 'superadmin@netpro.co.id',
                'phone' => '0812-9876-5432',
                'division' => 'NOC & Core Infrastructure',
                'role' => 'super admin',
                'status' => 'active',
                'password' => Hash::make('admin123'),
            ]
        );

        // =========================================================================
        // 2. ISP BROADBAND & DEDICATED PACKAGES
        // =========================================================================
        $packagesData = [
            ['name' => 'Home Lite 10M', 'speed_mbps' => 10, 'price' => 100000, 'default_ppn_mode' => 'include', 'category' => 'home'],
            ['name' => 'Home Basic 20M', 'speed_mbps' => 20, 'price' => 150000, 'default_ppn_mode' => 'include', 'category' => 'home'],
            ['name' => 'Home Premium 50M', 'speed_mbps' => 50, 'price' => 250000, 'default_ppn_mode' => 'include', 'category' => 'home'],
            ['name' => 'SOHO Pro 100M', 'speed_mbps' => 100, 'price' => 450000, 'default_ppn_mode' => 'include', 'category' => 'business'],
            ['name' => 'Dedicated DIA 100M', 'speed_mbps' => 100, 'price' => 2500000, 'default_ppn_mode' => 'exclude', 'category' => 'dedicated'],
        ];
        foreach ($packagesData as $pkg) {
            Package::firstOrCreate(['name' => $pkg['name']], $pkg);
        }

        // =========================================================================
        // 3. ADD-ONS & PROMO VOUCHERS
        // =========================================================================
        $addonsData = [
            ['name' => 'IP Publik Statis (/32)', 'category' => 'IP PUBLIK', 'price' => 50000, 'description' => '1 Alokasi IP Publik Statis untuk CCTV/Server'],
            ['name' => 'Mesh WiFi 6 Dual-Band', 'category' => 'MESH WIFI', 'price' => 35000, 'description' => 'Perangkat sewa Mesh WiFi Seamless Roaming'],
            ['name' => 'CCTV Cloud 7 Hari', 'category' => 'CCTV CLOUD', 'price' => 25000, 'description' => 'Penyimpanan rekaman CCTV Cloud 24/7'],
            ['name' => 'Speed Booster 2x (3 Hari)', 'category' => 'BOOSTER', 'price' => 25000, 'description' => 'Gandakan bandwidth instan tanpa ubah kontrak'],
            ['name' => 'STB Android 4K + OTT TV', 'category' => 'ENTERTAINMENT', 'price' => 35000, 'description' => '60+ Channel lokal & internasional plus YouTube'],
        ];
        foreach ($addonsData as $ad) {
            Addon::firstOrCreate(['name' => $ad['name']], $ad);
        }

        $promosData = [
            ['code' => 'NETPROMERDEKA', 'title' => 'Diskon Promo Merdeka 2026', 'discount_amount' => 50000, 'quota' => 100, 'valid_until' => $now->copy()->addMonths(3), 'status' => 'AKTIF'],
            ['code' => 'PASANGGRATIS', 'title' => 'Gratis Biaya Registrasi & Pasang', 'discount_amount' => 150000, 'quota' => 200, 'valid_until' => $now->copy()->addMonths(6), 'status' => 'AKTIF'],
            ['code' => 'UPGRADE30', 'title' => 'Diskon Rp 30.000 Upgrade ke 50M', 'discount_amount' => 30000, 'quota' => 50, 'valid_until' => $now->copy()->addMonths(2), 'status' => 'AKTIF'],
        ];
        foreach ($promosData as $pr) {
            Promo::firstOrCreate(['code' => $pr['code']], $pr);
        }

        // =========================================================================
        // 4. BRANCHES & EMPLOYEES
        // =========================================================================
        Branch::firstOrCreate(['code' => 'HQ-JKT'], [
            'name' => 'Headquarter Cyber Jakarta',
            'address' => 'Gedung Cyber Lt. 5, Jl. Kuningan Barat No. 8, Jakarta Selatan',
            'phone' => '021-5550100',
            'manager' => 'Ir. Budi Santoso',
            'subs_count' => 850,
            'status' => 'active',
        ]);

        Branch::firstOrCreate(['code' => 'BR-CLG'], [
            'name' => 'Kantor Cabang Banten Cilegon',
            'address' => 'Jl. Jend. Sudirman No. 45, Cilegon, Banten',
            'phone' => '0254-399100',
            'manager' => 'Hendra Gunawan, S.T.',
            'subs_count' => 420,
            'status' => 'active',
        ]);

        $employeesData = [
            [
                'nik' => 'EMP-2026-001',
                'name' => 'Ahmad Rian Maulana',
                'email' => 'rian.maulana@netpro.co.id',
                'phone' => '0813-8888-9999',
                'division' => 'Field Engineering',
                'position' => 'Senior FTTH Technician Lead',
                'contract_status' => 'TETAP',
                'basic_salary' => 5500000,
                'allowance' => 1200000,
                'bank_name' => 'BCA',
                'bank_account' => '8820192831',
                'status' => 'active',
            ],
            [
                'nik' => 'EMP-2026-002',
                'name' => 'Dimas Pratama',
                'email' => 'dimas.pratama@netpro.co.id',
                'phone' => '0812-7777-6666',
                'division' => 'NOC & Core Infrastructure',
                'position' => 'NOC Network Specialist',
                'contract_status' => 'TETAP',
                'basic_salary' => 6000000,
                'allowance' => 1500000,
                'bank_name' => 'Bank Mandiri',
                'bank_account' => '1270009823412',
                'status' => 'active',
            ],
            [
                'nik' => 'EMP-2026-003',
                'name' => 'Siti Nurhaliza',
                'email' => 'siti.nurhaliza@netpro.co.id',
                'phone' => '0812-1111-2222',
                'division' => 'Finance & Accounting',
                'position' => 'Senior Finance & Tax Officer',
                'contract_status' => 'TETAP',
                'basic_salary' => 5200000,
                'allowance' => 1000000,
                'bank_name' => 'BCA',
                'bank_account' => '7120394821',
                'status' => 'active',
            ],
            [
                'nik' => 'EMP-2026-004',
                'name' => 'Maya Anggraeni',
                'email' => 'maya.anggraeni@netpro.co.id',
                'phone' => '0857-4444-5555',
                'division' => 'Customer Service & Sales',
                'position' => 'Customer Care & Retention',
                'contract_status' => 'KONTRAK',
                'basic_salary' => 4500000,
                'allowance' => 800000,
                'bank_name' => 'BRI',
                'bank_account' => '0019283746501',
                'status' => 'active',
            ],
        ];

        $seededEmployees = [];
        foreach ($employeesData as $emp) {
            $seededEmployees[] = Employee::firstOrCreate(['nik' => $emp['nik']], $emp);
        }

        // =========================================================================
        // 5. PSAK CHART OF ACCOUNTS (COA)
        // =========================================================================
        $coaAccounts = [
            ['code' => '1101', 'name' => 'Kas & Setara Kas (BCA Operasional)', 'category' => 'ASET', 'normal_balance' => 'Debit', 'balance' => 45000000],
            ['code' => '1102', 'name' => 'Bank Mandiri Penerimaan VA', 'category' => 'ASET', 'normal_balance' => 'Debit', 'balance' => 25000000],
            ['code' => '1103', 'name' => 'Piutang Usaha Pelanggan', 'category' => 'ASET', 'normal_balance' => 'Debit', 'balance' => 8500000],
            ['code' => '1201', 'name' => 'Peralatan Jaringan (OLT & Router BRAS)', 'category' => 'ASET', 'normal_balance' => 'Debit', 'balance' => 120000000],
            ['code' => '1202', 'name' => 'Kabel Optik & Infrastruktur Tiang FO', 'category' => 'ASET', 'normal_balance' => 'Debit', 'balance' => 350000000],
            ['code' => '2101', 'name' => 'Hutang Usaha Vendor Bandwidth Upstream', 'category' => 'KEWAJIBAN', 'normal_balance' => 'Kredit', 'balance' => 15000000],
            ['code' => '2102', 'name' => 'Hutang PPN Keluaran 11%', 'category' => 'KEWAJIBAN', 'normal_balance' => 'Kredit', 'balance' => 4200000],
            ['code' => '2103', 'name' => 'Hutang Pajak PPh 23 Unifikasi', 'category' => 'KEWAJIBAN', 'normal_balance' => 'Kredit', 'balance' => 1200000],
            ['code' => '3101', 'name' => 'Modal Disetor Pemegang Saham', 'category' => 'EKUITAS', 'normal_balance' => 'Kredit', 'balance' => 500000000],
            ['code' => '3201', 'name' => 'Saldo Laba Ditahan', 'category' => 'EKUITAS', 'normal_balance' => 'Kredit', 'balance' => 75000000],
            ['code' => '4101', 'name' => 'Pendapatan Jasa Internet Residensial FTTH', 'category' => 'PENDAPATAN', 'normal_balance' => 'Kredit', 'balance' => 0],
            ['code' => '4102', 'name' => 'Pendapatan Jasa Internet Dedicated Korporat', 'category' => 'PENDAPATAN', 'normal_balance' => 'Kredit', 'balance' => 0],
            ['code' => '4201', 'name' => 'Pendapatan Biaya Pasang Baru (Instalasi)', 'category' => 'PENDAPATAN', 'normal_balance' => 'Kredit', 'balance' => 0],
            ['code' => '5101', 'name' => 'Beban IP Transit & Bandwidth Upstream', 'category' => 'BEBAN', 'normal_balance' => 'Debit', 'balance' => 0],
            ['code' => '5102', 'name' => 'Beban Sewa Tiang & Hak Lintas Kabel FO', 'category' => 'BEBAN', 'normal_balance' => 'Debit', 'balance' => 0],
            ['code' => '5103', 'name' => 'Beban Gaji & Upah Karyawan', 'category' => 'BEBAN', 'normal_balance' => 'Debit', 'balance' => 0],
            ['code' => '5104', 'name' => 'Beban PNBP Iuran USO Kominfo (1.25%)', 'category' => 'BEBAN', 'normal_balance' => 'Debit', 'balance' => 0],
            ['code' => '5105', 'name' => 'Beban PNBP Iuran BHP Kominfo (0.50%)', 'category' => 'BEBAN', 'normal_balance' => 'Debit', 'balance' => 0],
        ];
        foreach ($coaAccounts as $acc) {
            CoaAccount::firstOrCreate(['code' => $acc['code']], $acc);
        }

        // =========================================================================
        // 6. RADIUS NAS SERVERS
        // =========================================================================
        $nasServers = [
            [
                'nasname' => '127.0.0.1',
                'shortname' => 'CCR-CORE-HQ-01',
                'type' => 'mikrotik',
                'ports' => 1812,
                'secret' => 'testing123-radius-netpro',
                'description' => 'MikroTik CCR2116-12G-4S+ Core BRAS HQ Cyber',
                'ip_address' => '10.254.0.1',
                'api_port' => 8728,
                'status' => 'ONLINE',
            ],
            [
                'nasname' => '10.254.1.1',
                'shortname' => 'BRAS-POP-CLG-01',
                'type' => 'mikrotik',
                'ports' => 1812,
                'secret' => 'testing123-radius-netpro',
                'description' => 'MikroTik CCR1036-8G-2S+ POP Cilegon',
                'ip_address' => '10.254.1.1',
                'api_port' => 8728,
                'status' => 'ONLINE',
            ],
        ];
        foreach ($nasServers as $nas) {
            RadiusNas::firstOrCreate(['nasname' => $nas['nasname']], $nas);
        }

        // =========================================================================
        // 7. INVENTORY ITEMS (LOGISTIK / GUDANG)
        // =========================================================================
        $inventoryData = [
            ['sku' => 'ONT-ZTE-F670L', 'name' => 'ZTE F670L Dual Band GPON ONT Gigabit', 'category' => 'ONT / MODEM', 'stock' => 125, 'min_stock' => 20, 'unit' => 'Unit', 'unit_cost' => 245000, 'status' => 'AMAN'],
            ['sku' => 'ONT-HW-EG8145V5', 'name' => 'Huawei EG8145V5 Dual Band GPON ONT', 'category' => 'ONT / MODEM', 'stock' => 45, 'min_stock' => 15, 'unit' => 'Unit', 'unit_cost' => 265000, 'status' => 'AMAN'],
            ['sku' => 'FO-DROP-1CORE-1000M', 'name' => 'Drop Cable FO 1 Core 3 Seling 1000 Meter', 'category' => 'DROP CABLE FO', 'stock' => 18, 'min_stock' => 5, 'unit' => 'Roll', 'unit_cost' => 650000, 'status' => 'AMAN'],
            ['sku' => 'SFP-10G-LR-10KM', 'name' => 'SFP+ 10G LR 1310nm 10KM Transceiver', 'category' => 'SFP TRANSCEIVER', 'stock' => 32, 'min_stock' => 10, 'unit' => 'Pcs', 'unit_cost' => 185000, 'status' => 'AMAN'],
            ['sku' => 'ODP-SOLID-16PORT', 'name' => 'ODP Solid 16 Port Pole Mounted Enclosure', 'category' => 'ODP / FAT', 'stock' => 28, 'min_stock' => 8, 'unit' => 'Unit', 'unit_cost' => 145000, 'status' => 'AMAN'],
            ['sku' => 'PATCHCORD-SC-UPC-3M', 'name' => 'Patch Cord SC/UPC to SC/UPC Simplex 3M', 'category' => 'PATCH CORD', 'stock' => 240, 'min_stock' => 50, 'unit' => 'Pcs', 'unit_cost' => 8500, 'status' => 'AMAN'],
            ['sku' => 'FUSION-SPLICER-T410', 'name' => 'Signal Fire AI-9 Fusion Splicer Core Alignment', 'category' => 'TOOLS & ALAT UKUR', 'stock' => 4, 'min_stock' => 2, 'unit' => 'Unit', 'unit_cost' => 12500000, 'status' => 'AMAN'],
        ];
        foreach ($inventoryData as $inv) {
            InventoryItem::firstOrCreate(['sku' => $inv['sku']], $inv);
        }

        // =========================================================================
        // 8. CRM CUSTOMERS & RADIUS SYNC
        // =========================================================================
        $pkgLite = Package::where('name', 'Home Lite 10M')->first() ?? Package::find(1);
        $pkgBasic = Package::where('name', 'Home Basic 20M')->first() ?? Package::find(2);
        $pkgPrem = Package::where('name', 'Home Premium 50M')->first() ?? Package::find(3);
        $pkgSoho = Package::where('name', 'SOHO Pro 100M')->first() ?? Package::find(4);
        $pkgDed = Package::where('name', 'Dedicated DIA 100M')->first() ?? Package::find(5);

        $customersData = [
            [
                'cid' => 'CID-100881',
                'name' => 'HARTONO ARI SANDIKA',
                'nik' => '3275010190010001',
                'phone' => '0812-9876-1122',
                'email' => 'hartono.ari@gmail.com',
                'address' => 'Komplek Bumi Asri Blok A2 No. 5, Cilegon',
                'gps_lat' => -6.014230,
                'gps_lng' => 106.028940,
                'package_id' => $pkgBasic->id,
                'ppn_scheme' => 'include',
                'auth_method' => 'pppoe',
                'pppoe_user' => '32750101-HARTONO',
                'pppoe_password' => '782910',
                'billing_type' => 'postpaid',
                'billing_cycle_type' => 'anniversary',
                'status' => 'active',
            ],
            [
                'cid' => 'CID-100882',
                'name' => 'Susi Susanti',
                'nik' => '3275012304900001',
                'phone' => '0812-9988-7766',
                'email' => 'susi.susanti@gmail.com',
                'address' => 'Jl. Kenanga No. 14 RT 02/05, Jakarta Selatan',
                'gps_lat' => -6.289123,
                'gps_lng' => 106.918456,
                'package_id' => $pkgPrem->id,
                'ppn_scheme' => 'include',
                'auth_method' => 'pppoe',
                'pppoe_user' => '32750123-SUSI',
                'pppoe_password' => '789123',
                'billing_type' => 'postpaid',
                'billing_cycle_type' => 'anniversary',
                'status' => 'active',
            ],
            [
                'cid' => 'CID-100883',
                'name' => 'REYHAN FEBRIAN ARDIANSYAH',
                'nik' => '3275010594020003',
                'phone' => '0857-1234-8899',
                'email' => 'reyhan.febrian@gmail.com',
                'address' => 'Jl. Krakatau Indah No. 88, Cilegon',
                'gps_lat' => -6.021100,
                'gps_lng' => 106.035400,
                'package_id' => $pkgBasic->id,
                'ppn_scheme' => 'include',
                'auth_method' => 'pppoe',
                'pppoe_user' => '32750105-REYHAN',
                'pppoe_password' => '456123',
                'billing_type' => 'postpaid',
                'billing_cycle_type' => 'anniversary',
                'status' => 'active',
            ],
            [
                'cid' => 'CID-100884',
                'name' => 'ZERY ZULIFERT (HOME 1)',
                'nik' => '3275010892010004',
                'phone' => '0821-4455-6677',
                'email' => 'zery.zulifert@yahoo.com',
                'address' => 'Perum Cilegon Hills Blok B4 No. 12',
                'gps_lat' => -6.009800,
                'gps_lng' => 106.041200,
                'package_id' => $pkgLite->id,
                'ppn_scheme' => 'include',
                'auth_method' => 'pppoe',
                'pppoe_user' => '32750108-ZERY',
                'pppoe_password' => '998811',
                'billing_type' => 'prepaid',
                'billing_cycle_type' => 'anniversary',
                'status' => 'isolated',
            ],
            [
                'cid' => 'CID-100885',
                'name' => 'PT KRAKATAU SOLUSI TEKNOLOGI',
                'nik' => '3275011088030005',
                'phone' => '0254-388200',
                'email' => 'procurement@krakatausolusi.co.id',
                'address' => 'Kawasan Industri Krakatau Kav. E-4, Cilegon',
                'gps_lat' => -6.034567,
                'gps_lng' => 106.012345,
                'package_id' => $pkgDed->id,
                'ppn_scheme' => 'exclude',
                'auth_method' => 'pppoe',
                'pppoe_user' => '32750110-PTKRAKATAU',
                'pppoe_password' => 'krakatau2026',
                'billing_type' => 'postpaid',
                'billing_cycle_type' => 'fixed_date',
                'status' => 'active',
            ],
        ];

        $seededCustomers = [];
        foreach ($customersData as $cd) {
            $cust = Customer::firstOrCreate(['cid' => $cd['cid']], $cd);
            $seededCustomers[] = $cust;

            RadiusUser::firstOrCreate(['username' => $cust->pppoe_user], [
                'password' => $cust->pppoe_password,
                'customer_name' => $cust->name,
                'profile_name' => 'PROFILE_' . strtoupper(str_replace(' ', '_', $cust->package->name ?? 'HOME_20M')),
                'ip_address' => '10.100.10.' . rand(20, 240),
                'nas_name' => 'CCR-CORE-HQ-01',
                'rate_limit' => ($cust->package->speed_mbps ?? 20) . 'M/' . ($cust->package->speed_mbps ?? 20) . 'M',
                'status' => $cust->status === 'active' ? 'CONNECTED' : ($cust->status === 'isolated' ? 'ISOLATED' : 'DISCONNECTED'),
                'last_online_at' => $now,
            ]);
        }

        // =========================================================================
        // 9. INVOICES & PAYMENTS
        // =========================================================================
        $cust1 = $seededCustomers[0];
        $cust2 = $seededCustomers[1];
        $cust5 = $seededCustomers[4];

        $inv1 = Invoice::firstOrCreate(['invoice_no' => 'INV-2026-09-001'], [
            'customer_id' => $cust1->id,
            'billing_period' => 'September 2026',
            'dpp_amount' => 135135,
            'ppn_amount' => 14865,
            'total_amount' => 150000,
            'ppn_mode' => 'include',
            'billing_type' => 'postpaid',
            'due_date' => $now->copy()->addDays(15),
            'status' => 'PAID',
        ]);

        Payment::firstOrCreate(['payment_ref' => 'PAY-20260901-001'], [
            'invoice_id' => $inv1->id,
            'amount' => 150000,
            'payment_method' => 'BCA Virtual Account',
            'paid_at' => $now->copy()->subDays(1),
            'gateway_response' => 'SETTLEMENT',
        ]);

        $inv2 = Invoice::firstOrCreate(['invoice_no' => 'INV-2026-09-002'], [
            'customer_id' => $cust2->id,
            'billing_period' => 'September 2026',
            'dpp_amount' => 225225,
            'ppn_amount' => 24775,
            'total_amount' => 250000,
            'ppn_mode' => 'include',
            'billing_type' => 'postpaid',
            'due_date' => $now->copy()->addDays(18),
            'status' => 'UNPAID',
        ]);

        $inv3 = Invoice::firstOrCreate(['invoice_no' => 'INV-2026-09-003'], [
            'customer_id' => $cust5->id,
            'billing_period' => 'September 2026',
            'dpp_amount' => 2500000,
            'ppn_amount' => 275000,
            'total_amount' => 2775000,
            'ppn_mode' => 'exclude',
            'billing_type' => 'postpaid',
            'due_date' => $now->copy()->addDays(20),
            'status' => 'PAID',
        ]);

        Payment::firstOrCreate(['payment_ref' => 'PAY-20260901-002'], [
            'invoice_id' => $inv3->id,
            'amount' => 2775000,
            'payment_method' => 'Bank Mandiri Corporate Transfer',
            'paid_at' => $now->copy()->subHours(5),
            'gateway_response' => 'SETTLEMENT',
        ]);

        // =========================================================================
        // 10. LEADS, SURVEYS & WORK ORDERS (BAST)
        // =========================================================================
        $leadsData = [
            ['name' => 'Bpk. Hendra Gunawan', 'phone' => '0812-3344-5566', 'email' => 'hendra.gunawan@gmail.com', 'address' => 'Jl. Merdeka No. 45, RT 02/05 Cilegon', 'package_interest' => 'Home Premium 50M', 'sales_agent' => 'Maya Anggraeni', 'status' => 'SURVEY_SCHEDULED'],
            ['name' => 'Ibu Ratna Sari', 'phone' => '0857-9988-1122', 'email' => 'ratna.sari@yahoo.com', 'address' => 'Komplek Permata Indah Blok B3/12', 'package_interest' => 'Home Basic 20M', 'sales_agent' => 'Maya Anggraeni', 'status' => 'CONTACTED'],
            ['name' => 'Klinik Medika Sehat', 'phone' => '0254-311223', 'email' => 'admin@medikasehat.com', 'address' => 'Jl. Raya Anyer KM 3 Cilegon', 'package_interest' => 'SOHO Pro 100M', 'sales_agent' => 'Maya Anggraeni', 'status' => 'NEW LEAD'],
        ];
        foreach ($leadsData as $ld) {
            Lead::firstOrCreate(['phone' => $ld['phone']], $ld);
        }

        Survey::firstOrCreate(['survey_no' => 'SRV-2026-001'], [
            'customer_name' => 'Bpk. Hendra Gunawan',
            'phone' => '0812-3344-5566',
            'address' => 'Jl. Merdeka No. 45, RT 02/05 Cilegon',
            'nearest_odp' => 'ODP-CLG-04 (Port 6/8)',
            'distance_m' => 85,
            'attenuation' => '-18.4 dBm',
            'status' => 'APPROVED',
        ]);

        WorkOrder::firstOrCreate(['wo_no' => 'WO-2026-001'], [
            'customer_name' => 'HARTONO ARI SANDIKA',
            'package_name' => 'Home Basic 20M',
            'ont_type' => 'ZTE F670L Dual Band',
            'ont_sn' => 'ZTEGC9120938',
            'tech_name' => 'Ahmad Rian Maulana',
            'odp_port' => 'ODP-CLG-01 Port 3',
            'attenuation' => '-18.2 dBm',
            'status' => 'AKTIF & ONLINE',
            'bast_no' => 'BAST-2026-001',
        ]);

        WorkOrder::firstOrCreate(['wo_no' => 'WO-2026-002'], [
            'customer_name' => 'Bpk. Hendra Gunawan',
            'package_name' => 'Home Premium 50M',
            'ont_type' => 'ZTE F670L Dual Band',
            'ont_sn' => 'ZTEGC9812401',
            'tech_name' => 'Ahmad Rian Maulana',
            'odp_port' => 'ODP-CLG-04 Port 6',
            'attenuation' => '-18.4 dBm',
            'status' => 'IN_PROGRESS',
            'bast_no' => 'BAST-2026-002',
        ]);

        // =========================================================================
        // 11. NOC NETWORK OUTAGES & HELPDESK TICKETS
        // =========================================================================
        NocOutage::firstOrCreate(['outage_no' => 'INC-20260901-001'], [
            'location' => 'Jl. Jatiwaringin Raya KM 5 (Tiang ODP-14)',
            'issue_type' => 'FO Cut / Putus Kabel Feeder',
            'affected_users' => 45,
            'tech_name' => 'Tim Splicer NOC-1 (Ahmad Rian)',
            'status' => 'IN_PROGRESS',
        ]);

        NocOutage::firstOrCreate(['outage_no' => 'INC-20260901-002'], [
            'location' => 'POP Cilegon Center (OLT ZTE C320)',
            'issue_type' => 'Power PLN Outage & Genset Switchover',
            'affected_users' => 120,
            'tech_name' => 'Dimas Pratama',
            'status' => 'RESOLVED',
        ]);

        Ticket::firstOrCreate(['ticket_no' => 'TCK-20260901-1001'], [
            'customer_id' => $cust1->id,
            'category' => 'Redaman Optik Tinggi / LOS',
            'priority' => 'HIGH',
            'status' => 'OPEN',
            'assigned_tech' => 'Ahmad Rian Maulana',
            'sla_minutes' => 120,
            'description' => 'Redaman optik terukur -28.5 dBm pada dropcore tiang ODP-CLG-01.',
        ]);

        Ticket::firstOrCreate(['ticket_no' => 'TCK-20260901-1002'], [
            'customer_id' => $cust2->id,
            'category' => 'Ganti Password WiFi SSID',
            'priority' => 'LOW',
            'status' => 'RESOLVED',
            'assigned_tech' => 'Maya Anggraeni',
            'sla_minutes' => 60,
            'description' => 'Bantu ubah password WiFi modem ZTE F670L via remote TR-069.',
        ]);

        // =========================================================================
        // 12. FINANCE PSAK: CASH TRANSACTIONS, OPEX & TAX RECORDS
        // =========================================================================
        CashTransaction::firstOrCreate(['description' => 'Penerimaan Tagihan Langganan Internet via BCA VA'], [
            'trans_date' => $now->copy()->subDays(1),
            'bank_account' => 'BCA Operasional',
            'type' => 'in',
            'amount' => 150000,
            'status' => 'VERIFIED',
        ]);

        CashTransaction::firstOrCreate(['description' => 'Pembayaran Sewa Bandwidth Upstream 1 Gbps PT Telkom'], [
            'trans_date' => $now->copy()->subDays(2),
            'bank_account' => 'BCA Operasional',
            'type' => 'out',
            'amount' => 12500000,
            'status' => 'VERIFIED',
        ]);

        OpexExpense::firstOrCreate(['voucher_no' => 'OPX-2026-09-001'], [
            'exp_date' => $now->copy()->subDays(3),
            'category' => 'BANDWIDTH UPSTREAM',
            'vendor_name' => 'PT Telkom Indonesia (Persero) Tbk',
            'description' => 'Tagihan IP Transit & Metro-E Bandwidth 1 Gbps Core HQ',
            'amount' => 12500000,
            'bank_account' => 'BCA Operasional',
            'approver' => 'Ir. Budi Santoso',
            'status' => 'DISETUJUI',
        ]);

        OpexExpense::firstOrCreate(['voucher_no' => 'OPX-2026-09-002'], [
            'exp_date' => $now->copy()->subDays(2),
            'category' => 'SEWA TIANG & FO',
            'vendor_name' => 'PT PLN ICON PLUS',
            'description' => 'Sewa Hak Lintasan Kabel FO 24 Core 15 KM Jalur Cilegon',
            'amount' => 4500000,
            'bank_account' => 'BCA Operasional',
            'approver' => 'Ir. Budi Santoso',
            'status' => 'DISETUJUI',
        ]);

        TaxRecord::firstOrCreate(['bupot_no' => 'BUPOT-23-202609-001'], [
            'tax_type' => 'PPh 23',
            'vendor_name' => 'PT PLN ICON PLUS',
            'npwp' => '01.000.111.2-092.000',
            'obj_income' => 'Sewa Tiang & Hak Lintas Kabel FO',
            'dpp_amount' => 4500000,
            'rate_percent' => 2.00,
            'tax_amount' => 90000,
            'period' => '09-2026',
            'status' => 'TERBIT',
            'ntpn' => '1209384729104928',
        ]);

        TaxRecord::firstOrCreate(['bupot_no' => 'FAKTUR-PPN-202609-001'], [
            'tax_type' => 'PPN 11%',
            'vendor_name' => 'PT KRAKATAU SOLUSI TEKNOLOGI',
            'npwp' => '02.345.678.9-081.000',
            'obj_income' => 'Jasa Internet Dedicated DIA 100M',
            'dpp_amount' => 2500000,
            'rate_percent' => 11.00,
            'tax_amount' => 275000,
            'period' => '09-2026',
            'status' => 'DISETOR',
            'ntpn' => '9847291049283741',
        ]);

        JournalEntry::firstOrCreate(['journal_no' => 'JV-20260901-001', 'account_code' => '1101'], [
            'trans_date' => $now->copy()->subDays(1),
            'description' => 'Penerimaan Kas Tagihan Pelanggan CID-100881',
            'debit' => 150000,
            'credit' => 0,
            'ref_type' => 'INVOICE',
            'ref_id' => $inv1->id,
        ]);

        JournalEntry::firstOrCreate(['journal_no' => 'JV-20260901-001', 'account_code' => '4101'], [
            'trans_date' => $now->copy()->subDays(1),
            'description' => 'Pendapatan Jasa Internet Residensial CID-100881',
            'debit' => 0,
            'credit' => 150000,
            'ref_type' => 'INVOICE',
            'ref_id' => $inv1->id,
        ]);

        // =========================================================================
        // 13. HRD & PAYROLL: ATTENDANCE, LEAVES, KPI & PAYROLL RECORDS
        // =========================================================================
        Attendance::firstOrCreate([
            'employee_name' => 'Ahmad Rian Maulana',
            'att_date' => $now->toDateString(),
        ], [
            'employee_id' => $seededEmployees[0]->id,
            'division' => 'Field Engineering',
            'shift_type' => 'SHIFT PAGI (08:00 - 17:00)',
            'clock_in' => '07:54:20',
            'clock_out' => null,
            'gps_location' => 'Kantor Cabang Cilegon',
            'gps_lat' => -6.014230,
            'gps_lng' => 106.028940,
            'status' => 'TEPAT WAKTU',
        ]);

        Attendance::firstOrCreate([
            'employee_name' => 'Dimas Pratama',
            'att_date' => $now->toDateString(),
        ], [
            'employee_id' => $seededEmployees[1]->id,
            'division' => 'NOC & Core Infrastructure',
            'shift_type' => 'NOC 24/7 SHIFT A',
            'clock_in' => '07:45:10',
            'clock_out' => null,
            'gps_location' => 'HQ Cyber Jakarta',
            'gps_lat' => -6.289123,
            'gps_lng' => 106.918456,
            'status' => 'TEPAT WAKTU',
        ]);

        Leave::firstOrCreate([
            'employee_name' => 'Maya Anggraeni',
            'start_date' => $now->copy()->addDays(5)->toDateString(),
        ], [
            'employee_id' => $seededEmployees[3]->id,
            'division' => 'Customer Service & Sales',
            'leave_type' => 'TAHUNAN',
            'end_date' => $now->copy()->addDays(7)->toDateString(),
            'duration_days' => 3,
            'reason' => 'Keperluan keluarga di luar kota',
            'status' => 'APPROVED',
        ]);

        $salaryCompData = [
            ['code' => 'GAPOK', 'name' => 'Gaji Pokok Karyawan', 'category' => 'PENDAPATAN', 'formula' => 'Fixed Basic Salary', 'borne_by' => 'Perusahaan'],
            ['code' => 'TUNJ_JABATAN', 'name' => 'Tunjangan Jabatan & Keahlian', 'category' => 'PENDAPATAN', 'formula' => 'Fixed Allowance', 'borne_by' => 'Perusahaan'],
            ['code' => 'INSENTIF_BAST', 'name' => 'Insentif Pasang Baru / BAST', 'category' => 'PENDAPATAN', 'formula' => 'Rp 50.000 per BAST selesai', 'borne_by' => 'Perusahaan'],
            ['code' => 'BPJS_KES', 'name' => 'Iuran BPJS Kesehatan (1%)', 'category' => 'POTONGAN', 'formula' => '1% dari Gaji Pokok', 'borne_by' => 'Karyawan'],
            ['code' => 'PPH21', 'name' => 'Potongan Pajak PPh 21 TER', 'category' => 'POTONGAN', 'formula' => 'Tarif Efektif Rata-rata PPh 21', 'borne_by' => 'Karyawan'],
        ];
        foreach ($salaryCompData as $sc) {
            SalaryComponent::firstOrCreate(['code' => $sc['code']], $sc);
        }

        PayrollRecord::firstOrCreate([
            'employee_name' => 'Ahmad Rian Maulana',
            'period' => 'Agustus 2026',
        ], [
            'employee_id' => $seededEmployees[0]->id,
            'basic_salary' => 5500000,
            'allowance' => 1200000,
            'bonus' => 450000, // Insentif 9 BAST
            'deductions' => 125000,
            'thp' => 7025000,
            'status' => 'TRANSFERRED',
            'bank_name' => 'BCA',
            'account_no' => '8820192831',
        ]);

        BonusClaim::firstOrCreate(['bast_no' => 'BAST-2026-001'], [
            'employee_id' => $seededEmployees[0]->id,
            'employee_name' => 'Ahmad Rian Maulana',
            'role' => 'Teknisi Lead Pasang Baru',
            'points' => 10,
            'rate' => 50000,
            'total_amount' => 500000,
            'status' => 'CAIR',
        ]);

        KpiIndicator::firstOrCreate(['name' => 'Rata-rata Waktu Penanganan Gangguan (MTTR)'], [
            'division' => 'Field Engineering',
            'target' => '< 90 Menit',
            'weight' => 30,
            'method' => 'SLA Ticket Resolution Time',
            'status' => 'AKTIF',
        ]);

        KpiIndicator::firstOrCreate(['name' => 'Uptime Backbone & BRAS Core Network'], [
            'division' => 'NOC & Core Infrastructure',
            'target' => '>= 99.85%',
            'weight' => 40,
            'method' => 'Zabbix & NMS Monitoring',
            'status' => 'AKTIF',
        ]);

        PerformanceReview::firstOrCreate([
            'employee_name' => 'Ahmad Rian Maulana',
            'period' => 'Q2 2026',
        ], [
            'employee_id' => $seededEmployees[0]->id,
            'division' => 'Field Engineering',
            'position' => 'Senior FTTH Technician Lead',
            'tech_score' => 94.50,
            'discipline_score' => 92.00,
            'total_score' => 93.25,
            'notes' => 'Kinerja instalasi dan perbaikan jaringan sangat cepat dan tingkat kepuasan pelanggan tinggi.',
            'supervisor_name' => 'Ir. Budi Santoso',
        ]);

        // =========================================================================
        // 14. GLOBAL SETTINGS & AUDIT LOGS
        // =========================================================================
        $settings = [
            'company_name' => 'PT MITRAXCON SYNERGY UTAMA',
            'company_brand' => 'NETPRO CRM OS',
            'company_npwp' => '01.234.567.8-901.000',
            'company_address' => 'Gedung Cyber Lt. 5, Jl. Rasuna Said, Jakarta Selatan',
            'company_phone' => '021-5550199',
            'company_email' => 'billing@netpro.co.id',
            'kominfo_license_no' => 'No. 9120202611900/KOMINFO/2024',
            'ppn_rate' => '11',
            'default_ppn_mode' => 'include',
            'uso_rate' => '1.25',
            'bhp_rate' => '0.50',
            'postpaid_due_day' => '20',
            'billing_generate_day' => '1',
            'payment_gateway_provider' => 'midtrans',
            'wa_gateway_provider' => 'fonnte',
            'radius_server_ip' => '127.0.0.1',
            'radius_auth_port' => '1812',
            'radius_coa_port' => '3799',
        ];
        foreach ($settings as $k => $v) {
            Setting::set($k, $v);
        }

        Backup::firstOrCreate(['filename' => 'NETPRO_BACKUP_AUTO_20260901_020000.sql.gz'], [
            'filesize' => '14.8 MB',
            'path' => 'storage/backups/NETPRO_BACKUP_AUTO_20260901_020000.sql.gz',
        ]);

        AuditLog::create([
            'username' => 'superadmin',
            'action' => 'DATABASE_SEED_COMPLETE',
            'ip_address' => '127.0.0.1',
            'details' => 'Sistem telah menginisialisasi master data lengkap untuk seluruh menu NETPRO CRM.',
            'status' => 'success',
            'created_at' => $now,
        ]);
    }
}
