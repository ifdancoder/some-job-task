<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\EquipmentTypeController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('equipment-type/without-pagination', [EquipmentTypeController::class, 'indexwp']); // Route, необходимый для правильной работы фронтенда

Route::apiResources([
    'equipment' => EquipmentController::class,
    'equipment-type' => EquipmentTypeController::class,
]);
