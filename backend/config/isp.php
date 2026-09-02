<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ISP Company & Regulatory Settings
    |--------------------------------------------------------------------------
    */
    'company_name' => env('ISP_COMPANY_NAME', 'PT NETPRO TELEKOMUNIKASI INDONESIA'),
    'company_npwp' => env('ISP_COMPANY_NPWP', '01.234.567.8-901.000'),
    'company_address' => env('ISP_COMPANY_ADDRESS', 'Gedung Cyber Lt. 5, Jl. Rasuna Said, Jakarta'),
    'company_phone' => env('ISP_COMPANY_PHONE', '021-5550199'),
    'company_email' => env('ISP_COMPANY_EMAIL', 'billing@netpro.co.id'),

    /*
    |--------------------------------------------------------------------------
    | Tax & Regulatory Constants
    |--------------------------------------------------------------------------
    */
    'ppn_rate' => (float) env('ISP_PPN_RATE', 11.0), // 11% PPN
    'default_ppn_mode' => env('ISP_PPN_MODE', 'include'), // 'include' or 'exclude'
    
    // Kominfo PNBP Regulatory Contributions
    'uso_rate' => (float) env('ISP_USO_RATE', 1.25), // USO Universal Service Obligation 1.25%
    'bhp_rate' => (float) env('ISP_BHP_RATE', 0.50), // BHP Hak Penyelenggaraan Telekomunikasi 0.50%
    
    // Pajak Penghasilan Pasal 23 Sewa / Upstream Bandwidth
    'pph23_rate' => (float) env('ISP_PPH23_RATE', 2.0), // 2% with NPWP (4% without NPWP)

    /*
    |--------------------------------------------------------------------------
    | Billing Engine Rules
    |--------------------------------------------------------------------------
    */
    'prepaid_grace_minutes' => (int) env('ISP_PREPAID_GRACE_MINUTES', 30),
    'prepaid_cycle_days' => (int) env('ISP_PREPAID_CYCLE_DAYS', 30),
    'postpaid_due_day' => (int) env('ISP_POSTPAID_DUE_DAY', 20), // 20th of the month
];
