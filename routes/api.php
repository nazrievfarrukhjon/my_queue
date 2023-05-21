<?php

use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Api\{AuthController, MonitorController, ServiceCenterController, TicketsController,
    DeviceController, MonitorGroupController};
use App\Http\Controllers\Api\Admin\{CabinetController,
    ClientsController,
    ProfileController,
    ReceptionController,
    ServiceCategoryController,
    ServicesController,
    UsersController};
use App\Http\Controllers\Auth\{ForgotPasswordController,
    RegisterController,
    ResetPasswordController,
    VerificationController};
use Illuminate\Support\Facades\Route;

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

    Route::group([], function () {
        Route::get('/home', [HomeController::class, 'index']);

        Route::get('/reception', [ReceptionController::class, 'index']);
        Route::post('/reception', [ReceptionController::class, 'store']);
        Route::post('/reception/skip-all', [ReceptionController::class, 'skipAll']);

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

        //devices
        Route::prefix('devices')->group(function () {
            Route::get('/', [DeviceController::class, 'get']);
            Route::post('/', [DeviceController::class, 'create']);
            Route::put('/{uuid}', [DeviceController::class, 'update']);
            Route::delete('/{uuid}', [DeviceController::class, 'delete']);
        });

        //devices
        Route::prefix('monitor_groups')->group(function () {
            Route::get('/', [MonitorGroupController::class, 'get']);
            Route::post('/', [MonitorGroupController::class, 'create']);
            Route::put('/{id}', [MonitorGroupController::class, 'update']);
            Route::delete('/{id}', [MonitorGroupController::class, 'delete']);
        });

        //these ticket routes are for administrating {serving ticket, removing updating etc}
        Route::prefix('/tickets')->group(function () {
            Route::get('/', [TicketsController::class, 'getAll']);
            Route::prefix('by')->group(function () {
                Route::get('/ticket_id/{id}', [TicketsController::class, 'getById']);
                Route::get('/status_id/{id}', [TicketsController::class, 'getByStatusId']);
                Route::get('user_id/{id}', [TicketsController::class, 'getByUserId']);
            });

            Route::put('/{id}', [TicketsController::class, 'update']);
            Route::delete('/{id}', [TicketsController::class, 'delete']);
        });

    });

    Route::middleware('auth_device')->group(function() {
        //these routes for devices like monitors
        Route::prefix('/monitors')->group(function () {
            Route::get('/', [MonitorController::class, 'getInvitedTickets']);

            Route::get('/{monitor_group_id}', [MonitorController::class, 'getByMonitorGroupId']);

            Route::get('/{status_id}', [MonitorController::class, 'getTicketsByStatusId']);

            Route::get('/by/statuses', [MonitorController::class, 'getTicketsByStatuses']);

            Route::get('/by/statuses/monitor_groups', [MonitorController::class, 'getTicketsByStatuses']);

        });

        //this route only for creatingticket from some device
        Route::prefix('/tickets')->group(function () {
            Route::post('/', [TicketsController::class, 'create']);
        });
    });

});

//Route::fallback([HomeController::class, 'fallback']);

