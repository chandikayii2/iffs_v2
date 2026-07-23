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

// Tyre System Controllers
use App\Http\Controllers\Tyre\TyreDashboardController;
use App\Http\Controllers\Tyre\TyreInventoryController;
use App\Http\Controllers\Tyre\VehicleAllocationController;
use App\Http\Controllers\Tyre\RefillingController;
use App\Http\Controllers\Tyre\TyreIssueController;
use App\Http\Controllers\Tyre\TyreScrapController;

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
    Route::prefix('tyre')->group(function () {
        // Tyre Issue Management Routes
// ============================================
Route::prefix('issue')->group(function () {
    Route::get('/', [TyreIssueController::class, 'index'])->name('tyre.issue.index');
    Route::get('/create', [TyreIssueController::class, 'create'])->name('tyre.issue.create');
    Route::post('/store', [TyreIssueController::class, 'store'])->name('tyre.issue.store');
    Route::get('/{id}', [TyreIssueController::class, 'show'])->name('tyre.issue.show');
    Route::get('/{id}/edit', [TyreIssueController::class, 'edit'])->name('tyre.issue.edit');
    Route::put('/{id}', [TyreIssueController::class, 'update'])->name('tyre.issue.update');
    Route::delete('/{id}', [TyreIssueController::class, 'delete'])->name('tyre.issue.delete');
    Route::get('/get-tyre-data/{tyreId}', [TyreIssueController::class, 'getTyreData'])->name('tyre.issue.get-tyre-data');
    Route::get('/{id}/pdf', [TyreIssueController::class, 'generatePdf'])->name('tyre.issue.pdf');
    Route::get('/{id}/gate-pass', [TyreIssueController::class, 'generateGatePass'])->name('tyre.issue.gate-pass');
});
        // ============================================
        // Tyre Dashboard
        // ============================================
        Route::get('/dashboard', [TyreDashboardController::class, 'index'])->name('tyre.dashboard');
        Route::get('/breakdown', [TyreDashboardController::class, 'breakdown'])->name('tyre.breakdown');
        Route::get('/category/{type}', [TyreDashboardController::class, 'categoryTyres'])->name('tyre.category.list');
        Route::get('/dashboard/stats', [TyreDashboardController::class, 'getStats'])->name('tyre.dashboard.stats');
        Route::get('/alerts', [TyreDashboardController::class, 'getAlerts'])->name('tyre.alerts');
        
        // ============================================
        // Tyre Inventory Management
        // ============================================
       Route::prefix('inventory')->group(function () {
    Route::get('/', [TyreInventoryController::class, 'index'])->name('tyre.inventory.index');
    Route::get('/create', [TyreInventoryController::class, 'create'])->name('tyre.inventory.create');
    Route::post('/store', [TyreInventoryController::class, 'store'])->name('tyre.inventory.store');
    Route::get('/{id}', [TyreInventoryController::class, 'show'])->name('tyre.inventory.show');
    Route::get('/edit/{id}', [TyreInventoryController::class, 'edit'])->name('tyre.inventory.edit');
    Route::put('/update/{id}', [TyreInventoryController::class, 'update'])->name('tyre.inventory.update');
    Route::delete('/delete/{id}', [TyreInventoryController::class, 'delete'])->name('tyre.inventory.delete');
    Route::get('/allocate-to-vehicle/{id}', [TyreInventoryController::class, 'allocateToVehicle'])->name('tyre.inventory.allocate-to-vehicle');
    Route::post('/allocate-to-vehicle/{id}', [TyreInventoryController::class, 'processAllocateToVehicle'])->name('tyre.inventory.allocate-to-vehicle.process');
    Route::get('/send-refill/{id}', [TyreInventoryController::class, 'sendForRefill'])->name('tyre.inventory.send-refill');
    Route::get('/gate-pass/{id}', [TyreInventoryController::class, 'generateGatePass'])->name('tyre.inventory.gate-pass');
    Route::get('/remove-from-vehicle/{tyreId}', [TyreInventoryController::class, 'removeTyreFromVehicle'])->name('tyre.inventory.remove-from-vehicle');
    Route::post('/process-removal/{tyreId}', [TyreInventoryController::class, 'processTyreRemoval'])->name('tyre.inventory.process-removal');
    Route::post('/update-refill-count/{tyreId}', [TyreInventoryController::class, 'updateRefillCount'])->name('tyre.inventory.update-refill-count');
    Route::get('/export/excel', [TyreInventoryController::class, 'exportExcel'])->name('tyre.inventory.export.excel');
    Route::get('/export/pdf', [TyreInventoryController::class, 'exportPdf'])->name('tyre.inventory.export.pdf');
    Route::get('/api/brands', [TyreInventoryController::class, 'getBrands'])->name('tyre.inventory.api.brands');
    Route::get('/api/sizes', [TyreInventoryController::class, 'getSizes'])->name('tyre.inventory.api.sizes');
    Route::get('/api/types', [TyreInventoryController::class, 'getTypes'])->name('tyre.inventory.api.types');
});
        
        // ============================================
        // Vehicle and Allocation Management
        // ============================================
        Route::prefix('vehicles')->group(function () {
            Route::get('/', [VehicleAllocationController::class, 'index'])->name('tyre.vehicles.index');
            Route::get('/create', [VehicleAllocationController::class, 'createVehicle'])->name('tyre.vehicles.create');
            Route::post('/store', [VehicleAllocationController::class, 'storeVehicle'])->name('tyre.vehicles.store');
            Route::get('/{vehicleId}', [VehicleAllocationController::class, 'showVehicle'])->name('tyre.vehicles.show');
            Route::get('/edit/{vehicleId}', [VehicleAllocationController::class, 'editVehicle'])->name('tyre.vehicles.edit');
            Route::put('/update/{vehicleId}', [VehicleAllocationController::class, 'updateVehicle'])->name('tyre.vehicles.update');
            Route::delete('/delete/{vehicleId}', [VehicleAllocationController::class, 'deleteVehicle'])->name('tyre.vehicles.delete');
            Route::get('/{vehicleId}/allocate', [VehicleAllocationController::class, 'allocateForm'])->name('tyre.vehicles.allocate');
            Route::post('/{vehicleId}/allocate', [VehicleAllocationController::class, 'allocateTyres'])->name('tyre.vehicles.allocate.store');
            Route::get('/remove/{allocationId}', [VehicleAllocationController::class, 'removeTyre'])->name('tyre.vehicles.remove');
            Route::post('/remove/{allocationId}', [VehicleAllocationController::class, 'processRemoval'])->name('tyre.vehicles.remove.process');
            Route::get('/{vehicleId}/history', [VehicleAllocationController::class, 'vehicleHistory'])->name('tyre.vehicles.history');
            Route::get('/reports/mileage-summary', [VehicleAllocationController::class, 'mileageReport'])->name('tyre.vehicles.report.mileage');
            Route::post('/{vehicleId}/update-mileage', [VehicleAllocationController::class, 'updateMileage'])->name('tyre.vehicles.update-mileage');
            Route::get('/api/available-tyres', [VehicleAllocationController::class, 'getAvailableTyres'])->name('tyre.vehicles.api.available-tyres');
            Route::get('/api/vehicle/{vehicleId}/current-tyres', [VehicleAllocationController::class, 'getCurrentTyres'])->name('tyre.vehicles.api.current-tyres');
        });
        
       // ============================================
// Refilling (Retreading) Management
// ============================================
Route::prefix('refilling')->group(function () {
    // Orders
    Route::get('/', [RefillingController::class, 'index'])->name('tyre.refilling.index');
    Route::get('/create', [RefillingController::class, 'createOrder'])->name('tyre.refilling.create');
    Route::post('/store', [RefillingController::class, 'storeOrder'])->name('tyre.refilling.store');
    Route::get('/{orderId}', [RefillingController::class, 'showOrder'])->name('tyre.refilling.show');
    Route::get('/{orderId}/receive', [RefillingController::class, 'receiveOrder'])->name('tyre.refilling.receive');
    Route::post('/{orderId}/receive', [RefillingController::class, 'processReceipt'])->name('tyre.refilling.receive.process');
    Route::post('/{orderId}/payment', [RefillingController::class, 'recordPayment'])->name('tyre.refilling.payment.store');
    Route::get('/edit/{orderId}', [RefillingController::class, 'editOrder'])->name('tyre.refilling.edit');
    Route::put('/update/{orderId}', [RefillingController::class, 'updateOrder'])->name('tyre.refilling.update');
    Route::delete('/cancel/{orderId}', [RefillingController::class, 'cancelOrder'])->name('tyre.refilling.cancel');
    Route::get('/{orderId}/pdf', [RefillingController::class, 'generatePdf'])->name('tyre.refilling.pdf');

    
    // Vendor Management - FIXED ROUTES
    Route::prefix('vendors')->group(function () {
        Route::get('/', [RefillingController::class, 'manageVendors'])->name('tyre.refilling.vendors');
        Route::get('/manage', [RefillingController::class, 'manageVendors'])->name('tyre.refilling.vendors.manage');
        Route::post('/store', [RefillingController::class, 'storeVendor'])->name('tyre.refilling.vendors.store');
        Route::get('/{vendorId}', [RefillingController::class, 'showVendor'])->name('tyre.refilling.vendors.show');
        Route::get('/{vendorId}/edit', [RefillingController::class, 'editVendor'])->name('tyre.refilling.vendors.edit');
        Route::put('/{vendorId}/update', [RefillingController::class, 'updateVendor'])->name('tyre.refilling.vendors.update');
        Route::delete('/{vendorId}/delete', [RefillingController::class, 'deleteVendor'])->name('tyre.refilling.vendors.delete');
    });
    
    // Reports
    Route::get('/reports/summary', [RefillingController::class, 'refillingReport'])->name('tyre.refilling.reports.summary');
    Route::get('/reports/vendor-performance', [RefillingController::class, 'vendorPerformance'])->name('tyre.refilling.reports.vendor');
    
    // API endpoints
    Route::get('/api/available-for-refill', [RefillingController::class, 'getAvailableForRefill'])->name('tyre.refilling.api.available');
});
        
        // ============================================
        // Scrap and Disposal Management
        // ============================================
        Route::prefix('scrap')->group(function () {
            Route::get('/', [TyreScrapController::class, 'index'])->name('tyre.scrap.index');
            Route::get('/export-pdf', [TyreScrapController::class, 'exportPdf'])->name('tyre.scrap.export-pdf');
            Route::get('/tyre/{tyreId}', [TyreScrapController::class, 'scrapTyre'])->name('tyre.scrap.create');
            Route::post('/tyre/{tyreId}', [TyreScrapController::class, 'processScrap'])->name('tyre.scrap.process');
            Route::post('/bulk', [TyreScrapController::class, 'bulkScrap'])->name('tyre.scrap.bulk');
            Route::get('/report/generate', [TyreScrapController::class, 'scrapReport'])->name('tyre.scrap.report');
            Route::get('/report/download/pdf', [TyreScrapController::class, 'downloadScrapReport'])->name('tyre.scrap.report.pdf');
            Route::get('/report/download/excel', [TyreScrapController::class, 'downloadScrapReportExcel'])->name('tyre.scrap.report.excel');
            Route::post('/restore/{scrapId}', [TyreScrapController::class, 'restoreTyre'])->name('tyre.scrap.restore');
            Route::post('/send-kurunagala/{scrapId}', [TyreScrapController::class, 'sendToKurunagala'])->name('tyre.scrap.send-kurunagala');
            Route::post('/sell/{scrapId}', [TyreScrapController::class, 'sellScrap'])->name('tyre.scrap.sell');
            Route::get('/disposal-methods', [TyreScrapController::class, 'disposalMethods'])->name('tyre.scrap.disposal-methods');
            Route::post('/disposal-methods/store', [TyreScrapController::class, 'storeDisposalMethod'])->name('tyre.scrap.disposal-methods.store');
            Route::get('/analytics', [TyreScrapController::class, 'scrapAnalytics'])->name('tyre.scrap.analytics');
        });
        
        // ============================================
        // Tyre Passport (Lifecycle History)
        // ============================================
        Route::prefix('passport')->group(function () {
            Route::get('/search', [TyreInventoryController::class, 'searchPassport'])->name('tyre.passport.search');
            Route::post('/lookup', [TyreInventoryController::class, 'lookupTyre'])->name('tyre.passport.lookup');
            Route::get('/{tyreId}/pdf', [TyreInventoryController::class, 'generatePassportPdf'])->name('tyre.passport.pdf');
            Route::get('/{tyreId}/print', [TyreInventoryController::class, 'printPassport'])->name('tyre.passport.print');
        });
        
        // ============================================
        // Tyre Reports and Analytics
        // ============================================
        Route::prefix('reports')->group(function () {
            Route::get('/analytics', [TyreDashboardController::class, 'analytics'])->name('tyre.reports.analytics');
            Route::get('/tyre-life', [TyreInventoryController::class, 'tyreLifeReport'])->name('tyre.reports.tyre-life');
            Route::get('/usage-stats', [VehicleAllocationController::class, 'usageStatistics'])->name('tyre.reports.usage');
            Route::get('/cost-analysis', [RefillingController::class, 'costAnalysis'])->name('tyre.reports.cost');
            Route::post('/custom-range', [TyreDashboardController::class, 'customRangeReport'])->name('tyre.reports.custom-range');
        });
        
        // ============================================
        // API Routes for AJAX functionality
        // ============================================
        Route::prefix('api')->group(function () {
            Route::get('/tyre/by-serial/{serialNumber}', [TyreInventoryController::class, 'getBySerialNumber'])->name('tyre.api.by-serial');
            Route::get('/available-tyres', [TyreInventoryController::class, 'getAvailableTyres'])->name('tyre.api.available-tyres');
            Route::get('/vehicle/{vehicleId}/current-tyres', [VehicleAllocationController::class, 'getCurrentTyresApi'])->name('tyre.api.vehicle-tyres');
            Route::post('/validate-serial', [TyreInventoryController::class, 'validateSerialNumber'])->name('tyre.api.validate-serial');
            Route::get('/tyre/{tyreId}/summary', [TyreInventoryController::class, 'getLifecycleSummary'])->name('tyre.api.lifecycle-summary');
            Route::get('/monthly-activity', [TyreDashboardController::class, 'getMonthlyActivity'])->name('tyre.api.monthly-activity');
        });
    });
});