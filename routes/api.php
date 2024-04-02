<?php

use App\Http\Controllers\API\signController;
use App\Http\Controllers\API\userController;
use App\Http\Middleware\signMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::get('/', function (Request $request){
    return response()->json([
        'message' => 'Bienvenu sur l\'api de Ndanga',
        'version' => 'C\'est la version beta de cet API merci',
        'Developpeur' => 'Developper par hobedbayekula@gmail.com',
    ]);
});
// ->middleware('cors');


Route::prefix('v1')->group(function () {

    Route::post('/singup', [signController::class, 'singup'])->middleware(signMiddleware::class);

    Route::prefix('/user')->group(function () {        
        Route::get("", [userController::class, 'index']);
    });
});
