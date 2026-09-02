<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BillingController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\FinanceController;
use App\Http\Controllers\Api\V1\HrController;
use App\Http\Controllers\Api\V1\InventoryController;
use App\Http\Controllers\Api\V1\NocController;
use App\Http\Controllers\Api\V1\OAuthController;
use App\Http\Controllers\Api\V1\PackageController;
use App\Http\Controllers\Api\V1\PayrollController;
use App\Http\Controllers\Api\V1\RadiusController;
use App\Http\Controllers\Api\V1\SettingController;
use App\Http\Controllers\Api\V1\TicketController;
use App\Http\Controllers\Api\V1\WorkOrderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| NETPRO CRM RESTful API Routes (v1)
|--------------------------------------------------------------------------
| Full Action & Module Mapping (100% Mirroring v2/netro-dashbill Architecture)
*/

Route::prefix('v1')->group(function () {
    
    // ==========================================
    // 0. PUBLIC & AUTHENTICATION ENDPOINTS
    // ==========================================
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::get('/tax/simulation', [BillingController::class, 'calculateTaxSimulation']);

    // OAuth 2.0 SSO Public Endpoints
    Route::get('/auth/oauth/{provider}/redirect', [OAuthController::class, 'redirect']);
    Route::get('/auth/oauth/{provider}/callback', [OAuthController::class, 'callback']);
    Route::post('/auth/oauth/{provider}/callback', [OAuthController::class, 'callback']);

    // ==========================================
    // PROTECTED API ENDPOINTS (SANCTUM GUARD)
    // ==========================================
    Route::middleware('auth:sanctum')->group(function () {
        
        // ------------------------------------------
        // USER PROFILE & 2FA TOTP & OAUTH SOCIAL LINKING
        // ------------------------------------------
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/update-profile', [AuthController::class, 'updateProfile']);
        Route::post('/auth/update-password', [AuthController::class, 'updatePassword']);
        Route::post('/auth/toggle-2fa', [AuthController::class, 'toggle2fa']);
        Route::post('/auth/test-totp-code', [AuthController::class, 'testTotpCode']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        // OAuth Account Linking Management
        Route::get('/auth/oauth/linked-accounts', [OAuthController::class, 'linkedAccounts']);
        Route::post('/auth/oauth/{provider}/link', [OAuthController::class, 'link']);
        Route::delete('/auth/oauth/{provider}/unlink', [OAuthController::class, 'unlink']);
        


        // ------------------------------------------
        // 1. CRM & PELANGGAN (10 SUBMODULES)
        // ------------------------------------------
        Route::get('/customers', [CustomerController::class, 'index']);
        Route::post('/customers', [CustomerController::class, 'store']);
        Route::get('/customers/export', [CustomerController::class, 'export']);
        Route::post('/customers/import', [CustomerController::class, 'import']);
        Route::get('/customers/{id}', [CustomerController::class, 'show']);
        Route::put('/customers/{id}', [CustomerController::class, 'update']);
        Route::delete('/customers/{id}', [CustomerController::class, 'destroy']);
        Route::post('/customers/{id}/set-online', [CustomerController::class, 'setOnline']);
        Route::post('/customers/{id}/isolate', [CustomerController::class, 'isolate']);
        Route::post('/customers/{id}/quick-topup', [CustomerController::class, 'quickTopup']);

        Route::get('/packages', [PackageController::class, 'index']);
        Route::post('/packages', [PackageController::class, 'store']);
        Route::put('/packages/{id}', [PackageController::class, 'update']);
        Route::delete('/packages/{id}', [PackageController::class, 'destroy']);
        Route::get('/addons', [PackageController::class, 'addons']);
        Route::get('/promos', [PackageController::class, 'promos']);

        Route::get('/surveys', [WorkOrderController::class, 'surveys']);
        Route::post('/surveys', [WorkOrderController::class, 'storeSurvey']);
        Route::get('/work-orders', [WorkOrderController::class, 'workOrders']);
        Route::post('/work-orders', [WorkOrderController::class, 'storeWorkOrder']);

        // ------------------------------------------
        // 2. NOC & NETWORK OPS (9 SUBMODULES)
        // ------------------------------------------
        Route::get('/noc/monitoring', [NocController::class, 'monitoring']);
        Route::get('/noc/topology', [NocController::class, 'topology']);
        Route::get('/noc/olts', [NocController::class, 'olts']);
        Route::get('/noc/otbs', [NocController::class, 'otbs']);
        Route::get('/noc/odcs', [NocController::class, 'odcs']);
        Route::get('/noc/odps', [NocController::class, 'odps']);
        Route::get('/noc/onus', [NocController::class, 'onus']);
        Route::get('/noc/mikrotik', [NocController::class, 'mikrotik']);
        Route::get('/noc/outages', [NocController::class, 'outages']);
        Route::post('/noc/outages', [NocController::class, 'storeOutage']);
        Route::post('/noc/outages/{id}/resolve', [NocController::class, 'resolveOutage']);

        // ------------------------------------------
        // 3. TICKETING & CSAT (2 SUBMODULES)
        // ------------------------------------------
        Route::get('/tickets', [TicketController::class, 'index']);
        Route::post('/tickets', [TicketController::class, 'store']);
        Route::post('/tickets/{id}/resolve', [TicketController::class, 'resolve']);

        // ------------------------------------------
        // 4. BILLING & TAGIHAN (6 SUBMODULES)
        // ------------------------------------------
        Route::get('/invoices', [BillingController::class, 'index']);
        Route::get('/invoices/{id}', [BillingController::class, 'show']);
        Route::post('/invoices/generate-customer', [BillingController::class, 'generateForCustomer']);
        Route::post('/invoices/generate-monthly', [BillingController::class, 'generateMonthly']);
        Route::post('/invoices/{id}/pay', [BillingController::class, 'pay']);
        Route::post('/invoices/{id}/send-reminder', [BillingController::class, 'sendReminder']);

        // ------------------------------------------
        // 5. RADIUS SERVER ENGINE (6 SUBMODULES)
        // ------------------------------------------
        Route::get('/radius/telemetry', [RadiusController::class, 'telemetry']);
        Route::get('/radius/users', [RadiusController::class, 'users']);
        Route::post('/radius/users', [RadiusController::class, 'storeUser']);
        Route::delete('/radius/users/{id}', [RadiusController::class, 'destroyUser']);
        Route::get('/radius/nas', [RadiusController::class, 'nasList']);
        Route::post('/radius/nas', [RadiusController::class, 'storeNas']);
        Route::delete('/radius/nas/{id}', [RadiusController::class, 'destroyNas']);
        Route::get('/radius/profiles', [RadiusController::class, 'profiles']);
        Route::get('/radius/vouchers', [RadiusController::class, 'vouchers']);
        Route::get('/radius/reports', [RadiusController::class, 'reports']);
        Route::post('/radius/disconnect', [RadiusController::class, 'disconnect']);
        Route::post('/radius/probe', [RadiusController::class, 'probe']);

        // ------------------------------------------
        // 6. MARKETING & SALES (3 SUBMODULES)
        // ------------------------------------------
        Route::get('/leads', [InventoryController::class, 'leads']);

        // ------------------------------------------
        // 7. KEUANGAN & AKUNTANSI PSAK (5 SUBMODULES)
        // ------------------------------------------
        Route::get('/finance/coa', [FinanceController::class, 'coaList']);
        Route::get('/finance/journals', [FinanceController::class, 'journals']);
        Route::get('/finance/taxes', [FinanceController::class, 'taxes']);
        Route::post('/finance/taxes', [FinanceController::class, 'storeTax']);
        Route::get('/finance/opex', [FinanceController::class, 'opex']);
        Route::post('/finance/opex', [FinanceController::class, 'storeOpex']);
        Route::get('/finance/cashflow', [FinanceController::class, 'cashflow']);
        Route::get('/finance/regulatory-summary', [FinanceController::class, 'regulatorySummary']);

        // ------------------------------------------
        // 8. HR, PRESENSI & PAYROLL THP (7 SUBMODULES)
        // ------------------------------------------
        Route::get('/hr/employees', [HrController::class, 'employees']);
        Route::post('/hr/employees', [HrController::class, 'storeEmployee']);
        Route::get('/hr/attendances', [HrController::class, 'attendances']);
        Route::post('/hr/clock-in', [HrController::class, 'clockIn']);
        Route::get('/hr/leaves', [HrController::class, 'leaves']);
        Route::post('/hr/leaves', [HrController::class, 'storeLeave']);
        Route::get('/hr/kpi', [HrController::class, 'kpiList']);

        Route::get('/payroll/records', [PayrollController::class, 'records']);
        Route::post('/payroll/generate', [PayrollController::class, 'generatePayroll']);
        Route::get('/payroll/bonus-claims', [PayrollController::class, 'bonusClaims']);
        Route::post('/payroll/bonus-claims/{id}/approve', [PayrollController::class, 'approveBonusClaim']);

        // ------------------------------------------
        // 9. INVENTORY & CABANG (4 SUBMODULES)
        // ------------------------------------------
        Route::get('/inventory/items', [InventoryController::class, 'items']);
        Route::post('/inventory/items', [InventoryController::class, 'storeItem']);
        Route::post('/inventory/items/{id}/adjust-stock', [InventoryController::class, 'adjustStock']);
        Route::get('/branches', [InventoryController::class, 'branches']);

        // ------------------------------------------
        // 10. PENGATURAN SISTEM & AUDIT (11 SUBMODULES)
        // ------------------------------------------
        Route::get('/settings', [SettingController::class, 'index']);
        Route::post('/settings', [SettingController::class, 'update']);
        Route::get('/audit-logs', [SettingController::class, 'auditLogs']);
        Route::get('/backups', [SettingController::class, 'backups']);
        Route::post('/backups', [SettingController::class, 'createBackup']);
        Route::get('/backups/{id}/download', [SettingController::class, 'downloadBackup']);
        Route::delete('/backups/{id}', [SettingController::class, 'destroyBackup']);
    });
});
