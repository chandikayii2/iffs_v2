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

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/login', [AuthController::class, 'loginView'])->name('login');

Route::redirect('/', '/login');

Route::post('login-check', [AuthController::class, 'loginCheck'])->name('login-check');

Route::group(['middleware' => 'AdminAuth1'], function () {

    Route::get('logout', [AuthController::class, 'logout']);

    Route::group(['prefix' => 'admin'], function () {

        Route::prefix('reports/product')->group(function() {
            Route::get('/', [ProductReportController::class, 'showForm'])->name('reports.product.form');
            Route::post('/generate', [ProductReportController::class, 'generateReport'])->name('reports.product.generate');
            Route::get('/download', [ProductReportController::class, 'generateReport'])->name('reports.product.download');
        });

        Route::get('dashboard', [AuthController::class, 'dashboardView'])->name('dashboard');

        Route::group(['prefix' => 'supplier'], function () {

            Route::post('/create', [SupplierController::class, 'create'])->name('supplier-create');
            Route::get('/', [SupplierController::class, 'getAll'])->name('supplier-get-all');
            Route::get('/edit/{supplierId}', [SupplierController::class, 'edit']);
            Route::post('/update', [SupplierController::class, 'update']);
            Route::delete('/delete/{productId}', [ProductController::class, 'delete']);

        });

        Route::group(['prefix' => 'products'], function () {

            Route::post('/create', [ProductController::class, 'create'])->name('product-create');
            Route::get('/', [ProductController::class, 'getAll'])->name('product-get-all');
            Route::get('/edit/{productId}', [ProductController::class, 'edit']);
            Route::post('/update', [ProductController::class, 'update']);
        });

        Route::group(['prefix' => 'stock'], function () {

            Route::get('/', [StockController::class, 'getAll'])->name('stock-get-all');
                        Route::get('/generate-pdf', [StockController::class, 'genaratePdf']);

        });

        Route::group(['prefix' => 'users'], function () {

            Route::get('/', [UserController::class, 'getAll'])->name('user-get-all');
            Route::get('/edit/{userId}', [UserController::class, 'edit']);
            Route::post('/update', [UserController::class, 'update']);
            Route::post('/create', [UserController::class, 'create'])->name('user-create');
            Route::post('/password-update', [UserController::class, 'passwordUpdate']);

            Route::group(['prefix' => 'role'], function () {
                Route::get('/', [UserRoleController::class, 'getAllUserRole'])->name('role-all-get-all');
                Route::post('/create', [UserRoleController::class, 'create'])->name('user-role-create');
            });

            Route::group(['prefix' => 'permission'], function () {
                Route::get('/', [UserRolePermisionController::class, 'getAllUserPermissions'])->name('get-all-user-permissions');
            });

            Route::group(['prefix' => 'role_permission'], function () {
                Route::get('/', [UserRolePermisionController::class, 'getAllUserRolePermissions'])->name('get-all-user-role-permissions');
                Route::post('/create', [UserRolePermisionController::class, 'create'])->name('role-permission-create');

                Route::post('/check-role-exists', [UserRolePermisionController::class, 'checkRoleExists'])->name('check-role-exists');

                Route::get('/edit/{role_permission_id}', [UserRolePermisionController::class, 'edit']);
                Route::post('/update', [UserRolePermisionController::class, 'update']);
            });
        });


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
});
