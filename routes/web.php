<?php

use App\Http\Controllers\MessageController;
use App\Http\Controllers\RollController;
use Danhunsaker\BC;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::post('/CreateUsersRole',[RollController::class,'CreateUsersRole'])->name('CreateUsersRole');
Route::post('/RemoveUsersRole',[RollController::class,'RemoveUsersRole'])->name('RemoveUsersRole');
Route::post('/ShowUsersWithOutRole',[RollController::class,'ShowUsersWithOutRole'])->name('ShowUsersWithOutRole');
Route::post('/ShowUsersWithRole',[RollController::class,'ShowUsersWithRole'])->name('ShowUsersWithRole');
//
Route::post('/CreateRoll',[RollController::class,'create'])->name('CreateRoll');
Route::post('/ShowRoll',[RollController::class,'ShowRoll'])->name('ShowRoll');
Route::get('/RemoveRoll',[RollController::class,'RemoveRoll'])->name('RemoveRoll');
Route::post('/CreatePermission',[RollController::class,'createPermission'])->name('createPermission');
Route::post('/ShowPermission',[RollController::class,'ShowPermission'])->name('ShowPermission');
Route::get('/RemovePermission',[RollController::class,'RemovePermission'])->name('RemovePermission');
//
//از اینجا برای چت

Route::get('/',[MessageController::class,'index'])->name('chat');
Route::get('/messages',[MessageController::class,'fetchMessage']);
Route::post('/messages',[MessageController::class,'sendMessage']);
Route::post('/endChat',[MessageController::class,'endChat']);
Route::post('/selectUser',[MessageController::class,'selectSupportForUser']);
Route::post('/showUser',[MessageController::class,'showUser']);
Route::post('/supportable',[MessageController::class,'supportable']);
Route::post('/Supports',[MessageController::class,'supports']);
Route::post('/ban',[MessageController::class,'ban']);
Route::post('/rateToSupport',[MessageController::class,'rateToSupport']);
//تا اینجا
Route::get('/dashboard',[RollController::class,'show'])->middleware(['auth'])->name('dashboard');
//Route::get('/showUsers', function () {
//      $users=\App\Models\Message::all();
//  return $users;
//});
Route::get('/clear', function() {
    $configCache = Artisan::call('config:cache');
    $clearCache = Artisan::call('optimize');
    // return what you want
});
Route::get('/math',function (){
    $a = '0.00000000000000000000000000000000000000000000005';
    $b = '0.00000000000000000000000000000000000000000000004';
     // outputs 0.3
//    $test = 0.1 + 0.2;
    $test= BC::add('0.100000000000000000000001','0.2',30);

    echo (BC::comp($test,'0.3')) ? 'true' : 'false';
//    return BC::sub($a,$b,50);

//    return BC::max( ['99999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999','456666666666666666666666668711111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111111179879789.546678687777777777777777796786'],30);
//    1.732050807568877293527446341505;
//    1.7320508075688772;
});
require __DIR__.'/auth.php';
