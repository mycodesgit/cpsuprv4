<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\YearController;
use App\Http\Controllers\ShopRequestItemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRolesController;
use App\Http\Controllers\AuditTrailController;

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

Route::group(['middleware'=>['guest']],function(){
    Route::get('/', function () {
        return view('auth.login');
    });

    Route::get('/login',[LoginController::class,'getLogin'])->name('getLogin');
    Route::post('/login',[LoginController::class,'postLogin'])->name('postLogin');
});

Route::group(['middleware'=>['login_auth']],function(){
    Route::get('/dashboard',[DashboardController::class,'index'])->name('dashboard.index');
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

    Route::prefix('/manage')->group(function () {
        Route::get('/categorylist/view/all',[CategoryController::class,'index'])->name('category.index');
        Route::get('/categorylist/view/fetch',[CategoryController::class,'show'])->name('category.show');
        Route::post('/categorylist/view/add',[CategoryController::class,'create'])->name('category.create');
        Route::post('/categorylist/view/update',[CategoryController::class,'update'])->name('category.update');

        Route::get('/unit/list', [UnitController::class, 'index'])->name('unit.index');
        Route::get('/unit/list/ajax', [UnitController::class, 'show'])->name('unit.show');
        Route::post('/unit/list/add', [UnitController::class, 'create'])->name('unit.create');
        Route::post('/unit/list/update', [UnitController::class, 'update'])->name('unit.update');

        Route::get('/item/list', [ItemController::class, 'index'])->name('item.index');
        Route::get('/item/list/fetch', [ItemController::class, 'show'])->name('item.show');
        Route::post('/item/list/add', [ItemController::class, 'create'])->name('item.create');
        Route::post('/item/list/update', [ItemController::class, 'update'])->name('item.update');

        Route::get('/office/list', [OfficeController::class, 'index'])->name('office.index');
        Route::get('/office/list/fetch', [OfficeController::class, 'show'])->name('office.show');
        Route::post('/office/list/add', [OfficeController::class, 'create'])->name('office.create');
        Route::post('/office/list/update', [OfficeController::class, 'update'])->name('office.update');
        
        Route::get('/year/list', [YearController::class, 'index'])->name('year.index');
        Route::get('/year/list/fetch', [YearController::class, 'show'])->name('year.show');
        Route::post('/year/list/add', [YearController::class, 'create'])->name('year.create');
        Route::post('/year/list/update', [YearController::class, 'update'])->name('year.update');
    });
    
    Route::prefix('/shop-request-items')->group(function () {
        Route::get('/list/view/all',[ShopRequestItemController::class,'index'])->name('shopitem-request.index');
    });
    
    Route::prefix('/users')->group(function () {
        Route::get('/list/view/all',[UserController::class,'index'])->name('user.index');
        Route::post('/list/view/add',[UserController::class,'create'])->name('user.create');
        Route::get('/list/view/fetch',[UserController::class,'show'])->name('user.show');
        Route::post('/list/view/update', [UserController::class, 'update'])->name('user.update');
        Route::post('/list/updatePass', [UserController::class, 'userUpdatePassword'])->name('userUpdatePassword');
        Route::post('list/updateStatusnow', [UserController::class, 'userUpdateStatus'])->name('userUpdateStatus');
    });
    
    Route::prefix('/roles')->group(function () {
        Route::get('/user/view/all',[UserRolesController::class,'index'])->name('roles.index');
        Route::post('/user/view/add',[UserRolesController::class,'create'])->name('roles.create');
        Route::get('/user/view/fetch',[UserRolesController::class,'show'])->name('roles.show');
        Route::post('/user/view/update', [UserRolesController::class, 'update'])->name('roles.update');
    });
    
    Route::prefix('/audit-trail')->group(function () {
        Route::get('/view/all', [AuditTrailController::class, 'index'])->name('audit-trail.index');
        Route::get('/view/fetch', [AuditTrailController::class, 'showUser'])->name('audit-trail.show.user');
        Route::get('/view/fetch/category', [AuditTrailController::class, 'showCategory'])->name('audit-trail.show.category');
        Route::get('/view/fetch/unit', [AuditTrailController::class, 'showUnit'])->name('audit-trail.show.unit');
        Route::get('/view/fetch/item', [AuditTrailController::class, 'showItem'])->name('audit-trail.show.item');
        Route::get('/view/fetch/office', [AuditTrailController::class, 'showOffice'])->name('audit-trail.show.office');
        Route::get('/view/fetch/year', [AuditTrailController::class, 'showYear'])->name('audit-trail.show.year');
    });
});
