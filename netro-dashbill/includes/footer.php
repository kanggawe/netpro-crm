<?php
/**
 * Common Footer Template
 */
?>
                <?php if (!in_array($currentFile, ['invoice.php', 'kwitansi.php'])): ?>
                <!-- Official Enterprise Print Sign-Off Footer (Hanya muncul saat dicetak/print) -->
                <div class="global-print-footer print-only print-sign-off hidden mt-6 pt-4 border-t border-slate-300">
                    <div class="grid grid-cols-3 gap-4 text-center text-xs" style="display: flex !important; justify-content: space-between !important; border: none !important;">
                        <div style="border: none !important; padding: 0 !important;">
                            <span class="text-slate-500 block mb-8 text-[11px]">Disiapkan Oleh,</span>
                            <strong class="text-slate-900 font-bold block underline text-xs"><?= htmlspecialchars(auth_user()['full_name'] ?? 'Staff Operasional') ?></strong>
                            <span class="text-[9px] text-slate-500 font-mono"><?= htmlspecialchars(auth_user()['role'] ?? 'Operator') ?></span>
                        </div>
                        <div style="border: none !important; padding: 0 !important;">
                            <span class="text-slate-500 block mb-8 text-[11px]">Diverifikasi Oleh,</span>
                            <strong class="text-slate-900 font-bold block underline text-xs">Ahmad Faisal, S.T.</strong>
                            <span class="text-[9px] text-slate-500 font-mono">Head of NOC & Operations</span>
                        </div>
                        <div style="border: none !important; padding: 0 !important;">
                            <span class="text-slate-500 block mb-8 text-[11px]">Disetujui Oleh,</span>
                            <strong class="text-slate-900 font-bold block underline text-xs">Muhammad Ibrahim</strong>
                            <span class="text-[9px] text-slate-500 font-mono">Direktur Utama & CEO</span>
                        </div>
                    </div>
                    <div class="mt-4 pt-2 border-t border-slate-200 text-center text-[8.5px] text-slate-400 font-mono">
                        Dokumen ini dicetak otomatis dari NETPRO CRM & Billing Management OS. Hak Cipta Dilindungi Undang-Undang.
                    </div>
                </div>
                <?php endif; ?>

            </div> <!-- End of content-viewport -->
        </main>
    </div> <!-- End of main flex container -->

    <!-- Quick Register Modal Component -->
    <?php require_once __DIR__ . '/modal_reg.php'; ?>

    <!-- System Toast Notification Box -->
    <div id="toastBox" class="fixed bottom-5 right-5 z-50 bg-slate-950 text-white border border-slate-800 rounded-xl px-5 py-4 shadow-2xl text-xs space-y-1 hidden transform translate-y-10 transition duration-300">
        <h4 id="toast-title" class="font-bold flex items-center gap-1.5 text-blue-400">
            <i class="fa-solid fa-circle-check"></i> Notifikasi Sistem
        </h4>
        <p id="toast-desc" class="text-slate-400">Aksi berhasil diproses.</p>
    </div>

    <!-- Global Application Scripts from assets/js -->
    <script src="<?= asset_url('js/ppn.js') ?>"></script>
    <script src="<?= asset_url('js/app.js') ?>"></script>
</body>
</html>
