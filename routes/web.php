<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\StockReceiptController;
use App\Http\Controllers\Admin\StockIssueController;
use App\Http\Controllers\Admin\StockTransferController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\AuditLogController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// توجيه الزوار لصفحة تسجيل الدخول
Route::get('/', fn () => redirect()->route('login'));

// ==========================================
// المسارات المحمية (تتطلب تسجيل دخول وتفعيل بريد)
// ==========================================
Route::middleware(['auth', 'verified'])->group(function () {

    // لوحة التحكم الرئيسية
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ==========================================
    // مجموعة مسارات الإدارة (Admin)
    // ==========================================
    Route::prefix('admin')->name('admin.')->group(function () {
        
        // 1. الإدارة (المستخدمون والأدوار)
        Route::middleware(['permission:manage_users'])->group(function () {
            Route::resource('users', UserController::class);
        });

        Route::middleware(['permission:manage_roles'])->group(function () {
            Route::resource('roles', RoleController::class);
        });

        // 2. البيانات الأساسية
        Route::middleware(['permission:view_warehouses'])->group(function () {
            Route::resource('warehouses', WarehouseController::class);
        });

        Route::middleware(['permission:view_items'])->group(function () {
            Route::resource('categories', CategoryController::class);
            Route::resource('units', UnitController::class);
            Route::resource('items', ItemController::class);
        });

        // 3. سندات الإدخال (الوارد)
        Route::middleware(['permission:view_receipts'])->group(function () {
            Route::resource('receipts', StockReceiptController::class);
            Route::post('receipts/{receipt}/confirm', [StockReceiptController::class, 'confirm'])->name('receipts.confirm');
            Route::post('receipts/{receipt}/cancel', [StockReceiptController::class, 'cancel'])->name('receipts.cancel');
        });

        // 4. سندات الصرف (الصادر)
        Route::middleware(['permission:view_issues'])->group(function () {
            Route::resource('issues', StockIssueController::class);
            Route::post('issues/{issue}/confirm', [StockIssueController::class, 'confirm'])->name('issues.confirm');
            Route::post('issues/{issue}/cancel', [StockIssueController::class, 'cancel'])->name('issues.cancel');
            Route::get('issues/get-balance', [StockIssueController::class, 'getBalance'])->name('issues.get-balance');
        });

        // 5. العمليات — التحويلات
        Route::middleware(['permission:view_transfers'])->group(function () {
            Route::resource('transfers', StockTransferController::class);
            Route::post('transfers/{transfer}/send', [StockTransferController::class, 'send'])->name('transfers.send');
            Route::post('transfers/{transfer}/receive', [StockTransferController::class, 'receive'])->name('transfers.receive');
            Route::post('transfers/{transfer}/cancel', [StockTransferController::class, 'cancel'])->name('transfers.cancel');
        });

        // 6. التقارير
        Route::middleware(['permission:view_reports'])->prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/stock-on-hand', [ReportController::class, 'stockOnHand'])->name('stock-on-hand');
            Route::get('/item-card', [ReportController::class, 'itemCard'])->name('item-card');
            Route::get('/low-stock', [ReportController::class, 'lowStock'])->name('low-stock');
            Route::get('/movement-summary', [ReportController::class, 'movementSummary'])->name('movement-summary');
            
            // تصدير التقارير (يتطلب صلاحية تصدير إضافية)
            Route::middleware(['permission:export_reports'])->group(function () {
                Route::get('/export/low-stock', [ReportController::class, 'exportLowStock'])->name('export.low-stock');
                Route::get('/export/stock-on-hand', [ReportController::class, 'exportStockOnHand'])->name('export.stock-on-hand');
            });
        });

        // 7. سجل العمليات (Audit Logs)
        Route::middleware(['permission:view_roles'])->prefix('audit-logs')->name('audit_logs.')->group(function () {
            Route::get('/', [AuditLogController::class, 'index'])->name('index');
            Route::get('/{log}', [AuditLogController::class, 'show'])->name('show');
        });
        // الطباعة
Route::get('receipts/{receipt}/print', [StockReceiptController::class, 'print'])->name('receipts.print');
Route::get('issues/{issue}/print', [StockIssueController::class, 'print'])->name('issues.print');
Route::get('transfers/{transfer}/print', [StockTransferController::class, 'print'])->name('transfers.print');

    }); // إغلاق مجموعة admin
        // ... (كل مساراتك السابقة تكون هنا) ...

    // ==========================================
    // مسار مؤقت للتحقق من الصلاحيات (للتجربة فقط)
    // ==========================================
    Route::get('/check-permissions', function () {
        $user = \App\Models\User::where('email', 'admin@erp.local')->first();
        
        if (!$user) {
            return '❌ المستخدم غير موجود. يرجى تشغيل الـ Seeder أولاً (تأكد من إصلاح خطأ categories.code).';
        }

        return response()->json([
            'user' => $user->name,
            'email' => $user->email,
            'roles' => $user->getRoleNames(),
            'has_dashboard_permission' => $user->hasPermissionTo('view_dashboard'),
            'total_permissions' => $user->getAllPermissions()->pluck('name'),
        ], 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    });

}); // إغلاق مجموعة auth, verified

require __DIR__.'/auth.php';

