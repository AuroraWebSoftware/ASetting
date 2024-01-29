<?php


use AuroraWebSoftware\ASetting\Http\Controllers\API\ASettingApiController;
use Illuminate\Support\Facades\Route;

Route::middleware(array_merge([\AuroraWebSoftware\ASetting\Http\Middleware\BearerTokenAuth::class], config('asetting.api_middleware')))->group(function () {
    Route::get("/api/asetting/getValue/{group}/{key}", [ASettingApiController::class, 'getValue'])->name('asetting.getValue');
    Route::get("/api/asetting/getTitle/{group}/{key}", [ASettingApiController::class, 'getTitle'])->name('asetting.getTitle');
    Route::get("/api/asetting/get/{group}/{key}", [ASettingApiController::class, 'get'])->name('asetting.get');
    Route::get("/api/asetting/isVisible/{group}/{key}", [ASettingApiController::class, 'isVisible'])->name('asetting.isVisible');
    Route::delete("/api/asetting/delete/{group}/{key}", [ASettingApiController::class, 'delete'])->name('asetting.delete');
    Route::delete("/api/asetting/destroy/{group}", [ASettingApiController::class, 'destroy'])->name('asetting.destroy');
    Route::get("/api/asetting/all/{group?}", [ASettingApiController::class, 'all'])->name('asetting.all');
    Route::post("/api/asetting/getValues", [ASettingApiController::class, 'getValues'])->name('asetting.getValues');
    Route::post("/api/asetting/add", [ASettingApiController::class, 'add'])->name('asetting.add');
    Route::put("/api/asetting/set", [ASettingApiController::class, 'set'])->name('asetting.set');
});
