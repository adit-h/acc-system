<?php

// Controllers
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\Security\RolePermission;
use App\Http\Controllers\Security\RoleController;
use App\Http\Controllers\Security\PermissionController;
use App\Http\Controllers\SettingController;

use App\Http\Controllers\UserController;
use App\Http\Controllers\TransInController;
use App\Http\Controllers\TransOutController;
use App\Http\Controllers\TransSaleController;
use App\Http\Controllers\TransSaleReturController;
use App\Http\Controllers\TransAssetTransferController;

use App\Http\Controllers\TransDebtPaymentController;
use App\Http\Controllers\TransDebtController;
use App\Http\Controllers\TransReceivableController;
use App\Http\Controllers\TransReceivableDepositController;

use App\Http\Controllers\AdjustmentInController;
use App\Http\Controllers\AdjustmentOutController;

// Reports
use App\Http\Controllers\Report\ReportIncomeStateController;
use App\Http\Controllers\Report\ReportTransJournalController;
use App\Http\Controllers\Report\ReportGeneralLedgerController;
use App\Http\Controllers\Report\ReportBalanceSheetController;

use Illuminate\Support\Facades\Artisan;
// Packages
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthenticatedSessionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

require __DIR__ . '/auth.php';

Route::get('/storage', function () {
    Artisan::call('storage:link');
});

//UI Pages Routs
Route::get('/uisheet', [HomeController::class, 'uisheet'])->name('uisheet');
// set default route to login page
Route::get('/', [AuthenticatedSessionController::class, 'create'])->name('login-show');

//Route::post('/role-permission/ajax-update', [RolePermission::class, 'ajaxUpdate'])->name('role.permission.ajax-update');

Route::group(['middleware' => 'auth'], function () {
    // Permission Module
    Route::group(['prefix' => 'role-permission'], function () {
        Route::get('/', [RolePermission::class, 'index'])->name('role.permission.list');
        Route::post('store', [RolePermission::class, 'store'])->name('role.permission.store');

        // Called by custom.js Role Permission submit sve
        Route::post('ajax-update', [RolePermission::class, 'ajaxUpdate'])->name('role.permission.ajax-update');
    });
    //Route::get('/role-permission', [RolePermission::class, 'index'])->name('role.permission.list');
    //Route::post('/role-permission/store', [RolePermission::class, 'store'])->name('role.permission.store');

    Route::resource('permission', PermissionController::class);
    Route::resource('role', RoleController::class);

    // Dashboard Routes
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    // Users Module
    Route::resource('users', UserController::class);

    // Settings Module
    Route::resource('settings', SettingController::class);

    // Master Module
    Route::group(['prefix' => 'master'], function () {
        //Master Page Routes
        Route::get('account', [MasterController::class, 'index'])->name('master.account');
        Route::get('create', [MasterController::class, 'create'])->name('master.create');
        Route::post('store', [MasterController::class, 'store'])->name('master.store');
        Route::get('edit/{id}', [MasterController::class, 'edit'])->name('master.edit');
        Route::patch('update/{id}', [MasterController::class, 'update'])->name('master.update');
        Route::delete('destroy/{id}', [MasterController::class, 'destroy'])->name('master.destroy');

        //Route::resource('account', MasterController::class);
    });

    // Transaction In Module
    Route::group(['prefix' => 'trans.in'], function () {
        //Transaction In Page Routes
        Route::get('/', [TransInController::class, 'index'])->name('trans.in');
        Route::get('index', [TransInController::class, 'index'])->name('trans.in.index');
        Route::get('create', [TransInController::class, 'create'])->name('trans.in.create');
        Route::post('store', [TransInController::class, 'store'])->name('trans.in.store');
        Route::get('edit/{id}', [TransInController::class, 'edit'])->name('trans.in.edit');
        Route::patch('update/{id}', [TransInController::class, 'update'])->name('trans.in.update');
        Route::delete('destroy/{id}', [TransInController::class, 'destroy'])->name('trans.in.destroy');

        //Route::resource('account', TransInController::class);
    });

    // Transaction Out Module
    Route::group(['prefix' => 'trans.out'], function () {
        //Transaction Out Page Routes
        Route::get('/', [TransOutController::class, 'index'])->name('trans.out');
        Route::get('index', [TransOutController::class, 'index'])->name('trans.out.index');
        Route::get('create', [TransOutController::class, 'create'])->name('trans.out.create');
        Route::post('store', [TransOutController::class, 'store'])->name('trans.out.store');
        Route::get('edit/{id}', [TransOutController::class, 'edit'])->name('trans.out.edit');
        Route::patch('update/{id}', [TransOutController::class, 'update'])->name('trans.out.update');
        Route::delete('destroy/{id}', [TransOutController::class, 'destroy'])->name('trans.out.destroy');

        //Route::resource('account', TransOutController::class);
    });

    // Transaction Sale Module
    Route::group(['prefix' => 'trans.sale'], function () {
        //Transaction Sale Page Routes
        Route::get('/', [TransSaleController::class, 'index'])->name('trans.sale');
        Route::get('index', [TransSaleController::class, 'index'])->name('trans.sale.index');
        Route::get('create', [TransSaleController::class, 'create'])->name('trans.sale.create');
        Route::post('store', [TransSaleController::class, 'store'])->name('trans.sale.store');
        Route::get('edit/{id}', [TransSaleController::class, 'edit'])->name('trans.sale.edit');
        Route::patch('update/{id}', [TransSaleController::class, 'update'])->name('trans.sale.update');
        Route::delete('destroy/{id}', [TransSaleController::class, 'destroy'])->name('trans.sale.destroy');

        //Route::resource('account', TransSaleController::class);
    });

    // Transaction Sale Module
    Route::group(['prefix' => 'trans.sale.retur'], function () {
        //Transaction Sale Retur Page Routes
        Route::get('/', [TransSaleReturController::class, 'index'])->name('trans.sale.retur');
        Route::get('index', [TransSaleReturController::class, 'index'])->name('trans.sale.retur.index');
        Route::get('create', [TransSaleReturController::class, 'create'])->name('trans.sale.retur.create');
        Route::post('store', [TransSaleReturController::class, 'store'])->name('trans.sale.retur.store');
        Route::get('edit/{id}', [TransSaleReturController::class, 'edit'])->name('trans.sale.retur.edit');
        Route::patch('update/{id}', [TransSaleReturController::class, 'update'])->name('trans.sale.retur.update');
        Route::delete('destroy/{id}', [TransSaleReturController::class, 'destroy'])->name('trans.sale.retur.destroy');

        //Route::resource('account', TransSaleReturController::class);
    });

    // Transaction Asset Transfer Module
    Route::group(['prefix' => 'trans.asset.transfer'], function () {
        //Transaction In Page Routes
        Route::get('/', [TransAssetTransferController::class, 'index'])->name('trans.asset.transfer');
        Route::get('index', [TransAssetTransferController::class, 'index'])->name('trans.asset.transfer.index');
        Route::get('create', [TransAssetTransferController::class, 'create'])->name('trans.asset.transfer.create');
        Route::post('store', [TransAssetTransferController::class, 'store'])->name('trans.asset.transfer.store');
        Route::get('edit/{id}', [TransAssetTransferController::class, 'edit'])->name('trans.asset.transfer.edit');
        Route::patch('update/{id}', [TransAssetTransferController::class, 'update'])->name('trans.asset.transfer.update');
        Route::delete('destroy/{id}', [TransAssetTransferController::class, 'destroy'])->name('trans.asset.transfer.destroy');

        //Route::resource('account', TransAssetTransferController::class);
    });

    // Transaction Debt Payment Module
    Route::group(['prefix' => 'trans.debt.payment'], function () {
        //Transaction Out Page Routes
        Route::get('/', [TransDebtPaymentController::class, 'index'])->name('trans.debt.payment');
        Route::get('index', [TransDebtPaymentController::class, 'index'])->name('trans.debt.payment.index');
        Route::get('create', [TransDebtPaymentController::class, 'create'])->name('trans.debt.payment.create');
        Route::post('store', [TransDebtPaymentController::class, 'store'])->name('trans.debt.payment.store');
        Route::get('edit/{id}', [TransDebtPaymentController::class, 'edit'])->name('trans.debt.payment.edit');
        Route::patch('update/{id}', [TransDebtPaymentController::class, 'update'])->name('trans.debt.payment.update');
        Route::delete('destroy/{id}', [TransDebtPaymentController::class, 'destroy'])->name('trans.debt.payment.destroy');

        //Route::resource('account', TransDebtPaymentController::class);
    });

    // Transaction Debt Module
    Route::group(['prefix' => 'trans.debt'], function () {
        //Transaction Out Page Routes
        Route::get('/', [TransDebtController::class, 'index'])->name('trans.debt');
        Route::get('index', [TransDebtController::class, 'index'])->name('trans.debt.index');
        Route::get('create', [TransDebtController::class, 'create'])->name('trans.debt.create');
        Route::post('store', [TransDebtController::class, 'store'])->name('trans.debt.store');
        Route::get('edit/{id}', [TransDebtController::class, 'edit'])->name('trans.debt.edit');
        Route::patch('update/{id}', [TransDebtController::class, 'update'])->name('trans.debt.update');
        Route::delete('destroy/{id}', [TransDebtController::class, 'destroy'])->name('trans.debt.destroy');

        //Route::resource('account', TransDebtController::class);
    });


    // Transaction Receivable Module
    Route::group(['prefix' => 'trans.receivable'], function () {
        //Transaction Receivable Page Routes
        Route::get('/', [TransReceivableController::class, 'index'])->name('trans.receivable');
        Route::get('index', [TransReceivableController::class, 'index'])->name('trans.receivable.index');
        Route::get('create', [TransReceivableController::class, 'create'])->name('trans.receivable.create');
        Route::post('store', [TransReceivableController::class, 'store'])->name('trans.receivable.store');
        Route::get('edit/{id}', [TransReceivableController::class, 'edit'])->name('trans.receivable.edit');
        Route::patch('update/{id}', [TransReceivableController::class, 'update'])->name('trans.receivable.update');
        Route::delete('destroy/{id}', [TransReceivableController::class, 'destroy'])->name('trans.receivable.destroy');

        //Route::resource('account', TransReceivableController::class);
    });

    // Transaction Receivable Deposit Module
    Route::group(['prefix' => 'trans.receivable.deposit'], function () {
        //Transaction Receivable Deposit Routes
        Route::get('/', [TransReceivableDepositController::class, 'index'])->name('trans.receivable.deposit');
        Route::get('index', [TransReceivableDepositController::class, 'index'])->name('trans.receivable.deposit.index');
        Route::get('create', [TransReceivableDepositController::class, 'create'])->name('trans.receivable.deposit.create');
        Route::post('store', [TransReceivableDepositController::class, 'store'])->name('trans.receivable.deposit.store');
        Route::get('edit/{id}', [TransReceivableDepositController::class, 'edit'])->name('trans.receivable.deposit.edit');
        Route::patch('update/{id}', [TransReceivableDepositController::class, 'update'])->name('trans.receivable.deposit.update');
        Route::delete('destroy/{id}', [TransReceivableDepositController::class, 'destroy'])->name('trans.receivable.deposit.destroy');

        //Route::resource('account', TransReceivableDepositController::class);
    });


    // Adjustment In Module
    Route::group(['prefix' => 'adjustment.in'], function () {
        //Adjustment In Page Routes
        Route::get('/', [AdjustmentInController::class, 'index'])->name('adjustment.in');
        Route::get('index', [AdjustmentInController::class, 'index'])->name('adjustment.in.index');
        Route::get('create', [AdjustmentInController::class, 'create'])->name('adjustment.in.create');
        Route::post('store', [AdjustmentInController::class, 'store'])->name('adjustment.in.store');
        Route::get('edit/{id}', [AdjustmentInController::class, 'edit'])->name('adjustment.in.edit');
        Route::patch('update/{id}', [AdjustmentInController::class, 'update'])->name('adjustment.in.update');
        Route::delete('destroy/{id}', [AdjustmentInController::class, 'destroy'])->name('adjustment.in.destroy');

        //Route::resource('account', AdjustmentInController::class);
    });

    // Adjustment Out Module
    Route::group(['prefix' => 'adjustment.out'], function () {
        //Adjustment Out Page Routes
        Route::get('/', [AdjustmentOutController::class, 'index'])->name('adjustment.out');
        Route::get('index', [AdjustmentOutController::class, 'index'])->name('adjustment.out.index');
        Route::get('create', [AdjustmentOutController::class, 'create'])->name('adjustment.out.create');
        Route::post('store', [AdjustmentOutController::class, 'store'])->name('adjustment.out.store');
        Route::get('edit/{id}', [AdjustmentOutController::class, 'edit'])->name('adjustment.out.edit');
        Route::patch('update/{id}', [AdjustmentOutController::class, 'update'])->name('adjustment.out.update');
        Route::delete('destroy/{id}', [AdjustmentOutController::class, 'destroy'])->name('adjustment.out.destroy');

        //Route::resource('account', AdjustmentOutController::class);
    });


    Route::group(['prefix' => 'report/income-state'], function () {
        //Income Statement Routes
        Route::get('/', [ReportIncomeStateController::class, 'index'])->name('report.income.state');
        Route::get('index', [ReportIncomeStateController::class, 'index'])->name('report.income.state.index');
        Route::post('filter', [ReportIncomeStateController::class, 'filter'])->name('report.income.state.filter');

        Route::get('export-excel/', [ReportIncomeStateController::class, 'exportExcel'])->name('report.income.state.export.excel');
        Route::get('export-pdf/', [ReportIncomeStateController::class, 'exportPdf'])->name('report.income.state.export.pdf');
        Route::get('export-html/', [ReportIncomeStateController::class, 'exportHtml'])->name('report.income.state.export.html');
    });

    Route::group(['prefix' => 'report/trans-journal'], function () {
        //Trans Journal Routes
        Route::get('/', [ReportTransJournalController::class, 'index'])->name('report.trans.journal');
        Route::get('index', [ReportTransJournalController::class, 'index'])->name('report.trans.journal.index');
        Route::post('filter', [ReportTransJournalController::class, 'filter'])->name('report.trans.journal.filter');

        Route::get('export-excel/', [ReportTransJournalController::class, 'exportExcel'])->name('report.trans.journal.export.excel');
        Route::get('export-pdf/', [ReportTransJournalController::class, 'exportPdf'])->name('report.trans.journal.export.pdf');
        Route::get('export-html/', [ReportTransJournalController::class, 'exportHtml'])->name('report.trans.journal.export.html');
    });

    Route::group(['prefix' => 'report/general-ledger'], function () {
        //General Ledger Routes
        Route::get('/', [ReportGeneralLedgerController::class, 'index'])->name('report.general.ledger');
        Route::get('index', [ReportGeneralLedgerController::class, 'index'])->name('report.general.ledger.index');
        Route::post('filter', [ReportGeneralLedgerController::class, 'filter'])->name('report.general.ledger.filter');

        Route::get('export-excel/', [ReportGeneralLedgerController::class, 'exportExcel'])->name('report.general.ledger.export.excel');
        Route::get('export-pdf/', [ReportGeneralLedgerController::class, 'exportPdf'])->name('report.general.ledger.export.pdf');
        Route::get('export-html/', [ReportGeneralLedgerController::class, 'exportHtml'])->name('report.general.ledger.export.html');
    });

    Route::group(['prefix' => 'report/balance-sheet'], function () {
        //Balance Sheet Routes
        Route::get('/', [ReportBalanceSheetController::class, 'index'])->name('report.balance.sheet');
        Route::get('index', [ReportBalanceSheetController::class, 'index'])->name('report.balance.sheet.index');
        Route::post('filter', [ReportBalanceSheetController::class, 'filter'])->name('report.balance.sheet.filter');

        Route::get('export-excel/', [ReportBalanceSheetController::class, 'exportExcel'])->name('report.balance.sheet.export.excel');
        Route::get('export-pdf/', [ReportBalanceSheetController::class, 'exportPdf'])->name('report.balance.sheet.export.pdf');
        Route::get('export-html/', [ReportBalanceSheetController::class, 'exportHtml'])->name('report.balance.sheet.export.html');
    });
});

//App Details Page => 'Dashboard'], function() {
Route::group(['prefix' => 'menu-style'], function () {
    //MenuStyle Page Routs
    Route::get('horizontal', [HomeController::class, 'horizontal'])->name('menu-style.horizontal');
    Route::get('dual-horizontal', [HomeController::class, 'dualhorizontal'])->name('menu-style.dualhorizontal');
    Route::get('dual-compact', [HomeController::class, 'dualcompact'])->name('menu-style.dualcompact');
    Route::get('boxed', [HomeController::class, 'boxed'])->name('menu-style.boxed');
    Route::get('boxed-fancy', [HomeController::class, 'boxedfancy'])->name('menu-style.boxedfancy');
});

/*
//App Details Page => 'special-pages'], function() {
Route::group(['prefix' => 'special-pages'], function() {
    //Example Page Routs
    Route::get('billing', [HomeController::class, 'billing'])->name('special-pages.billing');
    Route::get('calender', [HomeController::class, 'calender'])->name('special-pages.calender');
    Route::get('kanban', [HomeController::class, 'kanban'])->name('special-pages.kanban');
    Route::get('pricing', [HomeController::class, 'pricing'])->name('special-pages.pricing');
    Route::get('rtl-support', [HomeController::class, 'rtlsupport'])->name('special-pages.rtlsupport');
    Route::get('timeline', [HomeController::class, 'timeline'])->name('special-pages.timeline');
});

//Widget Routs
Route::group(['prefix' => 'widget'], function() {
    Route::get('widget-basic', [HomeController::class, 'widgetbasic'])->name('widget.widgetbasic');
    Route::get('widget-chart', [HomeController::class, 'widgetchart'])->name('widget.widgetchart');
    Route::get('widget-card', [HomeController::class, 'widgetcard'])->name('widget.widgetcard');
});

//Maps Routs
Route::group(['prefix' => 'maps'], function() {
    Route::get('google', [HomeController::class, 'google'])->name('maps.google');
    Route::get('vector', [HomeController::class, 'vector'])->name('maps.vector');
});

//Auth pages Routs
Route::group(['prefix' => 'auth'], function() {
    Route::get('signin', [HomeController::class, 'signin'])->name('auth.signin');
    Route::get('signup', [HomeController::class, 'signup'])->name('auth.signup');
    Route::get('confirmmail', [HomeController::class, 'confirmmail'])->name('auth.confirmmail');
    Route::get('lockscreen', [HomeController::class, 'lockscreen'])->name('auth.lockscreen');
    Route::get('recoverpw', [HomeController::class, 'recoverpw'])->name('auth.recoverpw');
    Route::get('userprivacysetting', [HomeController::class, 'userprivacysetting'])->name('auth.userprivacysetting');
});

//Error Page Route
Route::group(['prefix' => 'errors'], function() {
    Route::get('error404', [HomeController::class, 'error404'])->name('errors.error404');
    Route::get('error500', [HomeController::class, 'error500'])->name('errors.error500');
    Route::get('maintenance', [HomeController::class, 'maintenance'])->name('errors.maintenance');
});


//Forms Pages Routs
Route::group(['prefix' => 'forms'], function() {
    Route::get('element', [HomeController::class, 'element'])->name('forms.element');
    Route::get('wizard', [HomeController::class, 'wizard'])->name('forms.wizard');
    Route::get('validation', [HomeController::class, 'validation'])->name('forms.validation');
});


//Table Page Routs
Route::group(['prefix' => 'table'], function() {
    Route::get('bootstraptable', [HomeController::class, 'bootstraptable'])->name('table.bootstraptable');
    Route::get('datatable', [HomeController::class, 'datatable'])->name('table.datatable');
});

//Icons Page Routs
Route::group(['prefix' => 'icons'], function() {
    Route::get('solid', [HomeController::class, 'solid'])->name('icons.solid');
    Route::get('outline', [HomeController::class, 'outline'])->name('icons.outline');
    Route::get('dualtone', [HomeController::class, 'dualtone'])->name('icons.dualtone');
    Route::get('colored', [HomeController::class, 'colored'])->name('icons.colored');
});*/

//Extra Page Routs
//Route::get('privacy-policy', [HomeController::class, 'privacypolicy'])->name('pages.privacy-policy');
//Route::get('terms-of-use', [HomeController::class, 'termsofuse'])->name('pages.term-of-use');
