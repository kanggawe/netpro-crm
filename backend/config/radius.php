<?php

return [
    /*
    |--------------------------------------------------------------------------
    | FreeRADIUS & MikroTik NAS Settings
    |--------------------------------------------------------------------------
    */
    'enabled' => env('RADIUS_ENABLED', true),
    'server_host' => env('RADIUS_HOST', '127.0.0.1'),
    'auth_port' => (int) env('RADIUS_AUTH_PORT', 1812),
    'acct_port' => (int) env('RADIUS_ACCT_PORT', 1813),
    'coa_port' => (int) env('RADIUS_COA_PORT', 3799),
    'secret' => env('RADIUS_SECRET', 'testing123-radius-netpro'),
    'nas_identifier' => env('RADIUS_NAS_ID', 'CCR-CORE-HQ-01'),

    /*
    |--------------------------------------------------------------------------
    | Default Fallback IP Pools
    |--------------------------------------------------------------------------
    */
    'default_framed_pool' => env('RADIUS_DEFAULT_POOL', 'POOL_HOME_DYNAMIC'),
    'isolated_framed_pool' => env('RADIUS_ISOLATED_POOL', 'POOL_ISOLATED_SUSPEND'),
];
