<?php
use App\Models\Role;
use App\Models\Sonod;
use App\Models\Payment;
use App\Models\TikaLog;
use App\Models\Visitor;
use App\Models\Uniouninfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Cache\RedisTaggedCache;
use App\Http\Controllers\SonodController;
use App\Http\Controllers\fronendController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AplicationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HoldingtaxController;
use App\Http\Controllers\UniouninfoController;
use App\Http\Controllers\ExpenditureController;
use App\Http\Controllers\NotificationsController;

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


Route::get('/apllication/document/{id}',[fronendController::class,'apllication_Document'])->name('apllication_Document');
Route::get('/license/{id}',[fronendController::class,'license'])->name('license');


Route::get('/applicantion/full/copy/{id}',[fronendController::class,'applicantCopy']);




Route::get('/passGen/{pass}', function ($pass) {
    return  hash::make($pass);
});




Route::get('/sonod/payment/success/{id}', [SonodController::class,'sonodpaymentSuccessView']);

Route::get('/payment/success', function (Request $request) {
    // return $request->all();
    $transId = $request->transId;

    $payment = Payment::where(['trxId' => $transId])->first();

    $redirect = "/payment/success/confirm?transId=$transId";

    echo "
    <h3 style='text-align:center'>Please wait 10 seconds.This page will auto redirect you</h3>
    <script>
    setTimeout(() => {
    window.location.href='$redirect'
    }, 10000);
    </script>
    ";
    // return redirect("/payment/success/confirm?transId=$transId");
});
Route::get('/payment/success/confirm', [SonodController::class,'sonodpaymentSuccess']);

Route::get('/l/f/{id}', [SonodController::class,'sonodpayment']);

// Route::get('/sonod/payment/{id}', [SonodController::class,'sonodpayment']);







Route::post('unionCreate', [UniouninfoController::class,'unionCreate']);
Auth::routes([
    'login'=>false,
]);
Route::post('login',[LoginController::class,'login']);
Route::post('logout',[LoginController::class,'logout']);
// Route::group(['middleware' => ['is_admin']], function() {
// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
// });
// Route::group(['middleware' => ['CustomerMiddleware']], function() {
// Route::get('/sub', [App\Http\Controllers\HomeController::class, 'sub'])->name('sub');
// });






Route::get('/sonod/payment/success/{id}', [SonodController::class,'sonodpaymentSuccessView']);
// Route::get('/payment/success', [SonodController::class,'sonodpaymentSuccess']);
Route::get('/sonod/payment/{id}', [SonodController::class,'sonodpayment']);
Route::get('/sonod/{name}/{id}', [SonodController::class,'sonodDownload']);
Route::get('/invoice/{name}/{id}', [SonodController::class,'invoice']);
Route::get('/card/{name}/{id}', [SonodController::class,'userDocument']);
Route::get('/information/{name}/{id}', [SonodController::class,'userinformation']);
Route::get('/pay/holding/tax/{id}', [HoldingtaxController::class,'holding_tax_pay_Online']);

Route::get('/holdingPay/success', [HoldingtaxController::class,'holdingPaymentSuccess']);

Route::get('/holding/tax/invoice/{id}', [HoldingtaxController::class,'holdingPaymentInvoice']);

Route::get('/report/export', [PaymentController::class,'export']);

Route::get('/cashbook/download', [ExpenditureController::class,'cashbook_download']);

Route::get('/secretary/approve/{id}', [SonodController::class,'SecretariNotificationApprove']);
Route::get('/chairman/approve/{id}', [SonodController::class,'ChairnamNotificationApprove']);
Route::get('/secretary/pay/{id}', [SonodController::class,'SecretariNotificationPay']);
Route::get('/allow/application/notification', function () {







    return view('applicationNotification');


    });
Route::group(['prefix' => 'dashboard','middleware' => ['auth']], function() {


    Route::get('/application/report/{id}',[AplicationController::class,'download_report']);

Route::get('/report/download', [SonodController::class,'ReportDownload']);



    Route::get('/{vue_capture?}', function () {
        // return   Auth::user()->roles->permission;
        $roles = Role::all();
        return view('layout',compact('roles'));
    })->where('vue_capture', '[\/\w\.-]*')->name('dashboard');
});
Route::get('/{vue_capture?}', function () {


        // return  Uniouninfo::find(1);
 $uniounDetials['defaultColor']  = 'green';
      $uniounDetials = json_decode(json_encode($uniounDetials));
     return view('frontlayout',compact('uniounDetials'));

})->where('vue_capture', '[\/\w\.-]*')->name('frontend');
