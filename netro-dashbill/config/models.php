<?php
/**
 * Complete CRUD Models for NETPRO CRM
 */
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/app.php';

class Customer {
    public static function all() {
        global $pdo;
        // Auto-Isolir akun Prabayar yang melewati batas 30 menit atau masa aktif 30 hari
        try {
            $pdo->exec("UPDATE customers SET status = 'isolated' WHERE billing_type = 'prepaid' AND expired_at IS NOT NULL AND expired_at < CURRENT_TIMESTAMP AND status = 'active'");
        } catch (Throwable $t) {}

        return $pdo->query("SELECT c.*, p.name as package_name, p.speed_mbps, p.price as package_price FROM customers c LEFT JOIN packages p ON c.package_id = p.id ORDER BY c.id DESC")->fetchAll();
    }
    public static function find($id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    public static function create($data) {
        global $pdo;
        $cid = 'CID-' . rand(100000, 999999);
        $authMethod = $data['auth_method'] ?? 'pppoe';
        
        // Generate PPPoE username prefix from NIK (first 8 digits) + customer name KAPITAL (e.g. 32122725-SUSI)
        $rawNik = preg_replace('/[^0-9]/', '', $data['nik'] ?? '');
        $nikPrefix = strlen($rawNik) >= 8 ? substr($rawNik, 0, 8) : (!empty($rawNik) ? $rawNik : '32122725');
        $nameParts = explode(' ', trim($data['name'] ?? ''));
        $firstName = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $nameParts[0] ?? 'USER'));
        if (empty($firstName)) $firstName = 'USER';
        $defaultPppoeUser = $nikPrefix . '-' . $firstName;

        $pppoeUser = !empty($data['pppoe_user']) ? trim($data['pppoe_user']) : $defaultPppoeUser;
        $pppoePass = !empty($data['pppoe_password']) ? trim($data['pppoe_password']) : (string)rand(100000, 999999);

        $billingType = $data['billing_type'] ?? 'postpaid';
        $billingCycleType = $data['billing_cycle_type'] ?? 'anniversary'; // 'anniversary' (Rolling 30 Hari) or 'fixed_date' (Reset Akhir Bulan)

        // Pelanggan baru terdaftar berstatus 'inactive' (Belum Online / Menunggu Aktivasi Modem)
        $stmt = $pdo->prepare("INSERT INTO customers (cid, name, nik, phone, email, address, gps_lat, gps_lng, package_id, ppn_scheme, auth_method, pppoe_user, pppoe_password, billing_type, billing_cycle_type, expired_at, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NULL, 'inactive')");
        $stmt->execute([
            $cid, 
            $data['name'], 
            $data['nik'] ?? '3275' . rand(100000000000, 999999999999), 
            $data['phone'], 
            $data['email'] ?? '', 
            $data['address'], 
            $data['gps_lat'] ?? -6.2891, 
            $data['gps_lng'] ?? 106.9182, 
            $data['package_id'] ?? 2, 
            $data['ppn_scheme'] ?? 'include', 
            $authMethod,
            $pppoeUser,
            $pppoePass,
            $billingType,
            $billingCycleType
        ]);
        $newId = $pdo->lastInsertId();

        // Auto-Register Kredensial PPPoE ke Tabel RADIUS Users (Status Awal: DISCONNECTED)
        if (!empty($pppoeUser)) {
            $pkg = Package::find($data['package_id'] ?? 2);
            $pkgProfile = !empty($pkg['name']) ? 'PROFILE_' . strtoupper(preg_replace('/[^a-zA-Z0-9]/', '_', $pkg['name'])) : 'PROFILE_HOME_50M';
            $ipAlloc = '10.100.10.' . (10 + ($newId % 240));
            $stmtRad = $pdo->prepare("INSERT INTO radius_users (username, password, customer_name, profile_name, ip_address, nas_name, status) VALUES (?, ?, ?, ?, ?, 'CCR-CORE-HQ-01', 'DISCONNECTED')");
            $stmtRad->execute([$pppoeUser, $pppoePass, $data['name'], $pkgProfile, $ipAlloc]);
        }

        return $newId;
    }

    public static function setOnline($id) {
        global $pdo;
        $cust = self::find($id);
        if (!$cust) return false;

        $now = time();
        $billingType = $cust['billing_type'] ?? 'postpaid';
        $billingCycleType = $cust['billing_cycle_type'] ?? 'anniversary';
        
        // Update Customer Status & Grace Period
        $expiredAt = null;
        if ($billingType === 'prepaid') {
            $expiredAt = date('Y-m-d H:i:s', $now + (30 * 60)); // Grace Period 30 Menit sejak online
        }

        $stmt = $pdo->prepare("UPDATE customers SET status = 'active', expired_at = ? WHERE id = ?");
        $stmt->execute([$expiredAt, $id]);

        // Update RADIUS user status to CONNECTED
        if (!empty($cust['pppoe_user'])) {
            $pdo->prepare("UPDATE radius_users SET status = 'CONNECTED' WHERE username = ?")->execute([$cust['pppoe_user']]);
        }

        // Terbitkan Invoice Perdana jika belum ada invoice untuk pelanggan ini
        $existingInv = $pdo->prepare("SELECT id FROM invoices WHERE customer_id = ?");
        $existingInv->execute([$id]);
        if (!$existingInv->fetch()) {
            $pkg = Package::find($cust['package_id'] ?? 2);
            if ($pkg) {
                $totalDaysInMonth = (int)date('t');
                $currentDay = (int)date('j');
                $daysRemaining = max(1, $totalDaysInMonth - $currentDay + 1);
                $isProrata = ($billingCycleType === 'fixed_date');

                if ($isProrata && $daysRemaining < $totalDaysInMonth) {
                    $prorataFactor = $daysRemaining / $totalDaysInMonth;
                    $chargePrice = round($pkg['price'] * $prorataFactor);
                    $periodLabel = date('F Y') . " (Prorata $daysRemaining/$totalDaysInMonth Hari)";
                } else {
                    $chargePrice = $pkg['price'];
                    $periodLabel = date('F Y');
                }

                $calc = calculate_ppn($chargePrice, $cust['ppn_scheme'] ?? 'include');
                $invNo = 'INV-' . date('Y-m') . '-' . rand(1000, 9999);

                if ($billingType === 'prepaid') {
                    $dueDatePrepaid = date('Y-m-d H:i:s', $now + (30 * 60));
                    $cycleNote = ($billingCycleType === 'fixed_date') ? ' [Fixed Date]' : ' [Rolling 30D]';
                    $stmtInv = $pdo->prepare("INSERT INTO invoices (invoice_no, customer_id, billing_period, dpp_amount, ppn_amount, ppn_mode, billing_type, total_amount, due_date, status) VALUES (?, ?, ?, ?, ?, ?, 'prepaid', ?, ?, 'unpaid')");
                    $stmtInv->execute([$invNo, $id, $periodLabel . $cycleNote . ' [Grace 30 Menit]', $calc['dpp'], $calc['ppn'], $cust['ppn_scheme'] ?? 'include', $calc['total'], $dueDatePrepaid]);
                } else {
                    $dueDate = date('Y-m-20', strtotime('+1 month'));
                    $stmtInv = $pdo->prepare("INSERT INTO invoices (invoice_no, customer_id, billing_period, dpp_amount, ppn_amount, ppn_mode, billing_type, total_amount, due_date, status) VALUES (?, ?, ?, ?, ?, ?, 'postpaid', ?, ?, 'unpaid')");
                    $stmtInv->execute([$invNo, $id, $periodLabel, $calc['dpp'], $calc['ppn'], $cust['ppn_scheme'] ?? 'include', $calc['total'], $dueDate]);
                }
            }
        }

        return true;
    }

    public static function setOnlineByUsername($username) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT id FROM customers WHERE pppoe_user = ?");
        $stmt->execute([$username]);
        $cust = $stmt->fetch();
        if ($cust) {
            return self::setOnline($cust['id']);
        }
        return false;
    }
    public static function renewPrepaid($id, $days = 30, $paymentMethod = 'QRIS Dinamis') {
        global $pdo;
        $cust = self::find($id);
        if (!$cust) return false;

        $now = time();
        $currentExpiry = !empty($cust['expired_at']) ? strtotime($cust['expired_at']) : 0;
        $baseTime = ($currentExpiry > $now) ? $currentExpiry : $now;
        $cycleType = $cust['billing_cycle_type'] ?? 'anniversary';

        if ($cycleType === 'fixed_date') {
            // Fixed date: perpanjang hingga akhir bulan berikutnya
            $newExpiry = date('Y-m-t 23:59:59', strtotime("+1 month", $baseTime));
        } else {
            // Anniversary / Rolling date: perpanjang persis $days hari (default 30 hari)
            $newExpiry = date('Y-m-d H:i:s', $baseTime + ($days * 86400));
        }

        // Update Masa Aktif & Status
        $stmt = $pdo->prepare("UPDATE customers SET expired_at = ?, status = 'active' WHERE id = ?");
        $stmt->execute([$newExpiry, $id]);

        // Generate Invoice Lunas Perpanjangan Prabayar
        $pkg = Package::find($cust['package_id'] ?? 2);
        if ($pkg) {
            $calc = calculate_ppn($pkg['price'], $cust['ppn_scheme'] ?? 'include');
            $invNo = 'INV-TOPUP-' . date('Ym') . '-' . rand(1000, 9999);
            $stmtInv = $pdo->prepare("INSERT INTO invoices (invoice_no, customer_id, billing_period, dpp_amount, ppn_amount, ppn_mode, billing_type, total_amount, due_date, paid_date, payment_method, status) VALUES (?, ?, ?, ?, ?, ?, 'prepaid', ?, CURRENT_DATE, CURRENT_DATE, ?, 'paid')");
            $stmtInv->execute([$invNo, $id, 'Top-up ' . date('F Y'), $calc['dpp'], $calc['ppn'], $cust['ppn_scheme'] ?? 'include', $calc['total'], $paymentMethod]);
        }
        return $newExpiry;
    }
    public static function delete($id) {
        global $pdo;
        $cust = self::find($id);
        if ($cust && !empty($cust['pppoe_user'])) {
            $pdo->prepare("DELETE FROM radius_users WHERE username = ?")->execute([$cust['pppoe_user']]);
        }
        $stmt = $pdo->prepare("DELETE FROM customers WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

class Package {
    public static function all() {
        global $pdo;
        return $pdo->query("SELECT * FROM packages ORDER BY id ASC")->fetchAll();
    }
    public static function find($id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM packages WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    public static function create($data) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO packages (name, speed_mbps, price, default_ppn_mode, category) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$data['name'], $data['speed_mbps'], $data['price'], $data['default_ppn_mode'] ?? 'include', $data['category'] ?? 'home']);
    }
}

class Invoice {
    public static function all() {
        global $pdo;
        return $pdo->query("SELECT i.*, c.name as customer_name, c.cid, c.phone as customer_phone, c.billing_type as cust_billing_type, c.billing_cycle_type, p.name as package_name FROM invoices i JOIN customers c ON i.customer_id = c.id LEFT JOIN packages p ON c.package_id = p.id ORDER BY i.id DESC")->fetchAll();
    }
    public static function find($id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT i.*, c.name as customer_name, c.cid, c.address as customer_address, c.phone as customer_phone, c.billing_type as cust_billing_type, c.billing_cycle_type, c.pppoe_user, p.name as package_name, p.speed_mbps FROM invoices i JOIN customers c ON i.customer_id = c.id LEFT JOIN packages p ON c.package_id = p.id WHERE i.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    public static function pay($id, $paymentMethod = 'Transfer Bank BCA', $refNo = '') {
        global $pdo;
        $inv = self::find($id);
        if (!$inv) return false;

        $payMethodStr = !empty($paymentMethod) ? $paymentMethod : 'Transfer Bank BCA';
        if (!empty($refNo)) {
            $payMethodStr .= ' (Ref: ' . $refNo . ')';
        }

        $stmt = $pdo->prepare("UPDATE invoices SET status = 'paid', paid_date = CURRENT_TIMESTAMP, payment_method = ? WHERE id = ?");
        $stmt->execute([$payMethodStr, $id]);

        // Aktifkan kembali status pelanggan & hitung masa aktif
        $bType = strtolower($inv['cust_billing_type'] ?? ($inv['billing_type'] ?? 'postpaid'));
        $cust = Customer::find($inv['customer_id']);

        if ($bType === 'prepaid') {
            $cycleType = $cust['billing_cycle_type'] ?? 'anniversary';
            if ($cycleType === 'fixed_date') {
                $newExpiry = date('Y-m-t 23:59:59');
            } else {
                $newExpiry = date('Y-m-d H:i:s', strtotime('+30 days'));
            }
            $stmtCust = $pdo->prepare("UPDATE customers SET expired_at = ?, status = 'active' WHERE id = ?");
            $stmtCust->execute([$newExpiry, $inv['customer_id']]);
        } else {
            $stmtCust = $pdo->prepare("UPDATE customers SET status = 'active' WHERE id = ?");
            $stmtCust->execute([$inv['customer_id']]);
        }

        // Sinkronisasi status RADIUS ke CONNECTED
        if (!empty($inv['pppoe_user'])) {
            try {
                $pdo->prepare("UPDATE radius_users SET status = 'CONNECTED' WHERE username = ?")->execute([$inv['pppoe_user']]);
            } catch (Throwable $e) {}
        }

        // Catat ke Buku Kas Transaksi Penerimaan
        try {
            Cash::create([
                'trans_date' => date('Y-m-d'),
                'description' => 'Penerimaan Tagihan ' . $inv['invoice_no'] . ' - ' . ($inv['customer_name'] ?? 'Pelanggan') . ' (' . $payMethodStr . ')',
                'bank_account' => str_contains($payMethodStr, 'Tunai') ? 'Kasir HQ (1101)' : 'Bank BCA (1102)',
                'type' => 'in',
                'amount' => floatval($inv['total_amount'] ?? 0)
            ]);
        } catch (Throwable $e) {}

        // Jurnal Otomatis PSAK 72 (Balance: Total Debit == Total Kredit)
        try {
            $jrnNo = 'JRN-' . date('Ym') . '-' . str_pad($inv['id'], 4, '0', STR_PAD_LEFT);
            $transDate = date('Y-m-d');
            $desc = 'Pelunasan Faktur ' . $inv['invoice_no'] . ' a.n ' . ($inv['customer_name'] ?? 'Pelanggan');
            $cashAcc = str_contains($payMethodStr, 'Tunai') ? '1101' : '1102';

            // Debit: Kas/Bank
            JournalEntry::create([
                'journal_no' => $jrnNo,
                'trans_date' => $transDate,
                'account_code' => $cashAcc,
                'description' => $desc . ' (Penerimaan Kas/Bank)',
                'debit' => floatval($inv['total_amount']),
                'credit' => 0
            ]);

            // Kredit: Pendapatan Layanan FTTH (DPP)
            JournalEntry::create([
                'journal_no' => $jrnNo,
                'trans_date' => $transDate,
                'account_code' => '4101',
                'description' => $desc . ' (Pengakuan Pendapatan DPP)',
                'debit' => 0,
                'credit' => floatval($inv['dpp_amount'])
            ]);

            // Kredit: PPN Keluaran 11% (jika ada)
            if (floatval($inv['ppn_amount'] ?? 0) > 0) {
                JournalEntry::create([
                    'journal_no' => $jrnNo,
                    'trans_date' => $transDate,
                    'account_code' => '2103',
                    'description' => $desc . ' (PPN Keluaran 11%)',
                    'debit' => 0,
                    'credit' => floatval($inv['ppn_amount'])
                ]);
            }
        } catch (Throwable $e) {}

        return true;
    }

    public static function getDaysInMonth($year, $month) {
        return (int)date('t', strtotime("$year-$month-01"));
    }

    public static function addMonthSafe($dateStr, $months = 1) {
        $dt = new DateTime($dateStr);
        $origDay = (int)$dt->format('d');
        $dt->modify("first day of +$months month");
        $daysInTarget = (int)$dt->format('t');
        $targetDay = min($origDay, $daysInTarget);
        $dt->setDate((int)$dt->format('Y'), (int)$dt->format('m'), $targetDay);
        return $dt->format('Y-m-d');
    }

    public static function generateMassal($period = null) {
        global $pdo;
        $period = $period ?? date('F Y');
        $customers = Customer::all();
        $dueDaySetting = intval(Setting::get('billing_due_day', '20'));
        $generated = 0;

        foreach ($customers as $c) {
            // Hanya generate invoice massal untuk pelanggan PASCABAYAR (Fixed Date Terbit Tgl 1)
            $bType = strtolower($c['billing_type'] ?? 'postpaid');
            if ($c['status'] === 'active' && $bType === 'postpaid') {
                // Cek apakah sudah pernah terbit invoice di periode yang sama
                $stmtCheck = $pdo->prepare("SELECT id FROM invoices WHERE customer_id = ? AND billing_period = ? LIMIT 1");
                $stmtCheck->execute([$c['id'], $period]);
                if ($stmtCheck->fetch()) {
                    continue; // Lewati jika sudah pernah diterbitkan agar tidak duplikat
                }

                $calc = calculate_ppn($c['package_price'], $c['ppn_scheme']);
                $invNo = 'INV-' . date('Ym') . '-' . rand(1000, 9999);
                
                // Safe Due Date Clamping (e.g. Due day 31 on Feb becomes 28 or 29 leap year)
                $curYear = date('Y');
                $curMonth = date('m');
                $maxDays = (int)date('t', strtotime("$curYear-$curMonth-01"));
                $clampedDueDay = min($dueDaySetting, $maxDays);
                $dueDate = sprintf('%04d-%02d-%02d', $curYear, $curMonth, $clampedDueDay);

                $stmt = $pdo->prepare("INSERT INTO invoices (invoice_no, customer_id, billing_period, dpp_amount, ppn_amount, ppn_mode, billing_type, total_amount, due_date, status) VALUES (?, ?, ?, ?, ?, ?, 'postpaid', ?, ?, 'unpaid')");
                $stmt->execute([$invNo, $c['id'], $period, $calc['dpp'], $calc['ppn'], $c['ppn_scheme'], $calc['total'], $dueDate]);
                $generated++;
            }
        }
        return $generated;
    }

    public static function checkAndAutoIsolate() {
        global $pdo;
        $graceDays = intval(Setting::get('billing_grace_days', '5'));
        $autoIsolirEnabled = Setting::get('billing_auto_isolir', '1');
        if ($autoIsolirEnabled !== '1') {
            return ['isolated_count' => 0, 'status' => 'disabled'];
        }

        $today = date('Y-m-d');
        // Cari invoice unpaid yang tanggal jatuh temponya sudah lewat grace period
        $cutoffDate = date('Y-m-d', strtotime("-$graceDays days", strtotime($today)));
        
        $stmt = $pdo->prepare("
            SELECT DISTINCT i.customer_id, c.name, c.pppoe_user, c.phone, i.invoice_no, i.total_amount, i.due_date 
            FROM invoices i 
            JOIN customers c ON i.customer_id = c.id 
            WHERE (LOWER(i.status) = 'unpaid' OR LOWER(i.status) = 'belum bayar') 
              AND i.due_date <= ? 
              AND c.status = 'active'
        ");
        $stmt->execute([$cutoffDate]);
        $overdueCustomers = $stmt->fetchAll();

        $isolatedCount = 0;
        foreach ($overdueCustomers as $cust) {
            $custId = $cust['customer_id'];
            // 1. Ubah status customer menjadi isolated
            $pdo->prepare("UPDATE customers SET status = 'isolated' WHERE id = ?")->execute([$custId]);

            // 2. Ubah status di radius_users menjadi SUSPENDED
            if (!empty($cust['pppoe_user'])) {
                $pdo->prepare("UPDATE radius_users SET status = 'SUSPENDED' WHERE username = ?")->execute([$cust['pppoe_user']]);
            }

            // 3. Catat ke activity_logs
            try {
                $pdo->prepare("INSERT INTO activity_logs (action, description, created_at) VALUES ('AUTO_ISOLIR', ?, NOW())")
                    ->execute(["Pelanggan #$custId ({$cust['name']}) diisolir otomatis karena tunggakan invoice {$cust['invoice_no']} melewati batas $cutoffDate"]);
            } catch (Throwable $e) {}

            $isolatedCount++;
        }

        return [
            'isolated_count' => $isolatedCount,
            'cutoff_date' => $cutoffDate,
            'customers' => $overdueCustomers
        ];
    }

    public static function getReminderQueue() {
        global $pdo;
        $today = date('Y-m-d');
        $h3 = date('Y-m-d', strtotime('+3 days', strtotime($today)));
        $h1 = date('Y-m-d', strtotime('+1 day', strtotime($today)));
        $hPast1 = date('Y-m-d', strtotime('-1 day', strtotime($today)));

        // Ambil invoice unpaid untuk kategori reminder
        $stmt = $pdo->prepare("
            SELECT i.id as invoice_id, i.invoice_no, i.total_amount, i.due_date, i.billing_period,
                   c.id as customer_id, c.name, c.phone, c.cid,
                   CASE 
                       WHEN i.due_date = ? THEN 'H-3'
                       WHEN i.due_date = ? THEN 'H-1'
                       WHEN i.due_date = ? THEN 'H+1_OVERDUE'
                       ELSE 'GENERAL'
                   END as reminder_type
            FROM invoices i
            JOIN customers c ON i.customer_id = c.id
            WHERE (LOWER(i.status) = 'unpaid' OR LOWER(i.status) = 'belum bayar')
              AND (i.due_date = ? OR i.due_date = ? OR i.due_date = ?)
        ");
        $stmt->execute([$h3, $h1, $hPast1, $h3, $h1, $hPast1]);
        return $stmt->fetchAll();
    }
}

class Survey {
    public static function all() {
        global $pdo;
        return $pdo->query("SELECT * FROM surveys ORDER BY id DESC")->fetchAll();
    }
    public static function create($data) {
        global $pdo;
        $surveyNo = 'SRV-' . date('Y') . '-' . str_pad(rand(10, 999), 3, '0', STR_PAD_LEFT);
        $stmt = $pdo->prepare("INSERT INTO surveys (survey_no, customer_name, phone, address, nearest_odp, distance_m, tech_name, status, attenuation) VALUES (?, ?, ?, ?, ?, ?, ?, 'FEASIBLE', ?)");
        return $stmt->execute([$surveyNo, $data['customer_name'], $data['phone'], $data['address'], $data['nearest_odp'] ?? 'ODP-JTW-04/16', $data['distance_m'] ?? 75, $data['tech_name'] ?? 'Teknisi Rian', $data['attenuation'] ?? '-18.5 dBm']);
    }
    public static function delete($id) {
        global $pdo;
        return $pdo->prepare("DELETE FROM surveys WHERE id = ?")->execute([$id]);
    }
}

class WorkOrder {
    public static function all() {
        global $pdo;
        return $pdo->query("SELECT * FROM work_orders ORDER BY id DESC")->fetchAll();
    }
    public static function create($data) {
        global $pdo;
        $woNo = 'WO-' . date('Y') . '-' . str_pad(rand(10, 999), 4, '0', STR_PAD_LEFT);
        $stmt = $pdo->prepare("INSERT INTO work_orders (wo_no, customer_name, package_name, ont_type, ont_sn, tech_name, odp_port, attenuation, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'AKTIF & ONLINE')");
        return $stmt->execute([$woNo, $data['customer_name'], $data['package_name'] ?? 'Home Premium 50M', $data['ont_type'] ?? 'ZTE F660 Dualband', $data['ont_sn'] ?? 'ZTEG' . rand(10000000, 99999999), $data['tech_name'] ?? 'Teknisi Rian', $data['odp_port'] ?? 'ODP-JTW-04/16 (Port 3)', $data['attenuation'] ?? '-18.4 dBm']);
    }
    public static function delete($id) {
        global $pdo;
        return $pdo->prepare("DELETE FROM work_orders WHERE id = ?")->execute([$id]);
    }
}

class Addon {
    public static function all() {
        global $pdo;
        return $pdo->query("SELECT * FROM addons ORDER BY id ASC")->fetchAll();
    }
    public static function create($data) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO addons (name, category, price, description) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$data['name'], $data['category'] ?? 'ADDON PRO', $data['price'], $data['description']]);
    }
}

class Promo {
    public static function all() {
        global $pdo;
        return $pdo->query("SELECT * FROM promos ORDER BY id DESC")->fetchAll();
    }
    public static function create($data) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO promos (code, title, discount_amount, quota, valid_until, status) VALUES (?, ?, ?, ?, ?, 'AKTIF')");
        return $stmt->execute([strtoupper($data['code']), $data['title'], $data['discount_amount'], $data['quota'] ?? 100, $data['valid_until'] ?? date('Y-12-31')]);
    }
}

class RadiusNas {
    public static function all() {
        $db = get_radius_db();
        try {
            return $db->query("SELECT * FROM radius_nas ORDER BY id ASC")->fetchAll();
        } catch (Throwable $e) {
            return $db->query("SELECT id, nasname as name, nasname as ip_address, shortname as model, secret, 0 as active_sessions, 'ONLINE' as status FROM nas ORDER BY id ASC")->fetchAll();
        }
    }
    public static function create($data) {
        $db = get_radius_db();
        try {
            $stmt = $db->prepare("INSERT INTO radius_nas (name, ip_address, model, secret, active_sessions, status) VALUES (?, ?, ?, ?, ?, 'ONLINE')");
            return $stmt->execute([$data['name'], $data['ip_address'], $data['model'] ?? 'Mikrotik CCR', $data['secret'] ?? 'radiussecret', rand(10, 300)]);
        } catch (Throwable $e) {
            $stmt = $db->prepare("INSERT INTO nas (nasname, shortname, type, secret, description) VALUES (?, ?, 'mikrotik', ?, ?)");
            return $stmt->execute([$data['ip_address'], $data['name'], $data['secret'] ?? 'radiussecret', $data['model'] ?? 'Mikrotik CCR']);
        }
    }
    public static function delete($id) {
        $db = get_radius_db();
        try {
            return $db->prepare("DELETE FROM radius_nas WHERE id = ?")->execute([$id]);
        } catch (Throwable $e) {
            return $db->prepare("DELETE FROM nas WHERE id = ?")->execute([$id]);
        }
    }
}

class RadiusUser {
    public static function all() {
        $dbApp = get_db();
        $dbRad = get_radius_db();
        
        // Auto-sync pelanggan dari tabel customers yang memiliki kredensial PPPoE ke radius_users & radcheck
        try {
            $missing = $dbApp->query("SELECT c.*, p.name as pkg_name FROM customers c LEFT JOIN packages p ON c.package_id = p.id WHERE c.pppoe_user IS NOT NULL AND c.pppoe_user != ''")->fetchAll();
            foreach ($missing as $m) {
                $pkgProfile = !empty($m['pkg_name']) ? 'PROFILE_' . strtoupper(preg_replace('/[^a-zA-Z0-9]/', '_', $m['pkg_name'])) : 'PROFILE_HOME_50M';
                $ipAlloc = '10.100.10.' . (10 + ($m['id'] % 240));
                $statusStr = ($m['status'] === 'active') ? 'CONNECTED' : 'SUSPENDED';

                // 1. Sync to radius_users metadata
                try {
                    $stmt = $dbRad->prepare("INSERT INTO radius_users (username, password, customer_name, profile_name, ip_address, nas_name, status) VALUES (?, ?, ?, ?, ?, 'CCR-CORE-HQ-01', ?) ON CONFLICT (username) DO UPDATE SET status = EXCLUDED.status, password = EXCLUDED.password");
                    $stmt->execute([$m['pppoe_user'], $m['pppoe_password'] ?? '123456', $m['name'], $pkgProfile, $ipAlloc, $statusStr]);
                } catch (Throwable $t) {}

                // 2. Sync to native radcheck
                try {
                    $stmtRad = $dbRad->prepare("INSERT INTO radcheck (username, attribute, op, value) VALUES (?, 'Cleartext-Password', ':=', ?) ON CONFLICT DO NOTHING");
                    $stmtRad->execute([$m['pppoe_user'], $m['pppoe_password'] ?? '123456']);
                } catch (Throwable $t) {}
            }
        } catch (Throwable $t) {}

        try {
            return $dbRad->query("SELECT * FROM radius_users ORDER BY id DESC")->fetchAll();
        } catch (Throwable $e) {
            return [];
        }
    }
    public static function create($data) {
        $dbRad = get_radius_db();
        try {
            $stmt = $dbRad->prepare("INSERT INTO radius_users (username, password, customer_name, profile_name, ip_address, nas_name, status) VALUES (?, ?, ?, ?, ?, ?, 'CONNECTED')");
            $stmt->execute([$data['username'], $data['password'], $data['customer_name'], $data['profile_name'] ?? 'PROFILE_HOME_50M', $data['ip_address'] ?? '10.100.10.' . rand(10, 250), $data['nas_name'] ?? 'CCR-CORE-HQ-01']);
        } catch (Throwable $e) {}

        // Native radcheck insert
        try {
            $stmtRad = $dbRad->prepare("INSERT INTO radcheck (username, attribute, op, value) VALUES (?, 'Cleartext-Password', ':=', ?)");
            $stmtRad->execute([$data['username'], $data['password']]);
        } catch (Throwable $e) {}

        return true;
    }
    public static function delete($id) {
        $dbRad = get_radius_db();
        return $dbRad->prepare("DELETE FROM radius_users WHERE id = ?")->execute([$id]);
    }
}

class RadCheck {
    public static function all() {
        $db = get_radius_db();
        try {
            return $db->query("SELECT * FROM radcheck ORDER BY id ASC")->fetchAll();
        } catch (Throwable $e) {
            return [];
        }
    }
}

class RadAcct {
    public static function recent($limit = 50) {
        $db = get_radius_db();
        try {
            return $db->query("SELECT * FROM radacct ORDER BY radacctid DESC LIMIT " . intval($limit))->fetchAll();
        } catch (Throwable $e) {
            return [];
        }
    }
}

class RadiusProfile {
    public static function all() {
        $db = get_radius_db();
        try {
            return $db->query("SELECT * FROM radius_profiles ORDER BY id ASC")->fetchAll();
        } catch (Throwable $e) {
            return [];
        }
    }
    public static function create($data) {
        $db = get_radius_db();
        $stmt = $db->prepare("INSERT INTO radius_profiles (name, rate_limit, burst_limit, pool_name, user_count) VALUES (?, ?, ?, ?, 0)");
        return $stmt->execute([$data['name'], $data['rate_limit'], $data['burst_limit'] ?? '', $data['pool_name'] ?? 'pool_pppoe_home']);
    }
}

class RadiusVoucher {
    public static function all() {
        $db = get_radius_db();
        try {
            return $db->query("SELECT * FROM radius_vouchers ORDER BY id DESC")->fetchAll();
        } catch (Throwable $e) {
            return [];
        }
    }
    public static function create($data) {
        $db = get_radius_db();
        $batch = 'BATCH-' . date('Y') . '-' . str_pad(rand(10, 999), 3, '0', STR_PAD_LEFT);
        $stmt = $db->prepare("INSERT INTO radius_vouchers (batch_code, plan_name, duration, qty, price, status) VALUES (?, ?, ?, ?, ?, 'READY PRINT')");
        return $stmt->execute([$batch, $data['plan_name'], $data['duration'] ?? '2 Jam', $data['qty'] ?? 100, $data['price'] ?? 3000]);
    }
}

class NocOutage {
    public static function all() {
        global $pdo;
        return $pdo->query("SELECT * FROM noc_outages ORDER BY id DESC")->fetchAll();
    }
    public static function create($data) {
        global $pdo;
        $outNo = 'OUT-' . date('Y') . '-' . str_pad(rand(10, 999), 3, '0', STR_PAD_LEFT);
        $stmt = $pdo->prepare("INSERT INTO noc_outages (outage_no, location, issue_type, affected_users, tech_name, status) VALUES (?, ?, ?, ?, ?, 'ON PROGRESS')");
        return $stmt->execute([$outNo, $data['location'], $data['issue_type'], $data['affected_users'] ?? 50, $data['tech_name'] ?? 'Tim Splicing']);
    }
    public static function resolve($id) {
        global $pdo;
        return $pdo->prepare("UPDATE noc_outages SET status = 'RESOLVED / PULIH' WHERE id = ?")->execute([$id]);
    }
}

class Ticket {
    public static function all() {
        global $pdo;
        return $pdo->query("SELECT t.*, c.name as customer_name, c.cid, c.phone as customer_phone, c.address as customer_address, p.name as package_name FROM tickets t LEFT JOIN customers c ON t.customer_id = c.id LEFT JOIN packages p ON c.package_id = p.id ORDER BY t.id DESC")->fetchAll();
    }
    public static function find($id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT t.*, c.name as customer_name, c.cid, c.phone as customer_phone, c.address as customer_address, p.name as package_name FROM tickets t LEFT JOIN customers c ON t.customer_id = c.id LEFT JOIN packages p ON c.package_id = p.id WHERE t.id = ?");
        $stmt->execute([intval($id)]);
        return $stmt->fetch();
    }
    public static function create($data) {
        global $pdo;
        $ticketNo = $data['ticket_no'] ?? ('TCK-' . date('Ym') . '-' . rand(1000, 9999));
        $stmt = $pdo->prepare("INSERT INTO tickets (ticket_no, customer_id, category, priority, assigned_tech, sla_minutes, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $ticketNo, 
            intval($data['customer_id']), 
            $data['category'] ?? 'Gangguan Koneksi', 
            $data['priority'] ?? 'MEDIUM', 
            $data['assigned_tech'] ?? 'Teknisi Standby NOC', 
            intval($data['sla_minutes'] ?? 120),
            $data['status'] ?? 'OPEN'
        ]);
    }
    public static function updateStatus($id, $status) {
        global $pdo;
        return $pdo->prepare("UPDATE tickets SET status = ? WHERE id = ?")->execute([$status, intval($id)]);
    }
    public static function updatePriority($id, $priority) {
        global $pdo;
        return $pdo->prepare("UPDATE tickets SET priority = ? WHERE id = ?")->execute([$priority, intval($id)]);
    }
    public static function assignTech($id, $techName) {
        global $pdo;
        return $pdo->prepare("UPDATE tickets SET assigned_tech = ?, status = 'IN_PROGRESS' WHERE id = ?")->execute([$techName, intval($id)]);
    }
    public static function resolveTicket($id, $data) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE tickets SET 
            status = ?, 
            root_cause = ?, 
            action_taken = ?, 
            final_attenuation = ?, 
            resolution_notes = ?, 
            assigned_tech = ?,
            resolved_at = CURRENT_TIMESTAMP 
            WHERE id = ?");
        return $stmt->execute([
            $data['status'] ?? 'CLOSED',
            $data['root_cause'] ?? 'Penyebab Telah Teratasi',
            $data['action_taken'] ?? 'Tindakan Sesuai SOP',
            $data['final_attenuation'] ?? '-18.5 dBm',
            $data['resolution_notes'] ?? '',
            $data['assigned_tech'] ?? 'Teknisi Lapangan',
            intval($id)
        ]);
    }
    public static function delete($id) {
        global $pdo;
        return $pdo->prepare("DELETE FROM tickets WHERE id = ?")->execute([intval($id)]);
    }
}

class Complaint {
    public static function all() {
        global $pdo;
        try {
            return $pdo->query("SELECT * FROM complaints ORDER BY id DESC")->fetchAll();
        } catch (Throwable $e) {
            return [];
        }
    }
    public static function create($data) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO complaints (ticket_id, customer_id, customer_name, channel, category, sentiment, csat_rating, description, handler_name, compensation, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            !empty($data['ticket_id']) ? intval($data['ticket_id']) : NULL,
            !empty($data['customer_id']) ? intval($data['customer_id']) : NULL,
            $data['customer_name'] ?? 'Pelanggan',
            $data['channel'] ?? 'WhatsApp Care',
            $data['category'] ?? 'Keluhan Kecepatan / FUP',
            $data['sentiment'] ?? 'Kecewa',
            intval($data['csat_rating'] ?? 5),
            $data['description'] ?? '',
            $data['handler_name'] ?? 'Customer Care Officer',
            $data['compensation'] ?? '-',
            $data['status'] ?? 'INVESTIGASI'
        ]);
    }
    public static function updateStatus($id, $status) {
        global $pdo;
        return $pdo->prepare("UPDATE complaints SET status = ? WHERE id = ?")->execute([$status, intval($id)]);
    }
    public static function delete($id) {
        global $pdo;
        return $pdo->prepare("DELETE FROM complaints WHERE id = ?")->execute([intval($id)]);
    }
}

class Employee {
    public static function all() {
        global $pdo;
        return $pdo->query("SELECT * FROM employees ORDER BY id DESC")->fetchAll();
    }
    public static function create($data) {
        global $pdo;
        $nik = 'EMP-' . str_pad(rand(10, 999), 3, '0', STR_PAD_LEFT);
        $stmt = $pdo->prepare("INSERT INTO employees (nik, name, email, division, position, contract_status, status) VALUES (?, ?, ?, ?, ?, ?, 'active')");
        return $stmt->execute([$nik, $data['name'], $data['email'], $data['division'], $data['position'], $data['contract_status'] ?? 'TETAP']);
    }
    public static function delete($id) {
        global $pdo;
        return $pdo->prepare("DELETE FROM employees WHERE id = ?")->execute([$id]);
    }
}

class Leave {
    public static function all() {
        global $pdo;
        return $pdo->query("SELECT * FROM leaves ORDER BY id DESC")->fetchAll();
    }
    public static function create($data) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO leaves (employee_name, division, leave_type, start_date, end_date, duration_days, reason, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'APPROVED HRD')");
        return $stmt->execute([$data['employee_name'], $data['division'] ?? 'Operasional', $data['leave_type'], $data['start_date'], $data['end_date'], $data['duration_days'] ?? 3, $data['reason']]);
    }
}

class Inventory {
    public static function all() {
        global $pdo;
        return $pdo->query("SELECT * FROM inventory_items ORDER BY id ASC")->fetchAll();
    }
    public static function updateStock($id, $qty, $type = 'add') {
        global $pdo;
        $stmt = $pdo->prepare("SELECT stock FROM inventory_items WHERE id = ?");
        $stmt->execute([intval($id)]);
        $item = $stmt->fetch();
        if ($item) {
            $newStock = ($type === 'add') ? ($item['stock'] + $qty) : max(0, $item['stock'] - $qty);
            $status = ($newStock < 5) ? 'MENIPIS' : 'AMAN';
            return $pdo->prepare("UPDATE inventory_items SET stock = ?, status = ? WHERE id = ?")->execute([$newStock, $status, intval($id)]);
        }
        return false;
    }
}

class Cash {
    public static function all() {
        global $pdo;
        return $pdo->query("SELECT * FROM cash_transactions ORDER BY id DESC")->fetchAll();
    }
    public static function create($data) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO cash_transactions (trans_date, description, bank_account, type, amount, status) VALUES (?, ?, ?, ?, ?, 'VERIFIED')");
        return $stmt->execute([$data['trans_date'] ?? date('Y-m-d'), $data['description'], $data['bank_account'], $data['type'] ?? 'in', $data['amount']]);
    }
}

class Lead {
    public static function all() {
        global $pdo;
        return $pdo->query("SELECT * FROM leads ORDER BY id DESC")->fetchAll();
    }
    public static function create($data) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO leads (name, phone, address, package_interest, sales_agent, status) VALUES (?, ?, ?, ?, ?, 'JADWAL SURVEY')");
        return $stmt->execute([$data['name'], $data['phone'], $data['address'], $data['package_interest'] ?? 'Home Premium 50M', $data['sales_agent'] ?? 'Sales']);
    }
}

class Setting {
    public static function get($key, $default = '') {
        global $pdo;
        $stmt = $pdo->prepare("SELECT value FROM settings WHERE key = ?");
        $stmt->execute([$key]);
        $val = $stmt->fetchColumn();
        return ($val !== false && $val !== null) ? $val : $default;
    }
    public static function set($key, $value) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO settings (key, value) VALUES (?, ?) ON CONFLICT (key) DO UPDATE SET value = EXCLUDED.value");
        return $stmt->execute([$key, $value]);
    }
    public static function all() {
        global $pdo;
        return $pdo->query("SELECT * FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR);
    }
}

class CoaAccount {
    public static function all() {
        global $pdo;
        try {
            $count = $pdo->query("SELECT COUNT(*) FROM coa_accounts")->fetchColumn();
            if ($count == 0) {
                $defaults = [
                    ['1101', 'Kas Utama & Kasir HQ', 'Aset Lancar', 'Debit', 15000000],
                    ['1102', 'Bank BCA Bisnis Giro', 'Aset Lancar', 'Debit', 125000000],
                    ['1103', 'Bank Mandiri Corporate', 'Aset Lancar', 'Debit', 45000000],
                    ['1201', 'Piutang Usaha Pelanggan', 'Aset Lancar', 'Debit', 8500000],
                    ['1202', 'Cadangan Penurunan Piutang (CKPN)', 'Aset Lancar', 'Kredit', 500000],
                    ['1301', 'Persediaan Modem ONT & Router', 'Aset Lancar', 'Debit', 18500000],
                    ['1302', 'Persediaan Kabel Drop Optik', 'Aset Lancar', 'Debit', 12000000],
                    ['1401', 'Uang Muka & Pajak Dibayar di Muka', 'Aset Lancar', 'Debit', 4200000],
                    ['1601', 'Infrastruktur Fiber Optik & OLT', 'Aset Tetap', 'Debit', 350000000],
                    ['1602', 'Perangkat Server & Router Core', 'Aset Tetap', 'Debit', 85000000],
                    ['1603', 'Peralatan Splicer & OTDR / OPM', 'Aset Tetap', 'Debit', 45000000],
                    ['1604', 'Hak Guna Aset Sewa Tiang/Core', 'Aset Tetap', 'Debit', 60000000],
                    ['1699', 'Akumulasi Penyusutan Aset Tetap', 'Aset Tetap', 'Kredit', 42000000],
                    ['2101', 'Hutang Usaha Upstream Bandwidth', 'Liabilitas Pendek', 'Kredit', 28000000],
                    ['2102', 'Hutang Gaji Pegawai & BPJS', 'Liabilitas Pendek', 'Kredit', 15000000],
                    ['2103', 'Hutang Pajak PPN Keluaran 11%', 'Liabilitas Pendek', 'Kredit', 6500000],
                    ['2201', 'Liabilitas Kontrak (PSAK 72)', 'Liabilitas Pendek', 'Kredit', 12000000],
                    ['2301', 'Titipan Uang Jaminan Deposit ONT', 'Liabilitas Panjang', 'Kredit', 9500000],
                    ['3101', 'Modal Disetor Pendiri / Saham', 'Ekuitas', 'Kredit', 500000000],
                    ['3201', 'Saldo Laba Ditahan', 'Ekuitas', 'Kredit', 145000000],
                    ['3301', 'Laba Bersih Periode Berjalan', 'Ekuitas', 'Kredit', 35000000],
                    ['4101', 'Pendapatan Langganan FTTH', 'Pendapatan Usaha', 'Kredit', 78000000],
                    ['4102', 'Pendapatan Dedicated Corporate', 'Pendapatan Usaha', 'Kredit', 45000000],
                    ['4201', 'Pendapatan Biaya Pasang Baru', 'Pendapatan Usaha', 'Kredit', 6000000],
                    ['4301', 'Pendapatan Add-on (IP Publik)', 'Pendapatan Usaha', 'Kredit', 3500000],
                    ['5101', 'Beban Bandwidth Upstream IP Transit', 'COGS Pokok', 'Debit', 28000000],
                    ['5102', 'Beban Sewa Tiang Tumpu PLN & Core', 'COGS Pokok', 'Debit', 8500000],
                    ['5201', 'Beban Material & Dropcore Terpakai', 'COGS Pokok', 'Debit', 5500000],
                    ['6101', 'Beban Gaji & Tunjangan Karyawan', 'Beban OPEX', 'Debit', 24000000],
                    ['6102', 'Beban Listrik, Internet & Utilitas POP', 'Beban OPEX', 'Debit', 4500000],
                    ['6201', 'Beban Pemasaran & Komisi Sales', 'Beban OPEX', 'Debit', 3200000],
                    ['6301', 'Beban Kontribusi USO Kominfo 1.25%', 'Beban Regulasi', 'Debit', 1650000],
                    ['6302', 'Beban BHP Telekomunikasi Kominfo 0.50%', 'Beban Regulasi', 'Debit', 660000],
                    ['6401', 'Beban Penyusutan Aset Tetap FO', 'Beban OPEX', 'Debit', 5800000]
                ];
                $stmt = $pdo->prepare("INSERT INTO coa_accounts (code, name, category, normal_balance, balance) VALUES (?, ?, ?, ?, ?)");
                foreach ($defaults as $d) {
                    $stmt->execute($d);
                }
            }
        } catch (Throwable $t) {}

        return $pdo->query("SELECT * FROM coa_accounts ORDER BY code ASC")->fetchAll();
    }
    public static function find($code) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM coa_accounts WHERE code = ?");
        $stmt->execute([$code]);
        return $stmt->fetch();
    }
    public static function create($data) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO coa_accounts (code, name, category, normal_balance, balance) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$data['code'], $data['name'], $data['category'], $data['normal_balance'] ?? 'Debit', $data['balance'] ?? 0]);
    }
    public static function delete($id) {
        global $pdo;
        return $pdo->prepare("DELETE FROM coa_accounts WHERE id = ?")->execute([$id]);
    }
}

class JournalEntry {
    public static function all() {
        global $pdo;
        return $pdo->query("SELECT j.*, c.name as account_name FROM journal_entries j LEFT JOIN coa_accounts c ON j.account_code = c.code ORDER BY j.id DESC")->fetchAll();
    }
    public static function getByAccount($accountCode) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM journal_entries WHERE account_code = ? ORDER BY trans_date ASC, id ASC");
        $stmt->execute([$accountCode]);
        return $stmt->fetchAll();
    }
    public static function create($data) {
        global $pdo;
        try {
            CoaAccount::all(); // Ensure COA master accounts exist
        } catch (Throwable $e) {}
        $jrnNo = $data['journal_no'] ?? ('JRN-' . date('Y') . '-' . str_pad(rand(10, 999), 3, '0', STR_PAD_LEFT));
        $stmt = $pdo->prepare("INSERT INTO journal_entries (journal_no, trans_date, account_code, description, debit, credit) VALUES (?, ?, ?, ?, ?, ?)");
        $res = $stmt->execute([$jrnNo, $data['trans_date'] ?? date('Y-m-d'), $data['account_code'], $data['description'], floatval($data['debit'] ?? 0), floatval($data['credit'] ?? 0)]);

        // Update balance in coa_accounts
        $acc = CoaAccount::find($data['account_code']);
        if ($acc) {
            $diff = ($acc['normal_balance'] === 'Debit') ? (floatval($data['debit'] ?? 0) - floatval($data['credit'] ?? 0)) : (floatval($data['credit'] ?? 0) - floatval($data['debit'] ?? 0));
            $newBalance = $acc['balance'] + $diff;
            $pdo->prepare("UPDATE coa_accounts SET balance = ? WHERE code = ?")->execute([$newBalance, $data['account_code']]);
        }
        return $res;
    }
}

class TaxRecord {
    public static function all() {
        global $pdo;
        return $pdo->query("SELECT * FROM tax_records ORDER BY id DESC")->fetchAll();
    }
    public static function create($data) {
        global $pdo;
        $bupotNo = $data['bupot_no'] ?? ('BUPOT-23-' . date('Ym') . '-' . rand(1000, 9999));
        $taxAmount = floatval($data['dpp_amount'] ?? 0) * (floatval($data['rate_percent'] ?? 2) / 100);
        $stmt = $pdo->prepare("INSERT INTO tax_records (bupot_no, tax_type, vendor_name, npwp, obj_income, dpp_amount, rate_percent, tax_amount, period, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'TERBIT')");
        return $stmt->execute([$bupotNo, $data['tax_type'] ?? 'PPh 23', $data['vendor_name'], $data['npwp'] ?? '01.234.567.8-000.000', $data['obj_income'], floatval($data['dpp_amount'] ?? 0), floatval($data['rate_percent'] ?? 2), $taxAmount, $data['period'] ?? date('F Y')]);
    }
    public static function pay($id, $ntpn) {
        global $pdo;
        return $pdo->prepare("UPDATE tax_records SET status = 'LUNAS (NTPN)', ntpn = ? WHERE id = ?")->execute([$ntpn, $id]);
    }
}

class OpexExpense {
    public static function all() {
        global $pdo;
        return $pdo->query("SELECT * FROM opex_expenses ORDER BY id DESC")->fetchAll();
    }
    public static function create($data) {
        global $pdo;
        $vchNo = $data['voucher_no'] ?? ('VCH-OPEX-' . date('Ym') . '-' . str_pad(rand(10, 999), 3, '0', STR_PAD_LEFT));
        $stmt = $pdo->prepare("INSERT INTO opex_expenses (voucher_no, exp_date, category, vendor_name, description, amount, bank_account, approver, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'DISETUJUI')");
        $res = $stmt->execute([$vchNo, $data['exp_date'] ?? date('Y-m-d'), $data['category'], $data['vendor_name'] ?? 'Vendor Rekanan', $data['description'], floatval($data['amount'] ?? 0), $data['bank_account'], $data['approver'] ?? 'Manager Finance']);

        // Record also into cash_transactions
        Cash::create([
            'trans_date' => $data['exp_date'] ?? date('Y-m-d'),
            'description' => '[OPEX] ' . $data['description'] . ' (' . ($data['vendor_name'] ?? '') . ')',
            'bank_account' => $data['bank_account'],
            'type' => 'out',
            'amount' => floatval($data['amount'] ?? 0)
        ]);

        return $res;
    }
}

class Attendance {
    public static function all() {
        global $pdo;
        return $pdo->query("SELECT * FROM attendances ORDER BY id DESC")->fetchAll();
    }
    public static function create($data) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO attendances (employee_name, division, shift_type, clock_in, clock_out, gps_location, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$data['employee_name'], $data['division'] ?? 'Operasional', $data['shift_type'] ?? 'Shift Pagi (08:00 - 17:00)', $data['clock_in'] ?? date('H:i:s'), $data['clock_out'] ?? NULL, $data['gps_location'] ?? 'Kantor Pusat HQ (GPS Valid)', $data['status'] ?? 'TEPAT WAKTU']);
    }
}

class KpiIndicator {
    public static function all() {
        global $pdo;
        return $pdo->query("SELECT * FROM kpi_indicators ORDER BY id ASC")->fetchAll();
    }
    public static function create($data) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO kpi_indicators (division, name, target, weight, method, status) VALUES (?, ?, ?, ?, ?, 'AKTIF')");
        return $stmt->execute([$data['division'], $data['name'], $data['target'], intval($data['weight'] ?? 25), $data['method'] ?? 'Log Operasional']);
    }
    public static function delete($id) {
        global $pdo;
        return $pdo->prepare("DELETE FROM kpi_indicators WHERE id = ?")->execute([$id]);
    }
}

class PerformanceReview {
    public static function all() {
        global $pdo;
        return $pdo->query("SELECT * FROM performance_reviews ORDER BY total_score DESC")->fetchAll();
    }
    public static function create($data) {
        global $pdo;
        $tech = floatval($data['tech_score'] ?? 90);
        $disc = floatval($data['discipline_score'] ?? 90);
        $total = ($tech * 0.5) + ($disc * 0.5);
        $stmt = $pdo->prepare("INSERT INTO performance_reviews (employee_id, employee_name, division, position, tech_score, discipline_score, total_score, notes, supervisor_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([intval($data['employee_id'] ?? 1), $data['employee_name'], $data['division'] ?? 'Operasional', $data['position'] ?? 'Staf', $tech, $disc, $total, $data['notes'] ?? '', $data['supervisor_name'] ?? 'Supervisor Operasional']);
    }
}

class SalaryComponent {
    public static function all() {
        global $pdo;
        return $pdo->query("SELECT * FROM salary_components ORDER BY id ASC")->fetchAll();
    }
    public static function create($data) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO salary_components (code, name, category, formula, borne_by, status) VALUES (?, ?, ?, ?, ?, 'AKTIF')");
        return $stmt->execute([$data['code'], $data['name'], $data['category'], $data['formula'], $data['borne_by']]);
    }
    public static function delete($id) {
        global $pdo;
        return $pdo->prepare("DELETE FROM salary_components WHERE id = ?")->execute([$id]);
    }
}

class PayrollRecord {
    public static function all() {
        global $pdo;
        return $pdo->query("SELECT * FROM payroll_records ORDER BY id DESC")->fetchAll();
    }
    public static function processBatch($period = 'Juni 2026') {
        global $pdo;
        $employees = Employee::all();
        $count = 0;
        foreach ($employees as $emp) {
            $basic = 5000000;
            $allowance = 1200000;
            $bonus = 800000;
            $ded = 350000;
            $thp = $basic + $allowance + $bonus - $ded;
            $stmt = $pdo->prepare("INSERT INTO payroll_records (employee_id, employee_name, period, basic_salary, allowance, bonus, deductions, thp, status, bank_name, account_no) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'APPROVED', 'Bank Mandiri', ?)");
            $stmt->execute([$emp['id'], $emp['name'], $period, $basic, $allowance, $bonus, $ded, $thp, '124-000-' . rand(1000, 9999)]);
            $count++;
        }
        return $count;
    }
}

class BonusClaim {
    public static function all() {
        global $pdo;
        return $pdo->query("SELECT * FROM bonus_claims ORDER BY id DESC")->fetchAll();
    }
    public static function create($data) {
        global $pdo;
        $points = intval($data['points'] ?? 10);
        $rate = floatval($data['rate'] ?? 50000);
        $total = $points * $rate;
        $stmt = $pdo->prepare("INSERT INTO bonus_claims (employee_id, employee_name, role, bast_no, points, rate, total_amount, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'TERVERIFIKASI')");
        return $stmt->execute([intval($data['employee_id'] ?? 1), $data['employee_name'], $data['role'], $data['bast_no'] ?? ('BAST-' . date('Y-m') . '-' . rand(100, 999)), $points, $rate, $total]);
    }
    public static function approve($id) {
        global $pdo;
        return $pdo->prepare("UPDATE bonus_claims SET status = 'DICAIRKAN (PAYROLL)' WHERE id = ?")->execute([$id]);
    }
}

class Branch {
    public static function all() {
        global $pdo;
        return $pdo->query("SELECT * FROM branches ORDER BY id ASC")->fetchAll();
    }
    public static function find($id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM branches WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    public static function create($data) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO branches (code, name, address, manager, subs_count) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$data['code'], $data['name'], $data['address'], $data['manager'], intval($data['subs_count'] ?? 0)]);
    }
    public static function delete($id) {
        global $pdo;
        return $pdo->prepare("DELETE FROM branches WHERE id = ?")->execute([$id]);
    }
}

class User {
    public static function all() {
        global $pdo;
        return $pdo->query("SELECT * FROM users ORDER BY id ASC")->fetchAll();
    }
    public static function find($id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    public static function create($data) {
        global $pdo;
        $hashed = password_hash($data['password'] ?? 'admin123', PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password, full_name, email, role, status) VALUES (?, ?, ?, ?, ?, 'active')");
        return $stmt->execute([$data['username'], $hashed, $data['full_name'], $data['email'], $data['role']]);
    }
    public static function delete($id) {
        global $pdo;
        $u = self::find($id);
        if ($u && ($u['username'] === 'superadmin' || intval($id) === 1)) {
            return false; // Proteksi: Akun Superadmin utama tidak boleh dihapus
        }
        return $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([intval($id)]);
    }
    public static function toggleStatus($id) {
        global $pdo;
        $u = self::find($id);
        if ($u) {
            if ($u['username'] === 'superadmin' || intval($id) === 1) {
                return false; // Proteksi: Akun Superadmin utama tidak boleh dinonaktifkan
            }
            $newStatus = ($u['status'] === 'active') ? 'inactive' : 'active';
            return $pdo->prepare("UPDATE users SET status = ? WHERE id = ?")->execute([$newStatus, intval($id)]);
        }
        return false;
    }
    public static function updateProfile($id, $data) {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone = ?, division = ?, role = ? WHERE id = ?");
        return $stmt->execute([
            $data['full_name'],
            $data['email'],
            $data['phone'] ?? '0812-9876-5432',
            $data['division'] ?? 'NOC & Core Infrastructure',
            $data['role'] ?? 'Super Admin',
            intval($id)
        ]);
    }
    public static function updatePassword($id, $newPassword) {
        global $pdo;
        $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        return $stmt->execute([$hashed, intval($id)]);
    }
}

class Backup {
    public static function all() {
        global $pdo;
        return $pdo->query("SELECT * FROM backups ORDER BY id DESC")->fetchAll();
    }
    public static function createSnapshot() {
        global $pdo;
        $dbPath = __DIR__ . '/../database/app.db';
        $filename = 'backup_app_db_' . date('Y_m_d_His') . '.sqlite';
        $backupPath = __DIR__ . '/../database/' . $filename;
        
        if (file_exists($dbPath)) {
            copy($dbPath, $backupPath);
            $size = round(filesize($backupPath) / (1024 * 1024), 2) . ' MB';
            $stmt = $pdo->prepare("INSERT INTO backups (filename, filesize, created_at) VALUES (?, ?, ?)");
            $stmt->execute([$filename, $size, date('Y-m-d H:i:s')]);
            return true;
        }
        return false;
    }
    public static function delete($id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT filename FROM backups WHERE id = ?");
        $stmt->execute([intval($id)]);
        $fn = $stmt->fetchColumn();
        if ($fn) {
            $path = __DIR__ . '/../database/' . $fn;
            if (file_exists($path)) {
                @unlink($path);
            }
        }
        return $pdo->prepare("DELETE FROM backups WHERE id = ?")->execute([intval($id)]);
    }
}

class AuditLog {
    public static function all($limit = 50) {
        global $pdo;
        $safeLimit = max(1, min(1000, intval($limit)));
        return $pdo->query("SELECT * FROM audit_logs ORDER BY id DESC LIMIT $safeLimit")->fetchAll();
    }
    public static function log($user, $action, $details, $ip = '127.0.0.1', $status = 'success') {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO audit_logs (timestamp, username, action, ip_address, details, status) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([date('Y-m-d H:i:s'), $user, $action, $ip, $details, $status]);
    }
    public static function clear() {
        global $pdo;
        return $pdo->exec("DELETE FROM audit_logs");
    }
}


