<?php

// Controllers
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\Security\RolePermission;
use App\Http\Controllers\Security\RoleController;
use App\Http\Controllers\Security\PermissionController;

use App\Http\Controllers\UserController;
use App\Http\Controllers\TransInController;
use App\Http\Controllers\TransOutController;

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

require __DIR__.'/auth.php';

Route::get('/storage', function () {
    Artisan::call('storage:link');
});

//UI Pages Routs
Route::get('/uisheet', [HomeController::class, 'uisheet'])->name('uisheet');
// set default route to login page
Route::get('/', [AuthenticatedSessionController::class, 'create'])->name('login-show');

Route::group(['middleware' => 'auth'], function () {
    // Permission Module
    Route::get('/role-permission',[RolePermission::class, 'index'])->name('role.permission.list');
    Route::resource('permission',PermissionController::class);
    Route::resource('role', RoleController::class);

    // Dashboard Routes
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    // Users Module
    Route::resource('users', UserController::class);

    // Master Module
    Route::group(['prefix' => 'master'], function() {
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
    Route::group(['prefix' => 'trans.in'], function() {
        //Transaction In Page Routes
        Route::get('index', [TransInController::class, 'index'])->name('trans.in.index');
        Route::get('create', [TransInController::class, 'create'])->name('trans.in.create');
        Route::post('store', [TransInController::class, 'store'])->name('trans.in.store');
        Route::get('edit/{id}', [TransInController::class, 'edit'])->name('trans.in.edit');
        Route::patch('update/{id}', [TransInController::class, 'update'])->name('trans.in.update');
        Route::delete('destroy/{id}', [TransInController::class, 'destroy'])->name('trans.in.destroy');

        //Route::resource('account', TransInController::class);
    });

    // Transaction Outc Module
    Route::group(['prefix' => 'trans.out'], function() {
        //Transaction Out Page Routes
        Route::get('index', [TransOutController::class, 'index'])->name('trans.out.index');
        Route::get('create', [TransOutController::class, 'create'])->name('trans.out.create');
        Route::post('store', [TransOutController::class, 'store'])->name('trans.out.store');
        Route::get('edit/{id}', [TransOutController::class, 'edit'])->name('trans.out.edit');
        Route::patch('update/{id}', [TransOutController::class, 'update'])->name('trans.out.update');
        Route::delete('destroy/{id}', [TransOutController::class, 'destroy'])->name('trans.out.destroy');

        //Route::resource('account', TransOutController::class);
    });
});

//App Details Page => 'Dashboard'], function() {
Route::group(['prefix' => 'menu-style'], function() {
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
