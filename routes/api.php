<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckAdminUser;
use App\Http\Middleware\CheckSalonUser;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AuthenticateApi;
use App\Http\Middleware\CheckclientUser;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SalonController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\VenteController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\EmployeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CoiffureController;
use App\Http\Controllers\RendezVouController;
use App\Http\Controllers\AccessoireController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\HistoriqueServiceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PerformanceController;
use App\Http\Controllers\StatistiqueController;
use App\Http\Controllers\TypeCoiffureController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\ForgotPasswordController;



// Route pour l'inscription (register)
Route::resource('/register', UserController::class)->except(['update' , 'destroy']);;
Route::put('users/{id}', [UserController::class , 'updateUser'])->name('users.update');
Route::put('users/{id}',  [UserController::class , 'destroyUser'])->name('users.destroy');
Route::post('/register_client' , [ClientController::class , 'register_client'] );


Route::get('/verify-email/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');


// Route pour la connexion (login)
Route::post('/login', [LoginController::class, 'login']);
Route::post('login-with-token/{token}', [LoginController::class, 'loginWithToken']);
Route::get('VerifyActif/{id}' , [SalonController::class , 'VerifyActif']);

//Reset password

Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->middleware('guest')->name('password.reset');



Route::middleware("auth:sanctum")->group(function () {
    // Routes protégées ici

    Route::resource('historic', HistoriqueServiceController::class);
    Route::resource('sale', VenteController::class);
    Route::resource('notif', NotificationController::class);
    Route::get('/reminder',[NotificationController::class , 'sendReminders']);
    Route::resource('statistic', StatistiqueController::class);
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::get('/performances', [PerformanceController::class, 'getPerformanceData'])->name('performances.datas');
    Route::get('/getUserType' , [LoginController::class , 'getUserType']);


    Route::middleware([CheckclientUser::class])->group(function () {
        Route::resource('client', ClientController::class);
        Route::get('/services-client', [HistoriqueServiceController::class, 'getServices'])->name('service.getservices');
        Route::get('/client-services', [ClientController::class, 'getClientServices']);
        Route::resource('rdv', RendezVouController::class);
        Route::get('/dispo/{id_salon}', [EmployeController::class, 'getEmployeDispo']);
        Route::get('getImages' , [TypeCoiffureController::class , 'getImage']);
        Route::get('getAllHairstyles', [TypeCoiffureController::class, 'getAllHairstyles']);
        Route::get('/accessoires/{hairstyle_type_id}/{salon_id}', [AccessoireController::class, 'getaccessory']);
        Route::get('getAllHairstyles/{salonId}', [TypeCoiffureController::class, 'getAllHairstyles']);
        Route::get('getImagesBySalon/{salon_id}', [TypeCoiffureController::class, 'getImagesBySalon']);
        Route::get('getAccompte/{id}' , [RendezVouController::class , 'getAccompte']);
        Route::post('initiatePayment/{transactionId}/{id_appointment}', [PaymentController::class, 'initiatePayment']);
        Route::get('getsomme' , [RendezVouController::class , 'getsomme']);
        Route::get('getmontantrestant/{id}' , [RendezVouController::class , 'getmontantrestant']);
        Route::post('solderdv/{transactionId}/{id_appointment}', [RendezVouController::class, 'solderdv']);

        Route::get('getClientrdv' , [ClientController::class , 'getClientrdv']);
        Route::get('getRendezVousForClient' , [ClientController::class , 'getRendezVousForClient']);
        Route::get('getHours/{hairstyle}/{date}' , [SalonController::class , 'getHours']);
        Route::get('/getClientId', [ClientController::class, 'getClientId']);













    });

    Route::middleware([CheckSalonUser::class])->group( function () {
        Route::resource('salon',SalonController::class);
        Route::resource('employe',EmployeController::class);
        Route::resource('hairstyle', TypeCoiffureController::class);
        Route::resource('accessory', AccessoireController::class);
        Route::resource('supplier', FournisseurController::class);
        Route::resource('stock', StockController::class);
        Route::get('/coiffure' , [CoiffureController::class , 'index']);
        Route::get('getSalonhairstyle' , [TypeCoiffureController::class , 'getSalonhairstyle']);
        Route::post('/employehairstyle' , [EmployeController::class , 'employehairstyles']);
        Route::post('/accessoirehairstyle' , [AccessoireController::class , 'accessoirehairstyle']);
        Route::get('/salon_appointments' , [RendezVouController::class , 'salon_appointments']);
        Route::get('/salon_employes' , [EmployeController::class , 'salon_employes']);
        Route::get('salon_coiffures' , [TypeCoiffureController::class , 'salon_coiffures']);
        Route::post('/sendInvitation' , [SalonController::class , 'sendInvitation']);
        Route::get('/getSalonAppointments' , [RendezVouController::class , 'getSalonAppointments']);
        Route::get('sendEvaluationLink' , [EvaluationController::class, 'sendEvaluationLink']);
        Route::post('/ActiveSalonInfo' , [SalonController::class , 'ActiveSalonInfo']);
        Route::get('getSalonId' , [SalonController::class , 'getSalonId']);
        Route::get('getEmployeInfo/{id}' , [EmployeController::class , 'getEmployeInfo']);
        Route::get('getInfoSalon' , [SalonController::class , 'getInfoSalon']);
        Route::get('getRendezVousForSalon' , [RendezVouController::class , 'getRendezVousForSalon']);
        Route::get('/salonhairstyleandaccessory' , [AccessoireController::class , 'salonhairstyleandaccesssory']);
        Route::get('Terminer/{id}' , [RendezVouController::class , 'Terminer']);




 
    });
    Route::middleware([CheckAdminUser::class])->group( function () {
        Route::post('coiffures', [CoiffureController::class, 'store']);
        Route::put('coiffures/{id}', [CoiffureController::class, 'update']);
        Route::delete('coiffures/{id}', [CoiffureController::class, 'destroy']);
        Route::put('ActiveSalon/{id}' , [SalonController::class , 'ActiveSalon']);
        Route::get('getInfoSalons' , [SalonController::class , 'getInfoSalons']);//admin
        Route::get('detailsalon' , [SalonController::class , 'detailsalon']);//admin
        Route::get('getAllcoiffures' , [CoiffureController::class,'getAllcoiffures']);//admin
        Route::get('getsalondata/{id}' , [SalonController::class , 'getsalondata']);//admin



    });

});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Routes pour les récupérer les images des types de coiffure
Route::get('/getSameCoiffure' , [TypeCoiffureController::class,'getSalonsWithSameCoiffure']);
Route::get('/salons' , [SalonController::class , 'getAllsalons']);
Route::get('getSalonshairstyle/{id_salon}' , [TypeCoiffureController::class , 'getSalonshairstyle']);
Route::get('getSalonwomanHairstyles' , [TypeCoiffureController::class , 'getSalonwomanHairstyles']);
Route::get('getSalonmanHairstyles' , [TypeCoiffureController::class , 'getSalonmanHairstyles']);
Route::get('getSalonchildHairstyles' , [TypeCoiffureController::class , 'getSalonchildHairstyles']);
Route::get('getAllHairstylesAcc', [TypeCoiffureController::class, 'getAllHairstylesAcc']);
Route::resource('review', EvaluationController::class);
Route::get('/getImagesBySalon/{id}' , [TypeCoiffureController::class,'getImagesBySalon']);
