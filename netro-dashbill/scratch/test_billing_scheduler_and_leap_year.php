<?php
/**
 * Automated Verification Script: Billing Scheduler & Leap-Year Safe Clamping
 * NETPRO CRM (ISP Management OS) - TASK-02
 */
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/models.php';

echo "=================================================================\n";
echo "🧪 UJI VERIFIKASI BILLING SCHEDULER & PROTEKSI TAHUN KABISAT\n";
echo "=================================================================\n\n";

$passCount = 0;
$failCount = 0;

function assertSched($condition, $testName) {
    global $passCount, $failCount;
    if ($condition) {
        echo "  ✅ [PASS] $testName\n";
        $passCount++;
    } else {
        echo "  ❌ [FAIL] $testName\n";
        $failCount++;
    }
}

// 1. Uji Jumlah Hari di Bulan Februari (Tahun Biasa vs Tahun Kabisat)
echo "1. UJI KALENDER: JUMLAH HARI FEBRUARI (LEAP YEAR VS NORMAL YEAR)\n";
$daysFeb2024 = Invoice::getDaysInMonth(2024, '02'); // Kabisat
$daysFeb2026 = Invoice::getDaysInMonth(2026, '02'); // Normal
$daysFeb2028 = Invoice::getDaysInMonth(2028, '02'); // Kabisat
$daysFeb2032 = Invoice::getDaysInMonth(2032, '02'); // Kabisat

assertSched($daysFeb2024 === 29, "Tahun 2024 (Kabisat): Februari = 29 Hari");
assertSched($daysFeb2026 === 28, "Tahun 2026 (Biasa): Februari = 28 Hari");
assertSched($daysFeb2028 === 29, "Tahun 2028 (Kabisat): Februari = 29 Hari");
assertSched($daysFeb2032 === 29, "Tahun 2032 (Kabisat): Februari = 29 Hari");

// 2. Uji Month-Safe Clamping Penambahan 1 Bulan
echo "\n2. UJI BULANAN: SAFE ADD MONTH (TANGGAL 31 JANUARI KE FEBRUARI)\n";
$safeAddNormal = Invoice::addMonthSafe('2026-01-31', 1);
$safeAddLeap   = Invoice::addMonthSafe('2028-01-31', 1);
$safeAdd29Feb  = Invoice::addMonthSafe('2028-02-29', 1);

assertSched($safeAddNormal === '2026-02-28', "31 Jan 2026 + 1 Bulan di-clamp aman ke 28 Feb 2026 (Tidak overflow ke Maret)");
assertSched($safeAddLeap === '2028-02-29', "31 Jan 2028 + 1 Bulan di-clamp aman ke 29 Feb 2028 (Kabisat)");
assertSched($safeAdd29Feb === '2028-03-29', "29 Feb 2028 + 1 Bulan menjadi 29 Maret 2028");

// 3. Uji Prorata Harian Februari
echo "\n3. UJI PRORATA: HARGA PROPORSIAL PADA BULAN KABISAT\n";
$pkgPrice = 300000;
$dailyRateNormal = $pkgPrice / $daysFeb2026; // 300.000 / 28
$dailyRateLeap = $pkgPrice / $daysFeb2028;   // 300.000 / 29

assertSched(abs($dailyRateNormal - 10714.28) < 1, "Tarif harian Feb 2026 (28 hari): Rp 10.714 / hari");
assertSched(abs($dailyRateLeap - 10344.82) < 1, "Tarif harian Feb 2028 (29 hari): Rp 10.345 / hari");

// 4. Uji Auto-Isolir & Reminder Queue
echo "\n4. UJI ENGINE: AUTO-ISOLIR & ANTRIAN REMINDER WHATSAPP\n";
$reminders = Invoice::getReminderQueue();
assertSched(is_array($reminders), "Invoice::getReminderQueue() berhasil membaca antrean reminder");

$isolateResult = Invoice::checkAndAutoIsolate();
assertSched(is_array($isolateResult) && isset($isolateResult['isolated_count']), "Invoice::checkAndAutoIsolate() berhasil memvalidasi status cut-off");

echo "\n=================================================================\n";
echo "📊 HASIL PENGUJIAN: $passCount LULUS, $failCount GAGAL\n";
echo "=================================================================\n";
