<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TaxController;
use App\Http\Controllers\Api\DateController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PartyController;
use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Api\AddItemController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ItemDetailsController;
use App\Http\Controllers\Api\TempInvoiceController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [UserController::class, 'register']);


Route::controller(LoginController::class)->group(function () {
    Route::post('login', 'login');
});



Route::middleware('auth:api')->group(function () {
    Route::post('categories', [CategoryController::class, 'createCategory']);
    Route::post('party', [PartyController::class, 'createParty']);
    Route::post('add-item', [AddItemController::class, 'addItem']);
    Route::post('add-item-details', [AddItemController::class, 'addItemDetail']);
    Route::post('add-temp-invoice',[InvoiceController::class, 'addItem']);
    Route::get('invoice', [InvoiceController::class, 'show_invoice']);
    Route::get('TotalTax',[TaxController::class,'total_tax']);
    Route::put('invoice/{id}', [InvoiceController::class, 'update_invoice']);
    Route::get('CategoryData',[CategoryController::class, 'index']);
    Route::get('StockData',[StockController::class, 'index']);
    Route::get('collect_amount',[InvoiceController::class,'index']);
    Route::get('amoutDetails',[InvoiceController::class,'getAmountDetails']);
    Route::post('userBalance',[InvoiceController::class,'getUserBalance']);
    
    Route::post('update_user',[UserController::class,'updateUser']);
    Route::post('update_Category',[CategoryController::class,'updateCategory']);
    Route::post('ext_qty',[AddItemController::class,'updateExtraQuantity']);
    Route::post('FinaId',[InvoiceController::class,'updateFinalId']);
    Route::post('update_stock',[AddItemController::class,'updateOpeningStock']);
    Route::post('update_number',[UserController::class,'updatePhoneNumber']);
    Route::get('get_bill',[InvoiceController::class,'getUserInvoices']);
    Route::get('categories/user',[CategoryController::class,'getUserCategories']);
    Route::get('party_name',[PartyController::class,'getUserParties']);
    Route::get('signature',[UserController::class,'getSignature']);
    Route::get('fieldList',[AddItemController::class,'getItemList']);
    Route::get('item_details',[ItemDetailsController::class,'getItemDetails']);
    Route::get('invoice_details',[InvoiceController::class,'getInvoiceDetails']);
    Route::get('bill_invoice',[InvoiceController::class,'getBillInvoice']);
    Route::delete('delete_party',[PartyController::class,'removeUserDetails']);
    Route::put('update_invoice',[InvoiceController::class,'updateInvoice']);
    Route::post('update_item',[AddItemController::class,'updateItem']);
    Route::post('update_name',[UserController::class,'updateUserName']);
    Route::post('get_business',[UserController::class,'getBusiness']);
    Route::get('get_date_item',[AddItemController::class,'dateItem']);


    //temp_inovice
    Route::post('add_data_temp',[TempInvoiceController::class,'addDataTemp']);
    Route::delete('delete_data_temp',[TempInvoiceController::class,'deleteItem']);
    Route::get('get_total_items',[TempInvoiceController::class,'getTotalItems']);
    Route::get('get_total_qty',[TempInvoiceController::class,'getTotalItemQty']);
    Route::get('get_sales_price',[TempInvoiceController::class,'getTotalSalesPrice']);
    Route::post('update_tempInvoice',[TempInvoiceController::class,'updateTempInvoice']);
    Route::post('update_tempDetails',[TempInvoiceController::class,'updateTempDetails']);
    Route::get('get_temp_invoice',[TempInvoiceController::class,'getTempInvoice']);
    Route::get('get_temp_qty',[TempInvoiceController::class,'getTempItemQty']);
    Route::get('get_temp_item',[TempInvoiceController::class,'getTempItem']);

    

});
