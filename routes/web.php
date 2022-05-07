<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\MembersController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\WorkplaceController;
use App\Http\Controllers\BallotController;

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
Route::get('/login/reset', [AuthController::class, 'reset'])->name('auth.reset');
Route::post('/login/reset', [AuthController::class, 'doReset'])->name('auth.doreset');


Route::middleware('auth')->group(function() {

    Route::get('/changepassword', [AuthController::class, 'changePassword'])->name('auth.password');
    Route::post('/changepassword', [AuthController::class, 'updatePassword'])->name('auth.password.update');

    // middleware so that a default password can't access these other
    // routes
    Route::middleware('auth.cpwd')->group(function() {

        // entirely public routes go here
        Route::get('/', [MainController::class, 'index'])->name('main');
        Route::get('/profile', [MainController::class, 'profile'])->name('profile');
        Route::post('/participate/{campaign}', [CampaignController::class, 'participate'])->name('participate');

        Route::get('/notices', [NoticeController::class, 'publicIndex'])->name('notices.public');
        Route::get('/notices/{notice}', [NoticeController::class, 'read'])->name('notices.read');
        
        // phonebanker routes
        Route::middleware('authz.phonebank')->prefix('phonebank')->group(function() {
            Route::get('/phonebank', [MembersController::class, 'search'])->name('phonebank');
            Route::post('/phonebank', [MembersController::class, 'doSearch'])->name('phonebank.search');
            Route::post('/phonebank/{member}', [MembersController::class, 'setParticipation'])->name('phonebank.update');
                
        });

        // general any-role routes
        Route::middleware('authz.any')->group(function() {
            Route::get('/campaigns/report', [CampaignController::class, 'reportIndex'])->name('campaign.report');
            Route::get('/campaigns/report/{campaign}', [CampaignController::class, 'reportView'])->name('campaign.report.view');  
        });
        
        // rep routes
        Route::middleware('authz.rep')->prefix('reps')->group(function() {
            
            Route::get('/members/export', [MembersController::class, 'export'])->name('members.export');            
            Route::get('/members', [MembersController::class, 'list'])->name('members.list');
            Route::get('/members/{member}', [MembersController::class, 'edit'])->name('members.edit');
            Route::post('/members/{member}/campaigns', [MembersController::class, 'update'])->name('members.update');
            Route::post('/members/{member}/workplaces', [MembersController::class, 'updateWorkplace'])->name('members.updateworkplace');
            Route::post('/members/{member}/notes', [MembersController::class, 'updateNotes'])->name('members.updatenotes');
            
        });
        
        // superuser routes
        Route::middleware('authz.super')->prefix('admin')->group(function() {
            
            Route::get('/import', [ImportController::class, 'index'])->name('import');
            Route::post('/import', [ImportController::class, 'process'])->name('import.process');

            Route::resource('/roles', RolesController::class)->except('show');
            Route::resource('/campaigns', CampaignController::class)->except('show');
            Route::resource('/notices', NoticeController::class)->except('show');
            Route::resource('/workplaces', WorkplaceController::class)->except('show');
            Route::resource('/ballots', BallotController::class)->except('show');
            

            Route::post('/members/{member}/setpassword', [MembersController::class, 'setPassword'])->name('members.setpassword');
            
            Route::get('/campaigns/{campaign}/import', [CampaignController::class, 'bulkImport'])->name('campaigns.import');
            Route::post('/campaigns/{campaign}/import', [CampaignController::class, 'bulkImportProcess'])->name('campaigns.import.process');

            
        });
        

    });

});
