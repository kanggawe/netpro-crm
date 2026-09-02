<?php
/**
 * Automated Verification Script: Navigation Routes & File Existence
 * NETPRO CRM (ISP Management OS)
 */
require_once __DIR__ . '/../config/app.php';

echo "=======================================================\n";
echo "🔍 VERIFIKASI KONSISTENSI NAVIGASI & FILE EXISTENCE\n";
echo "=======================================================\n\n";

$passCount = 0;
$failCount = 0;

// Read all items from sidebar.php
$sidebarFile = __DIR__ . '/../includes/sidebar.php';
$sidebarContent = file_get_contents($sidebarFile);

preg_match_all("/'url'\s*=>\s*'([^']+)'/", $sidebarContent, $matches);
$urls = array_unique($matches[1] ?? []);

echo "Ditemukan " . count($urls) . " rute navigasi terdaftar di sidebar.\n\n";

foreach ($urls as $url) {
    $filePath = __DIR__ . '/../' . $url;
    if (file_exists($filePath)) {
        echo "  ✅ [PASS] Berkas ditemukan: $url\n";
        $passCount++;
    } else {
        echo "  ❌ [FAIL] BERKAS TIDAK DITEMUKAN (404): $url ($filePath)\n";
        $failCount++;
    }
}

echo "\n=======================================================\n";
echo "📊 HASIL VERIFIKASI: $passCount RUTE VALID, $failCount BROKEN LINKS\n";
echo "=======================================================\n";
