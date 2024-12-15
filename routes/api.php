<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AutomationController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\InstanceController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\SchedulingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WebhookController;
use App\Http\Middleware\AdminMiddleware;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('login', [AuthController::class, 'login']);

Route::get('validateToken', [AuthController::class, 'validateToken']);
Route::post('recoverPassword', [UserController::class, 'passwordRecovery']);
Route::post('updatePassword', [UserController::class, 'updatePassword']);


Route::get('validateToken', [AuthController::class, 'validateToken']);

Route::prefix('webhook')->group(function(){    
    Route::post('handle', [WebhookController::class, 'handle']);        
    Route::post('handle', [WebhookController::class, 'handle']);        
});

Route::middleware('jwt')->group(function(){

    Route::middleware(AdminMiddleware::class)->group(function() {
        // Middleware do admin
    });

    Route::post('logout', [AuthController::class, 'logout']);

    Route::prefix('user')->group(function(){
        Route::get('all', [UserController::class, 'all']);
        Route::get('search', [UserController::class, 'search']);
        Route::get('cards', [UserController::class, 'cards']);
        Route::get('me', [UserController::class, 'getUser']);
        Route::post('create', [UserController::class, 'create']);
        Route::patch('{id}', [UserController::class, 'update']);
        Route::post('block/{id}', [UserController::class, 'userBlock']);
    });

    Route::prefix('client')->group(function(){
        Route::get('search', [ClientController::class, 'search']);
        Route::post('create', [ClientController::class, 'create']);
        Route::patch('{id}', [ClientController::class, 'update']);
        Route::delete('{id}', [ClientController::class, 'delete']);
    });

    Route::prefix('link')->group(function(){
        Route::get('search', [LinkController::class, 'search']);
        Route::post('create', [LinkController::class, 'create']);
        Route::patch('{id}', [LinkController::class, 'update']);
        Route::delete('{id}', [LinkController::class, 'delete']);
    });

    Route::prefix('schedule')->group(function(){
        Route::get('search', [SchedulingController::class, 'search']);
        Route::post('create', [SchedulingController::class, 'create']);
        Route::patch('{id}', [SchedulingController::class, 'update']);
        Route::delete('{id}', [SchedulingController::class, 'delete']);
    });

    Route::prefix('automation')->group(function(){
        Route::get('search', [AutomationController::class, 'search']);
        Route::post('create', [AutomationController::class, 'create']);
        Route::patch('{id}', [AutomationController::class, 'update']);
        Route::delete('{id}', [AutomationController::class, 'delete']);
    });

    Route::prefix('instance')->group(function(){
        Route::get('search', [InstanceController::class, 'search']);        
        Route::get('groups/{instanceName}', [InstanceController::class, 'groups']);
        Route::get('connect/{instanceName}', [InstanceController::class, 'connect']);
        Route::post('create', [InstanceController::class, 'create']);        
    });
});
