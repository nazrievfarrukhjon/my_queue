<?php

use App\Http\Controllers\Admin\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\{
    ForgotPasswordController,
    RegisterController,
    ResetPasswordController,
    VerificationController};
use App\Http\Controllers\Api\Admin\{CabinetController,
    ClientsController,
    ProfileController,
    ReceptionController,
    ServiceCategoryController,
    ServicesController,
    UsersController};
use App\Http\Controllers\Api\{AuthController, MonitorController, ServiceCenterController};

Route::get('/', function () {
    return 'Queue system service';
});

Route::namespace('Api')->group(function () {

    Route::middleware('guest')->group(function () {
        Route::get('/service-centers/list', [ServiceCenterController::class, 'list']);

        Route::post('/register', [RegisterController::class, 'register'])->name('register');
        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);
        Route::post('/password/reset', [ResetPasswordController::class, 'reset']);
        Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
    });

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::get('/logout', [AuthController::class, 'logout']);
        Route::put('/profile', [ProfileController::class, 'updateProfile']);
        Route::put('/password', [ProfileController::class, 'updatePassword']);
        Route::post('/email/resend', [VerificationController::class, 'resend']);
    });

    Route::middleware(['auth:sanctum', 'verified'])->group(function () {
        Route::get('/home', [HomeController::class, 'index']);

        Route::get('/reception', [ReceptionController::class, 'index']);
        Route::post('/reception', [ReceptionController::class, 'store']);
        Route::post('/reception/skip-all', [ReceptionController::class, 'skipAll']);

        Route::get('/monitor/', [MonitorController::class, 'index']);

        Route::get('/users', [UsersController::class, 'index']);
        Route::get('/users/{user}', [UsersController::class, 'show'])->where('user', '[0-9]+');
        Route::post('/users', [UsersController::class, 'store']);
        Route::put('/users/{user}', [UsersController::class, 'update'])->where('user', '[0-9]+');
        Route::delete('/users/{user}', [UsersController::class, 'destroy'])->where('user', '[0-9]+');

        Route::get('/service-categories', [ServiceCategoryController::class, 'index']);
        Route::post('/service-categories', [ServiceCategoryController::class, 'store']);
        Route::get('/service-categories/{serviceCategory}', [ServiceCategoryController::class, 'show']);
        Route::put('/service-categories/{serviceCategory}', [ServiceCategoryController::class, 'update']);
        Route::delete('/service-categories/{serviceCategory}', [ServiceCategoryController::class, 'destroy']);

        Route::get('/cabinet', [CabinetController::class, 'index']);
        Route::get('/cabinet/services', [CabinetController::class, 'services']);
        Route::post('/cabinet/invite', [CabinetController::class, 'invite']);
        Route::post('/cabinet/accept', [CabinetController::class, 'accept']);
        Route::post('/cabinet/done', [CabinetController::class, 'done']);
        Route::post('/cabinet/save', [CabinetController::class, 'saveTicket']);

        Route::get('/clients', [ClientsController::class, 'index']);
        Route::post('/clients', [ClientsController::class, 'store']);
        Route::get('/clients/{client}', [ClientsController::class, 'show']);
        Route::put('/clients/{client}', [ClientsController::class, 'update']);
        Route::delete('/clients/{client}', [ClientsController::class, 'destroy']);

        Route::get('/services', [ServicesController::class, 'index']);
        Route::get('/services/{service}', [ServicesController::class, 'list']);
        Route::get('/services/list', [ServicesController::class, 'list']);
        Route::post('/services', [ServicesController::class, 'store']);
        Route::put('/services/{service}', [ServicesController::class, 'update']);
        Route::delete('/services/{service}', [ServicesController::class, 'destroy']);

        Route::get('/service-centers', [ServiceCenterController::class, 'index']);
        Route::post('/service-centers', [ServiceCenterController::class, 'store']);
        Route::put('/service-centers/{serviceCenter}', [ServiceCenterController::class, 'update']);
        Route::delete('/service-centers/{serviceCenter}', [ServiceCenterController::class, 'destroy']);
    });
});

//Route::fallback([HomeController::class, 'fallback']);

