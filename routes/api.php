<?php

use App\Http\Controllers\CampaignParticipationController;
use App\Http\Controllers\MemberCheckController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api.ip', 'api.token', 'throttle:30,1'])->group(function () {
    Route::post('/member/check', [MemberCheckController::class, 'check']);

    Route::get('/campaigns', [CampaignParticipationController::class, 'index']);
    Route::post('/campaigns/{campaign}/participation', [
        CampaignParticipationController::class,
        'update',
    ]);
});
