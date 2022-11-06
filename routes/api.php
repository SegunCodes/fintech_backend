<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\User\AccountController;
use App\Http\Controllers\User\GroupedController;
use App\Http\Controllers\User\flowController;
use App\Http\Controllers\User\BillController;
use App\Http\Controllers\User\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//USER  Auth ROUTES
Route::post('/signup',[UserController::class, 'register']);
Route::post('/user-otp',[UserController::class, 'verifyUserOtp']);
Route::post('/login',[UserController::class, 'login']);
Route::post('/reset',[UserController::class, 'sendResetCode']);
Route::post('/check-reset-code',[UserController::class, 'verifyResetCode']);
Route::post('/reset-password',[UserController::class, 'resetPassword']);
//USER PROTECTED ROUTES
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user',[UserController::class, 'User']);
    Route::post('/logout',[UserController::class, 'logout']);
    //User account settings routes
    Route::post('/update-account',[AccountController::class, 'personalInfo']);
    Route::post('/update-password',[AccountController::class, 'updatePassword']);
    Route::post('/two-factor-auth',[AccountController::class, 'twoFactorAuth']);
    Route::post('/next-of-kin',[AccountController::class, 'nextOfKin']);
    Route::post('/personal-identity',[AccountController::class, 'personalIdentity']);
    Route::post('/bank-details',[AccountController::class, 'bankDetails']);
    Route::delete('/delete-bank/{id}',[AccountController::class, 'deleteBank']);
    Route::post('/debit-card',[AccountController::class, 'debitCard']);
    Route::delete('/delete-card/{id}',[AccountController::class, 'deleteDebitCard']);
    Route::get('/get-user-card',[AccountController::class, 'getCard']);
    // Route::get('/view-card/{id}',[AccountController::class, 'viewCard']);
    //User savings,withdrawal and top-up routes
    Route::post('/fixed-savings',[flowController::class, 'fixedSaving']);
    Route::post('/personal-savings',[flowController::class, 'personalSaving']);
    Route::post('/top-up',[flowController::class, 'topUP']);
    Route::post('/withdrawal',[flowController::class, 'withdrawal']);
    Route::post('/quicksaving',[flowController::class, 'quickSaving']);
    //grouped and thrift savings and invitation routes
    Route::post('/group-savings',[GroupedController::class, 'groupSaving']);
    Route::post('/thrift',[GroupedController::class, 'thrift']);
    Route::post('/invite-group',[GroupedController::class, 'inviteMember']);
    Route::post('/invite-thrift',[GroupedController::class, 'inviteThrift']);
    //accept and reject invite routes
    //payBills routes
    Route::post('/airtime',[BillController::class, 'payAirtime']);
    Route::post('/cable',[BillController::class, 'payCable']);
    Route::post('/electricity',[BillController::class, 'payElectricity']);
    Route::post('/internet',[BillController::class, 'payInternet']);
    Route::post('/data',[BillController::class, 'payData']);
    //investment routes
});



//ADMIN ROUTES
Route::post('/create-admin',[AdminController::class, 'createAdmin']);
Route::post('/add-admin',[AdminController::class, 'addAdmin']);
Route::post('/admin-login',[AdminController::class, 'adminLogin']);
Route::post('/admin-otp',[AdminController::class, 'verifyAdminOtp']);
//admin functionality routes
Route::get('/all-users',[UsersController::class, 'getUsers']);
Route::get('/view-user/{id}',[UsersController::class, 'viewUser']);
Route::post('/disable-user/{id}',[UsersController::class, 'disableUser']);
Route::post('/activate-user/{id}',[UsersController::class, 'activateUser']);
Route::delete('/delete-user/{id}',[UsersController::class, 'deleteUser']);
//admin transaction routes
Route::get('/all-transactions',[TransactionController::class, 'getTransactions']);
Route::get('/fixed-transactions',[TransactionController::class, 'fixedTransactions']);
Route::get('/group-transactions',[TransactionController::class, 'groupTransactions']);
Route::get('/personal-transactions',[TransactionController::class, 'personalTransactions']);
Route::get('/investment-transactions',[TransactionController::class, 'investmentTransactions']);
Route::get('/thrift-transactions',[TransactionController::class, 'thriftTransactions']);
Route::get('/airtime-transactions',[TransactionController::class, 'airtimeTransaction']);
Route::get('/cable-transactions',[TransactionController::class, 'cableTransaction']);
Route::get('/electricity-transactions',[TransactionController::class, 'electricityTransaction']);
Route::get('/internet-transactions',[TransactionController::class, 'internetTransaction']);
Route::get('/data-transactions',[TransactionController::class, 'dataTransaction']);