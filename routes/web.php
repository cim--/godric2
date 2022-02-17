<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\MembersController;

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

Route::get('/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/login', [AuthController::class, 'doLogin'])->name('auth.dologin');
Route::get('/logout', [AuthController::class, 'logout'])->name('auth.logout');


Route::middleware('auth')->group(function() {

    Route::get('/changepassword', [AuthController::class, 'changePassword'])->name('auth.password');
    Route::post('/changepassword', [AuthController::class, 'updatePassword'])->name('auth.password.update');

    // middleware so that a default password can't access these other
    // routes
    Route::middleware('auth.cpwd')->group(function() {
    
        Route::get('/', [MainController::class, 'index'])->name('main');
        Route::post('/participate/{campaign}', [CampaignController::class, 'participate'])->name('participate');

        
        // rep routes
        Route::middleware('authz.rep')->prefix('reps')->group(function() {
            
            Route::get('/members', [MembersController::class, 'list'])->name('members.list');
            Route::get('/members/{member}', [MembersController::class, 'edit'])->name('members.edit');
            Route::post('/members/{member}', [MembersController::class, 'update'])->name('members.update');
            
        });
        
        // superuser routes
        Route::middleware('authz.super')->prefix('admin')->group(function() {
            
            Route::get('/import', [ImportController::class, 'index'])->name('import');
            Route::post('/import', [ImportController::class, 'process'])->name('import.process');

            Route::resource('/roles', RolesController::class)->except('show');
            Route::resource('/campaigns', CampaignController::class)->except('show', 'destroy');
            
            Route::get('/campaigns/{campaign}/import', [CampaignController::class, 'bulkImport'])->name('campaigns.import');
            Route::post('/campaigns/{campaign}/import', [CampaignController::class, 'bulkImportProcess'])->name('campaigns.import.process');

            
        });
        

    });

});
