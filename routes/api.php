<?php

use App\Http\Controllers\API\productController;
use App\Http\Controllers\API\signController;
use App\Http\Controllers\API\userController;
use App\Http\Controllers\API\userRoleController;
use App\Http\Middleware\receiverRegisterMiddleware;
use App\Http\Middleware\registerOneProductMiddleware;
use App\Http\Middleware\registerProductsMiddleware;
use App\Http\Middleware\signMiddleware;
use App\Http\Middleware\userLoginMiddleware;
use App\Http\Middleware\userUpdatePasswordMiddleware;
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

    //Singup
    Route::post('/singup', [signController::class, 'singup'])->middleware(signMiddleware::class);

    //Auth
    Route::post('/auth/login', [userController::class, 'auth'])->middleware(userLoginMiddleware::class);

    //Roles
    Route::get('/userRoles', [userRoleController::class, 'index']);


    Route::prefix('')->middleware(['auth:sanctum'])->group(function () {

        Route::prefix('/user')->group(function () {        
            Route::get("", [userController::class, 'index']);
            Route::post("/receiver", [userController::class, 'receiver'])->middleware(receiverRegisterMiddleware::class);
            Route::post("/cashier", [userController::class, 'cashier'])->middleware(receiverRegisterMiddleware::class);
            Route::post("/banner", [userController::class, 'cashier'])->middleware(receiverRegisterMiddleware::class);

            Route::post("/update-password", [userController::class, 'updatePassword'])->middleware(userUpdatePasswordMiddleware::class);
        });

        Route::prefix("/products")->group(function ()
        {
            Route::get("", [productController::class, 'index']);
            Route::get("/all", [productController::class, 'allProducts']);
            Route::post("", [productController::class, 'store'])->middleware(registerProductsMiddleware::class);
            Route::post("/add", [productController::class, 'product'])->middleware(registerOneProductMiddleware::class);
        });

    });


    

});
