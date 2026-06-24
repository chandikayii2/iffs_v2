<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\Auth\AuthController;
use App\Http\Controllers\V1\Grn\GrnController;
use App\Http\Controllers\V1\IssueNote\IssueNoteController;
use App\Http\Controllers\V1\Product\ProductController;
use App\Http\Controllers\V1\Purchase\PurchaseOrderController;
use App\Http\Controllers\V1\Stock\StockController;
use App\Http\Controllers\V1\Supplier\SupplierController;
use App\Http\Controllers\V1\User\UserController;
use App\Http\Controllers\V1\User\UserRoleController;
use App\Http\Controllers\V1\User\UserRolePermisionController;
use App\Http\Controllers\ProductReportController;

// Tire System Controllers
use App\Http\Controllers\Tire\TireDashboardController;
use App\Http\Controllers\Tire\TireInventoryController;
use App\Http\Controllers\Tire\VehicleAllocationController;
use App\Http\Controllers\Tire\RefillingController;
use App\Http\Controllers\Tire\TireIssueController;
use App\Http\Controllers\Tire\TireScrapController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public routes (no authentication required)
Route::get('/login', [AuthController::class, 'loginView'])->name('login');
Route::redirect('/', '/login');
Route::post('login-check', [AuthController::class, 'loginCheck'])->name('login-check');

// Routes requiring authentication
Route::group(['middleware' => 'AdminAuth1'], function () {

    // Logout route
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');
    
    // Welcome page for system selection (after login)
    Route::get('/welcome', function() {
        return view('welcome');
    })->name('welcome');

    // ============================================================
    // IFFS SYSTEM ROUTES (Original System)
    // ============================================================
    Route::group(['prefix' => 'admin'], function () {

        // Product Reports
        Route::prefix('reports/product')->group(function() {
            Route::get('/', [ProductReportController::class, 'showForm'])->name('reports.product.form');
            Route::post('/generate', [ProductReportController::class, 'generateReport'])->name('reports.product.generate');
            Route::get('/download', [ProductReportController::class, 'generateReport'])->name('reports.product.download');
        });

        // Dashboard
        Route::get('dashboard', [AuthController::class, 'dashboardView'])->name('dashboard');

        // Supplier Management
        Route::group(['prefix' => 'supplier'], function () {
            Route::post('/create', [SupplierController::class, 'create'])->name('supplier-create');
            Route::get('/', [SupplierController::class, 'getAll'])->name('supplier-get-all');
            Route::get('/edit/{supplierId}', [SupplierController::class, 'edit']);
            Route::post('/update', [SupplierController::class, 'update']);
            Route::delete('/delete/{productId}', [ProductController::class, 'delete']);
        });

        // Products Management
        Route::group(['prefix' => 'products'], function () {
            Route::post('/create', [ProductController::class, 'create'])->name('product-create');
            Route::get('/', [ProductController::class, 'getAll'])->name('product-get-all');
            Route::get('/edit/{productId}', [ProductController::class, 'edit']);
            Route::post('/update', [ProductController::class, 'update']);
        });

        // Stock Management
        Route::group(['prefix' => 'stock'], function () {
            Route::get('/', [StockController::class, 'getAll'])->name('stock-get-all');
            Route::get('/generate-pdf', [StockController::class, 'genaratePdf']);
        });

        // Users Management
        Route::group(['prefix' => 'users'], function () {
            Route::get('/', [UserController::class, 'getAll'])->name('user-get-all');
            Route::get('/edit/{userId}', [UserController::class, 'edit']);
            Route::post('/update', [UserController::class, 'update']);
            Route::post('/create', [UserController::class, 'create'])->name('user-create');
            Route::post('/password-update', [UserController::class, 'passwordUpdate']);

            // User Roles
            Route::group(['prefix' => 'role'], function () {
                Route::get('/', [UserRoleController::class, 'getAllUserRole'])->name('role-all-get-all');
                Route::post('/create', [UserRoleController::class, 'create'])->name('user-role-create');
            });

            // User Permissions
            Route::group(['prefix' => 'permission'], function () {
                Route::get('/', [UserRolePermisionController::class, 'getAllUserPermissions'])->name('get-all-user-permissions');
            });

            // Role Permissions
            Route::group(['prefix' => 'role_permission'], function () {
                Route::get('/', [UserRolePermisionController::class, 'getAllUserRolePermissions'])->name('get-all-user-role-permissions');
                Route::post('/create', [UserRolePermisionController::class, 'create'])->name('role-permission-create');
                Route::post('/check-role-exists', [UserRolePermisionController::class, 'checkRoleExists'])->name('check-role-exists');
                Route::get('/edit/{role_permission_id}', [UserRolePermisionController::class, 'edit']);
                Route::post('/update', [UserRolePermisionController::class, 'update']);
            });
        });

        // Purchase Order Management
        Route::group(['prefix' => 'purchase-order'], function () {
            Route::get('/', [PurchaseOrderController::class, 'getAll'])->name('purchase-orders');
            Route::get('/create', [PurchaseOrderController::class, 'createView'])->name('create-purchase-order-view');
            Route::get('/get-product-data/{productId}', [PurchaseOrderController::class, 'getProductData']);
            Route::post('/save-purchase-order', [PurchaseOrderController::class, 'savePurchaseOrder'])->name('save-purchase-order');
            Route::get('/po_products_view/{purchase_order_id}', [PurchaseOrderController::class, 'purchaseOrderProductView']);
            Route::get('/edit/{purchase_order_id}', [PurchaseOrderController::class, 'edit']);
            Route::post('/update', [PurchaseOrderController::class, 'updatePurchaseOrder'])->name('update-purchase-order');
            Route::delete('/delete-po-product/{po_product_id}', [PurchaseOrderController::class, 'deletePoProduct']);
            Route::delete('/delete-purchase-order/{purchase_order_id}', [PurchaseOrderController::class, 'deletePurchaseOrder']);
            Route::get('/generate-pdf/{purchase_order_id}', [PurchaseOrderController::class, 'genaratePdf']);
        });

        // Goods Received Note (GRN) Management
        Route::group(['prefix' => 'grn'], function () {
            Route::get('/', [GrnController::class, 'getAll'])->name('get-all-grns');
            Route::get('/create', [GrnController::class, 'createView'])->name('create-grn-view');
            Route::get('/get-purchase-order-products/{purchaseOrderId}', [GrnController::class, 'getPurchaseOrderProducts']);
            Route::post('/create-grn', [GrnController::class, 'createGrn'])->name('create-grn');
            Route::get('/grn_products_view/{grn_id}', [GrnController::class, 'grnProductsView']);
            Route::get('/edit/{grn_id}', [GrnController::class, 'edit']);
            Route::delete('/delete-grn/{grn_id}', [GrnController::class, 'deleteGrn']);
            Route::get('/generate-pdf/{grn_id}', [GrnController::class, 'genaratePdf']);
        });

        // Issue Note Management
        Route::group(['prefix' => 'issue-note'], function () {
            Route::get('/', [IssueNoteController::class, 'getAll'])->name('get-all-issue-note');
            Route::get('/create', [IssueNoteController::class, 'createView'])->name('create-issue-note-view');
            Route::get('/get-product-data/{productId}', [IssueNoteController::class, 'getProductData']);
            Route::post('/create-issue-note', [IssueNoteController::class, 'createIssueNote'])->name('create-issue-note');
            Route::delete('/delete-issue-note/{issue_note_id}', [IssueNoteController::class, 'deleteIssueNote']);
            Route::get('/issue_note_products_view/{issue_note_id}', [IssueNoteController::class, 'issueNoteProductsView']);
            Route::get('/generate-pdf/{issue_note_id}', [IssueNoteController::class, 'genaratePdf']);
        });
    });

    // ============================================================
    // TIRE LIFECYCLE MANAGEMENT SYSTEM ROUTES
    // ============================================================
    Route::prefix('tire')->group(function () {
        // Tire Issue Management Routes
// ============================================
Route::prefix('issue')->group(function () {
    Route::get('/', [TireIssueController::class, 'index'])->name('tire.issue.index');
    Route::get('/create', [TireIssueController::class, 'create'])->name('tire.issue.create');
    Route::post('/store', [TireIssueController::class, 'store'])->name('tire.issue.store');
    Route::get('/{id}', [TireIssueController::class, 'show'])->name('tire.issue.show');
    Route::get('/{id}/edit', [TireIssueController::class, 'edit'])->name('tire.issue.edit');
    Route::put('/{id}', [TireIssueController::class, 'update'])->name('tire.issue.update');
    Route::delete('/{id}', [TireIssueController::class, 'delete'])->name('tire.issue.delete');
    Route::get('/get-tire-data/{tireId}', [TireIssueController::class, 'getTireData'])->name('tire.issue.get-tire-data');
    Route::get('/{id}/pdf', [TireIssueController::class, 'generatePdf'])->name('tire.issue.pdf');
    Route::get('/{id}/gate-pass', [TireIssueController::class, 'generateGatePass'])->name('tire.issue.gate-pass');
});
        // ============================================
        // Tire Dashboard
        // ============================================
        Route::get('/dashboard', [TireDashboardController::class, 'index'])->name('tire.dashboard');
        Route::get('/dashboard/stats', [TireDashboardController::class, 'getStats'])->name('tire.dashboard.stats');
        Route::get('/alerts', [TireDashboardController::class, 'getAlerts'])->name('tire.alerts');
        
        // ============================================
        // Tire Inventory Management
        // ============================================
       Route::prefix('inventory')->group(function () {
    Route::get('/', [TireInventoryController::class, 'index'])->name('tire.inventory.index');
    Route::get('/create', [TireInventoryController::class, 'create'])->name('tire.inventory.create');
    Route::post('/store', [TireInventoryController::class, 'store'])->name('tire.inventory.store');
    Route::get('/{id}', [TireInventoryController::class, 'show'])->name('tire.inventory.show');
    Route::get('/edit/{id}', [TireInventoryController::class, 'edit'])->name('tire.inventory.edit');
    Route::put('/update/{id}', [TireInventoryController::class, 'update'])->name('tire.inventory.update');
    Route::delete('/delete/{id}', [TireInventoryController::class, 'delete'])->name('tire.inventory.delete');
    Route::get('/allocate-to-vehicle/{id}', [TireInventoryController::class, 'allocateToVehicle'])->name('tire.inventory.allocate-to-vehicle');
    Route::post('/allocate-to-vehicle/{id}', [TireInventoryController::class, 'processAllocateToVehicle'])->name('tire.inventory.allocate-to-vehicle.process');
    Route::get('/send-refill/{id}', [TireInventoryController::class, 'sendForRefill'])->name('tire.inventory.send-refill');
    Route::get('/gate-pass/{id}', [TireInventoryController::class, 'generateGatePass'])->name('tire.inventory.gate-pass');
    Route::get('/remove-from-vehicle/{tireId}', [TireInventoryController::class, 'removeTireFromVehicle'])->name('tire.inventory.remove-from-vehicle');
    Route::post('/process-removal/{tireId}', [TireInventoryController::class, 'processTireRemoval'])->name('tire.inventory.process-removal');
    Route::post('/update-refill-count/{tireId}', [TireInventoryController::class, 'updateRefillCount'])->name('tire.inventory.update-refill-count');
    Route::get('/export/excel', [TireInventoryController::class, 'exportExcel'])->name('tire.inventory.export.excel');
    Route::get('/export/pdf', [TireInventoryController::class, 'exportPdf'])->name('tire.inventory.export.pdf');
    Route::get('/api/brands', [TireInventoryController::class, 'getBrands'])->name('tire.inventory.api.brands');
    Route::get('/api/sizes', [TireInventoryController::class, 'getSizes'])->name('tire.inventory.api.sizes');
    Route::get('/api/types', [TireInventoryController::class, 'getTypes'])->name('tire.inventory.api.types');
});
        
        // ============================================
        // Vehicle and Allocation Management
        // ============================================
        Route::prefix('vehicles')->group(function () {
            Route::get('/', [VehicleAllocationController::class, 'index'])->name('tire.vehicles.index');
            Route::get('/create', [VehicleAllocationController::class, 'createVehicle'])->name('tire.vehicles.create');
            Route::post('/store', [VehicleAllocationController::class, 'storeVehicle'])->name('tire.vehicles.store');
            Route::get('/{vehicleId}', [VehicleAllocationController::class, 'showVehicle'])->name('tire.vehicles.show');
            Route::get('/edit/{vehicleId}', [VehicleAllocationController::class, 'editVehicle'])->name('tire.vehicles.edit');
            Route::put('/update/{vehicleId}', [VehicleAllocationController::class, 'updateVehicle'])->name('tire.vehicles.update');
            Route::delete('/delete/{vehicleId}', [VehicleAllocationController::class, 'deleteVehicle'])->name('tire.vehicles.delete');
            Route::get('/{vehicleId}/allocate', [VehicleAllocationController::class, 'allocateForm'])->name('tire.vehicles.allocate');
            Route::post('/{vehicleId}/allocate', [VehicleAllocationController::class, 'allocateTires'])->name('tire.vehicles.allocate.store');
            Route::get('/remove/{allocationId}', [VehicleAllocationController::class, 'removeTire'])->name('tire.vehicles.remove');
            Route::post('/remove/{allocationId}', [VehicleAllocationController::class, 'processRemoval'])->name('tire.vehicles.remove.process');
            Route::get('/{vehicleId}/history', [VehicleAllocationController::class, 'vehicleHistory'])->name('tire.vehicles.history');
            Route::get('/reports/mileage-summary', [VehicleAllocationController::class, 'mileageReport'])->name('tire.vehicles.report.mileage');
            Route::post('/{vehicleId}/update-mileage', [VehicleAllocationController::class, 'updateMileage'])->name('tire.vehicles.update-mileage');
            Route::get('/api/available-tires', [VehicleAllocationController::class, 'getAvailableTires'])->name('tire.vehicles.api.available-tires');
            Route::get('/api/vehicle/{vehicleId}/current-tires', [VehicleAllocationController::class, 'getCurrentTires'])->name('tire.vehicles.api.current-tires');
        });
        
       // ============================================
// Refilling (Retreading) Management
// ============================================
Route::prefix('refilling')->group(function () {
    // Orders
    Route::get('/', [RefillingController::class, 'index'])->name('tire.refilling.index');
    Route::get('/create', [RefillingController::class, 'createOrder'])->name('tire.refilling.create');
    Route::post('/store', [RefillingController::class, 'storeOrder'])->name('tire.refilling.store');
    Route::get('/{orderId}', [RefillingController::class, 'showOrder'])->name('tire.refilling.show');
    Route::get('/{orderId}/receive', [RefillingController::class, 'receiveOrder'])->name('tire.refilling.receive');
    Route::post('/{orderId}/receive', [RefillingController::class, 'processReceipt'])->name('tire.refilling.receive.process');
    Route::get('/edit/{orderId}', [RefillingController::class, 'editOrder'])->name('tire.refilling.edit');
    Route::put('/update/{orderId}', [RefillingController::class, 'updateOrder'])->name('tire.refilling.update');
    Route::delete('/cancel/{orderId}', [RefillingController::class, 'cancelOrder'])->name('tire.refilling.cancel');
    Route::get('/{orderId}/pdf', [RefillingController::class, 'generatePdf'])->name('tire.refilling.pdf');

    
    // Vendor Management - FIXED ROUTES
    Route::prefix('vendors')->group(function () {
        Route::get('/', [RefillingController::class, 'manageVendors'])->name('tire.refilling.vendors');
        Route::get('/manage', [RefillingController::class, 'manageVendors'])->name('tire.refilling.vendors.manage');
        Route::post('/store', [RefillingController::class, 'storeVendor'])->name('tire.refilling.vendors.store');
        Route::get('/{vendorId}', [RefillingController::class, 'showVendor'])->name('tire.refilling.vendors.show');
        Route::get('/{vendorId}/edit', [RefillingController::class, 'editVendor'])->name('tire.refilling.vendors.edit');
        Route::put('/{vendorId}/update', [RefillingController::class, 'updateVendor'])->name('tire.refilling.vendors.update');
        Route::delete('/{vendorId}/delete', [RefillingController::class, 'deleteVendor'])->name('tire.refilling.vendors.delete');
    });
    
    // Reports
    Route::get('/reports/summary', [RefillingController::class, 'refillingReport'])->name('tire.refilling.reports.summary');
    Route::get('/reports/vendor-performance', [RefillingController::class, 'vendorPerformance'])->name('tire.refilling.reports.vendor');
    
    // API endpoints
    Route::get('/api/available-for-refill', [RefillingController::class, 'getAvailableForRefill'])->name('tire.refilling.api.available');
});
        
        // ============================================
        // Scrap and Disposal Management
        // ============================================
        Route::prefix('scrap')->group(function () {
            Route::get('/', [TireScrapController::class, 'index'])->name('tire.scrap.index');
            Route::get('/tire/{tireId}', [TireScrapController::class, 'scrapTire'])->name('tire.scrap.create');
            Route::post('/tire/{tireId}', [TireScrapController::class, 'processScrap'])->name('tire.scrap.process');
            Route::post('/bulk', [TireScrapController::class, 'bulkScrap'])->name('tire.scrap.bulk');
            Route::get('/report/generate', [TireScrapController::class, 'scrapReport'])->name('tire.scrap.report');
            Route::get('/report/download/pdf', [TireScrapController::class, 'downloadScrapReport'])->name('tire.scrap.report.pdf');
            Route::get('/report/download/excel', [TireScrapController::class, 'downloadScrapReportExcel'])->name('tire.scrap.report.excel');
            Route::post('/restore/{scrapId}', [TireScrapController::class, 'restoreTire'])->name('tire.scrap.restore');
            Route::get('/disposal-methods', [TireScrapController::class, 'disposalMethods'])->name('tire.scrap.disposal-methods');
            Route::post('/disposal-methods/store', [TireScrapController::class, 'storeDisposalMethod'])->name('tire.scrap.disposal-methods.store');
            Route::get('/analytics', [TireScrapController::class, 'scrapAnalytics'])->name('tire.scrap.analytics');
        });
        
        // ============================================
        // Tire Passport (Lifecycle History)
        // ============================================
        Route::prefix('passport')->group(function () {
            Route::get('/search', [TireInventoryController::class, 'searchPassport'])->name('tire.passport.search');
            Route::post('/lookup', [TireInventoryController::class, 'lookupTire'])->name('tire.passport.lookup');
            Route::get('/{tireId}/pdf', [TireInventoryController::class, 'generatePassportPdf'])->name('tire.passport.pdf');
            Route::get('/{tireId}/print', [TireInventoryController::class, 'printPassport'])->name('tire.passport.print');
        });
        
        // ============================================
        // Tire Reports and Analytics
        // ============================================
        Route::prefix('reports')->group(function () {
            Route::get('/analytics', [TireDashboardController::class, 'analytics'])->name('tire.reports.analytics');
            Route::get('/tire-life', [TireInventoryController::class, 'tireLifeReport'])->name('tire.reports.tire-life');
            Route::get('/usage-stats', [VehicleAllocationController::class, 'usageStatistics'])->name('tire.reports.usage');
            Route::get('/cost-analysis', [RefillingController::class, 'costAnalysis'])->name('tire.reports.cost');
            Route::post('/custom-range', [TireDashboardController::class, 'customRangeReport'])->name('tire.reports.custom-range');
        });
        
        // ============================================
        // API Routes for AJAX functionality
        // ============================================
        Route::prefix('api')->group(function () {
            Route::get('/tire/by-serial/{serialNumber}', [TireInventoryController::class, 'getBySerialNumber'])->name('tire.api.by-serial');
            Route::get('/available-tires', [TireInventoryController::class, 'getAvailableTires'])->name('tire.api.available-tires');
            Route::get('/vehicle/{vehicleId}/current-tires', [VehicleAllocationController::class, 'getCurrentTiresApi'])->name('tire.api.vehicle-tires');
            Route::post('/validate-serial', [TireInventoryController::class, 'validateSerialNumber'])->name('tire.api.validate-serial');
            Route::get('/tire/{tireId}/summary', [TireInventoryController::class, 'getLifecycleSummary'])->name('tire.api.lifecycle-summary');
            Route::get('/monthly-activity', [TireDashboardController::class, 'getMonthlyActivity'])->name('tire.api.monthly-activity');
        });
    });
});