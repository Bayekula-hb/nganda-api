<?php

use App\Http\Controllers\API\adminController;
use App\Http\Controllers\API\drinkController;
use App\Http\Controllers\API\historicInventoryDrinkController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\productController;
use App\Http\Controllers\API\saleController;
use App\Http\Controllers\API\SettingsController;
use App\Http\Controllers\API\signController;
use App\Http\Controllers\API\userController;
use App\Http\Controllers\API\userRoleController;
use App\Http\Middleware\createDrinkMiddleware;
use App\Http\Middleware\drinkUpdatedImgMiddleware;
use App\Http\Middleware\paymentMiddleware;
use App\Http\Middleware\procurementProductByStoreMiddleware;
use App\Http\Middleware\procurementProductMiddleware;
use App\Http\Middleware\receiverRegisterMiddleware;
use App\Http\Middleware\registerOneProductMiddleware;
use App\Http\Middleware\registerProductsMiddleware;
use App\Http\Middleware\saleProductsMiddleware;
use App\Http\Middleware\saleStatisticsMiddleware;
use App\Http\Middleware\searchDrinkMiddleware;
use App\Http\Middleware\signMiddleware;
use App\Http\Middleware\updatedDrinkImgMiddleware;
use App\Http\Middleware\updateProductsStoreMiddleware;
use App\Http\Middleware\userLoginMiddleware;
use App\Http\Middleware\userRegisterMiddleware;
use App\Http\Middleware\userUpdatePasswordMiddleware;
use App\Models\establishment;
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
            Route::get("/establishment", [userController::class, 'userByEstablishment']);
            Route::post("/receiver", [userController::class, 'receiver'])->middleware(receiverRegisterMiddleware::class);
            Route::post("/cashier", [userController::class, 'cashier'])->middleware(receiverRegisterMiddleware::class);
            Route::post("/banner", [userController::class, 'cashier'])->middleware(receiverRegisterMiddleware::class);
            Route::post("", [userController::class, 'store'])->middleware(userRegisterMiddleware::class);

            Route::post("/update-password", [userController::class, 'updatePassword'])->middleware(userUpdatePasswordMiddleware::class);
        });

        Route::prefix("/products")->group(function ()
        {
            Route::get("", [productController::class, 'index']);
            Route::get("/all", [productController::class, 'allProducts']);
            Route::post("", [productController::class, 'store'])->middleware(registerProductsMiddleware::class);
            Route::post("/add", [productController::class, 'product'])->middleware(registerOneProductMiddleware::class);
            Route::put("/procurement", [productController::class, 'procurement'])->middleware(procurementProductMiddleware::class);
        });

        Route::prefix("/drink")->group(function ()
        {
            Route::get("", [drinkController::class, 'index']);
            Route::post("/", [drinkController::class, 'store'])->middleware(createDrinkMiddleware::class);
            Route::put("", [drinkController::class, 'update'])->middleware(drinkUpdatedImgMiddleware::class);
            Route::put("/{id}", [drinkController::class, 'updateDrink'])->middleware(updatedDrinkImgMiddleware::class);
        });

        Route::prefix("/sale")->group(function ()
        {
            Route::get("", [saleController::class, 'index']);
            Route::get("/statistics", [saleController::class, 'statistics']);
            Route::get("/statistics/{startDate}/{endDate}", [saleController::class, 'statisticByDate']);
            Route::get("/statistics-by-date/{endDate}", [saleController::class, 'statisticEndDateWithSixPreviousDays']);
            Route::post("", [saleController::class, 'store'])->middleware(saleProductsMiddleware::class);
            // Route::put("", [saleController::class, 'update'])->middleware(drinkUpdatedImgMiddleware::class);
        });

        Route::prefix("/payment")->group(function ()
        {
            // Route::get("", [drinkController::class, 'index']);
            Route::post("", [PaymentController::class, 'store'])->middleware(paymentMiddleware::class);
            Route::get("/{id}", [PaymentController::class, 'show']);
            // Route::put("", [drinkController::class, 'update'])->middleware(drinkUpdatedImgMiddleware::class);
            // Route::put("/{id}", [drinkController::class, 'updateDrink'])->middleware(updatedDrinkImgMiddleware::class);
        });

        Route::prefix("/settings")->group(function ()
        {
            Route::post("", [SettingsController::class, 'store']);
        });
        
        Route::prefix("/admin")->group(function(){
            Route::get("/establishments/{current_page}", [adminController::class, 'index']);
            Route::get("/establishment/{id}", [adminController::class, 'establishment']);
            Route::get("/statistics", [adminController::class, 'statistics']);
        });

    });
    
    Route::fallback(function () {
        return response()->json([
            'error' => true,
            'message' => 'Route not found'
        ], 404);
    });
});

Route::prefix('v1.1')->group(function () {

    //Singup
    Route::post('/singup', [signController::class, 'singup'])->middleware(signMiddleware::class);

    //Auth
    Route::post('/auth/login', [userController::class, 'auth'])->middleware(userLoginMiddleware::class);
    Route::post('/auth/forget-password', [userController::class, 'forgetPassword'])->middleware(userLoginMiddleware::class);

    //Roles
    Route::get('/userRoles', [userRoleController::class, 'index']);


    Route::prefix('')->middleware(['auth:sanctum'])->group(function () {

        Route::prefix('/user')->group(function () {        
            Route::get("", [userController::class, 'index']);
            Route::get("/establishment", [userController::class, 'userByEstablishment']);
            Route::post("/receiver", [userController::class, 'receiver'])->middleware(receiverRegisterMiddleware::class);
            Route::post("/cashier", [userController::class, 'cashier'])->middleware(receiverRegisterMiddleware::class);
            Route::post("/banner", [userController::class, 'cashier'])->middleware(receiverRegisterMiddleware::class);
            Route::post("", [userController::class, 'store'])->middleware(userRegisterMiddleware::class);

            Route::post("/update-password", [userController::class, 'updatePassword'])->middleware(userUpdatePasswordMiddleware::class);
        });

        Route::prefix("/product")->group(function ()
        {
            Route::get("/{current_page}", [productController::class, 'index']);
            Route::get("/all", [productController::class, 'allProducts']);
            Route::post("", [productController::class, 'store'])->middleware(registerProductsMiddleware::class);
            
            Route::post("/store", [productController::class, 'storeToInventoryStore'])->middleware(registerProductsMiddleware::class);
            Route::put("/store/procurement", [productController::class, 'procurementInventoryStore'])->middleware(updateProductsStoreMiddleware::class);
            Route::get("/store/{current_page}", [productController::class, 'allProductsInStore']);
            Route::put("/store/procurement/warehouse", [productController::class, 'procurementWarehouse'])->middleware(procurementProductByStoreMiddleware::class);

            Route::post("/add", [productController::class, 'product'])->middleware(registerOneProductMiddleware::class);
            Route::put("/procurement", [productController::class, 'procurement'])->middleware(procurementProductMiddleware::class);
        });

        Route::prefix("/drink")->group(function ()
        {
            Route::get("/{current_page}", [drinkController::class, 'index']);
            Route::post("/", [drinkController::class, 'store'])->middleware(createDrinkMiddleware::class);
            Route::post("/search", [drinkController::class, 'search'])->middleware(searchDrinkMiddleware::class);
            Route::put("", [drinkController::class, 'update'])->middleware(drinkUpdatedImgMiddleware::class);
            Route::put("/{id}", [drinkController::class, 'updateDrink'])->middleware(updatedDrinkImgMiddleware::class);
        });


        Route::prefix("/sale")->group(function ()
        {
            Route::get("", [saleController::class, 'index']);
            Route::get("/statistics", [saleController::class, 'statistics']);
            Route::get("/statistics/{startDate}/{endDate}", [saleController::class, 'statisticByDate']);
            Route::post("/statistics", [saleController::class, 'statisticInIntervaleByDate'])->middleware(saleStatisticsMiddleware::class);
            Route::get("/statistics-by-date/{endDate}", [saleController::class, 'statisticEndDateWithSixPreviousDays']);
            Route::post("", [saleController::class, 'store'])->middleware(saleProductsMiddleware::class);

            Route::post("/store", [saleController::class, 'saleInStore'])->middleware(saleProductsMiddleware::class);
            Route::get("/statistics/store", [saleController::class, 'statisticsInStore']);
            Route::get("/statistics/store/{startDate}/{endDate}", [saleController::class, 'statisticByDateInStore']);
            Route::get("/statistics-by-date/store/{endDate}", [saleController::class, 'statisticEndDateWithSixPreviousDaysInStore']);  
            Route::post("/statistics/store", [saleController::class, 'statisticInIntervaleByDateInStore'])->middleware(saleStatisticsMiddleware::class);

            // Route::put("", [saleController::class, 'update'])->middleware(drinkUpdatedImgMiddleware::class);
        });

        Route::prefix("/historic")->group(function ()
        {
            Route::get("/warehouse/{current_page}", [historicInventoryDrinkController::class, 'index']);
            Route::get("/store/{current_page}", [historicInventoryDrinkController::class, 'storeIndex']);
            // Route::get("/statistics", [historicInventoryDrinkController::class, 'statistics']);
            // Route::get("/statistics/{startDate}/{endDate}", [historicInventoryDrinkController::class, 'statisticByDate']);
            // Route::post("/statistics", [historicInventoryDrinkController::class, 'statisticInIntervaleByDate'])->middleware(saleStatisticsMiddleware::class);
            // Route::get("/statistics-by-date/{endDate}", [historicInventoryDrinkController::class, 'statisticEndDateWithSixPreviousDays']);
            // Route::post("", [historicInventoryDrinkController::class, 'store'])->middleware(saleProductsMiddleware::class);

            // Route::post("/store", [historicInventoryDrinkController::class, 'saleInStore'])->middleware(saleProductsMiddleware::class);
            // Route::get("/statistics/store", [historicInventoryDrinkController::class, 'statisticsInStore']);
            // Route::get("/statistics/store/{startDate}/{endDate}", [historicInventoryDrinkController::class, 'statisticByDateInStore']);
            // Route::get("/statistics-by-date/store/{endDate}", [historicInventoryDrinkController::class, 'statisticEndDateWithSixPreviousDaysInStore']);  
            // Route::post("/statistics/store", [historicInventoryDrinkController::class, 'statisticInIntervaleByDateInStore'])->middleware(saleStatisticsMiddleware::class);

            // Route::put("", [saleController::class, 'update'])->middleware(drinkUpdatedImgMiddleware::class);
        });

        Route::prefix("/payment")->group(function ()
        {
            // Route::get("", [drinkController::class, 'index']);
            Route::post("", [PaymentController::class, 'store'])->middleware(paymentMiddleware::class);
            Route::get("/{id}", [PaymentController::class, 'show']);
            // Route::put("", [drinkController::class, 'update'])->middleware(drinkUpdatedImgMiddleware::class);
            // Route::put("/{id}", [drinkController::class, 'updateDrink'])->middleware(updatedDrinkImgMiddleware::class);
        });

        Route::prefix("/settings")->group(function ()
        {
            Route::post("", [SettingsController::class, 'store']);
        });
    });
});

Route::any('{any}', function () {
    return response()->json([
        'error' => true,
        'message' => 'Route not found'
    ], 404);
})->where('any', '.*');

Route::get('/unauthorized', function () {
    return response()->json(['error' => 'Unauthorized access'], 401);
})->name('unauthorized');