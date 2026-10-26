<?php

use App\Http\Controllers\LandingPage\LandingController;
use Illuminate\Support\Facades\Auth;
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

Auth::routes();

// -------------------------------------------------------
// ADMS Push Server — ZKTeco / Solution A200
// Mesin fingerprint push data ke endpoint ini.
// Arahkan mesin ke: http://<IP_SERVER>/iclock
// -------------------------------------------------------
Route::prefix('iclock')->group(function () {
    Route::get('/cdata',      [App\Http\Controllers\FingerprintPushController::class, 'handshake']);
    Route::post('/cdata',     [App\Http\Controllers\FingerprintPushController::class, 'receiveRecords']);
    Route::get('/getrequest', [App\Http\Controllers\FingerprintPushController::class, 'getRequest']);
    Route::post('/devicecmd', [App\Http\Controllers\FingerprintPushController::class, 'deviceCmd']);
});

Route::get('/', [LandingController::class, 'index'])->name('landing-page');
Route::get('/about', [LandingController::class, 'about'])->name('landing-page.about');
Route::get('/service', [LandingController::class, 'service'])->name('landing-page.service');


Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', App\Http\Livewire\Dashboard\Index::class)->name('dashboard');

    Route::get('/master-task', App\Http\Livewire\MasterTask\Index::class)->name('master-task')->middleware(['permission:mastertask']);
    Route::get('/task', App\Http\Livewire\Task\Index::class)->name('task');
    Route::get('/getChartData', 'App\Http\Livewire\Task\Index@getChartData');
    Route::get('/detail-task/{id}', App\Http\Livewire\DetailTask\Index::class)->name('detail-task')->middleware(['permission:task']);
    Route::get('/close-periode', App\Http\Livewire\ClosePeriode\Index::class)->name('close-periode')->middleware(['permission:closeperiode']);

    Route::get('/canteen', App\Http\Livewire\Canteen\Index::class)->name('canteen')->middleware(['permission:scanqr']);
    Route::get('/canteen-fingerprint', App\Http\Livewire\Canteen\ScanFingerprint::class)->name('scan-fingerprint')->middleware(['permission:scanfingerprint']);
    Route::get('/fingerprint-device', App\Http\Livewire\Canteen\FingerprintDevice::class)->name('fingerprint-device')->middleware(['superadmin']);
    Route::get('/canteen-result', App\Http\Livewire\Canteen\CanteenResult::class)->name('canteenresult')->middleware(['permission:employeeschedule']);
    Route::get('/schedule', App\Http\Livewire\Canteen\EmployeeScheduleController::class)->name('employeeschedule')->middleware(['permission:employeeschedule']);
    Route::get('/canteenpage', App\Http\Livewire\Canteen\PageCanteen::class)->name('canteenpage')->middleware(['permission:recapcanteen']);

    //master
    Route::get('/profilsaya', App\Http\Livewire\Profil\Index::class)->name('profilsaya');
    Route::get('/role', App\Http\Livewire\Role\Index::class)->name('role')->middleware(['superadmin']);
    Route::get('/user', App\Http\Livewire\User\Index::class)->name('user')->middleware(['superadmin']);
    Route::get('/department', App\Http\Livewire\Department\Index::class)->name('department')->middleware(['superadmin']);
    Route::get('/mealshift', App\Http\Livewire\Canteen\MealShiftController::class)->name('mealshift')->middleware(['superadmin']);
    Route::get('/user-level', App\Http\Livewire\UserLevel\Index::class)->name('user-level')->middleware(['superadmin']);
    Route::get('/cost-center', App\Http\Livewire\CostCenter\Index::class)->name('cost-center')->middleware(['superadmin']);
    Route::get('/coa', App\Http\Livewire\Coa\Index::class)->name('coa')->middleware(['superadmin']);
    Route::get('/branch', App\Http\Livewire\Branch\Index::class)->name('branch')->middleware(['superadmin']);
    Route::get('/status-employee', App\Http\Livewire\StatusEmployee\Index::class)->name('status-employee')->middleware(['superadmin']);
    Route::get('/price', App\Http\Livewire\Price\Index::class)->name('price')->middleware(['superadmin']);

    Route::get('/master-kinerja', App\Http\Livewire\Performances\Index::class)->name('performance')->middleware(['permission:performance']); //spv
    Route::get('/evaluation', App\Http\Livewire\Evaluation\Index::class)->name('evaluation')->middleware(['permission:evaluation']); //ub
    Route::get('/discipline', App\Http\Livewire\Discipline\Index::class)->name('discipline')->middleware(['permission:discipline']);
    Route::get('/warning-letter', App\Http\Livewire\WarningLetter\Index::class)->name('warning-letter')->middleware(['permission:warningletter']);
    Route::get('/evaluation-result', App\Http\Livewire\EvaluationResult\Index::class)->name('evaluation-result')->middleware(['permission:evaluationresult']); //spv
    Route::get('/self-evaluation', App\Http\Livewire\EvaluationResult\IndexSelf::class)->name('self-evaluation')->middleware(['permission:selfevaluation']); //ub

    Route::get('/borrowingroom', App\Http\Livewire\Borrowing\Index::class)->name('pinjamruangan');
    Route::get('/roommeet', App\Http\Livewire\MeetRoom\Index::class)->name('ruangan')->middleware(['superadmin']);
});
