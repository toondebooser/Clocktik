<?php

use App\Http\Controllers\AddCustomTimesheetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeleteTimesheetController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MyWorkersController;
use App\Http\Controllers\PdfExportController;
use App\Http\Controllers\SpecialsController;
use App\Http\Controllers\TimeclockController;
use App\Http\Controllers\TimesheetController;
use App\Http\Controllers\UpdateTimesheetController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UsersheetsController;
use App\Http\Controllers\WorkersController;
use Illuminate\Http\Response;
use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

Route::get('/', [HomeController::class, 'show'])->name('home');
Route::get('/login', [HomeController::class, 'login'])->name('login');
Route::get('/logout', [HomeController::class, 'logout'])->name('logout');
Route::get('/registration-form', [UserController::class, 'registrationForm'])->name('registration-form');
Route::post('/user-registration', [UserController::class, 'registrate'])->name('registrate');
Route::post('/authentication', [HomeController::class, 'authentication'])->name('authentication');
Route::match(['get', 'post'],'/dashboard', [DashboardController::class, 'userDashboard'])->name('dashboard')->middleware('worker');
Route::get('/dashboard-start', [TimeclockController::class, 'startWorking'])->name('start')->middleware('worker');
Route::get('/dashboard-break', [TimeclockController::class, 'break'])->name('break')->middleware('worker');
Route::get('/dashboard-stop-break', [TimeclockController::class, 'stopBreak'])->name('stopBreak')->middleware('worker');
Route::get('/dashboard-stop', [TimeclockController::class, 'stop'])->name('stop')->middleware('worker');
Route::get('/my-profile', [UsersheetsController::class, 'myProfile'])->name('myProfile')->middleware('worker');
Route::match(['get', 'post'],'/my-profile-post', [UsersheetsController::class, 'myProfile'])->name('getData')->middleware('auth');
Route::get('/make-timesheet/{id}', [TimesheetController::class, 'makeTimesheet'])->name('makeTimesheet')->middleware('auth');
Route::post('/new-timesheet-form', [AddCustomTimesheetController::class, 'customTimesheetForm'])->name('timesheetForm')->middleware('admin');
Route::post('/add-new-timesheet', [TimesheetController::class, 'addNewTimesheet'])->name('newTimesheet')->middleware('admin');
Route::get('/my-workers', [MyWorkersController::class, 'fetchWorkers'])->name('myWorkers')->middleware('admin');
Route::get('/forWorker', [SpecialsController::class, 'forWorker'])->name('forWorker')->middleware('admin');
Route::match(['get', 'post'], '/update-timesheet/{id}/{timesheet}', [UpdateTimesheetController::class, 'updateForm'])->name('update')->middleware('admin');
Route::post('/update-worker-timesheet', [UpdateTimesheetController::class, 'updateTimesheet'])->name('updateTimesheet')->middleware('admin');
Route::match(['get', 'post'], '/specials', [SpecialsController::class, 'specials'])->name('specials')->middleware('admin');
Route::post('/setSpecial', [TimesheetController::class, 'setSpecial'])->name('setSpecial')->middleware('admin');
<<<<<<< HEAD
Route::get('/export-pdf', function () {

    $user = json_decode(request('userJSONstring'));
    $timesheet = json_decode(request('timesheetJSONstring'));
    $total = request('totalJSONstring');
    $type = request('type');
    $pdf = Pdf::loadView('pdf', compact('user', 'timesheet', "total"));


    // Define the filename for the download
    $filename = 'Uurrooster'.'-'.$user->name.'-'.date('F', strtotime($timesheet[0]->Month)) . '.pdf'; // Customize the filename as needed

    // Set the filename in the Content-Disposition header
    $response = new Response($pdf->output());
    $response->header('Content-Type', 'application/pdf');
    $response->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    if ($type == 'preview')
    {
    return $pdf->stream();
    }elseif($type =='download')
    {
    return $pdf->download($filename);
    }
})->name('exportPdf')->middleware('admin');
=======
Route::get('/export-pdf',[PdfExportController::class, 'exportPdf'])->name('exportPdf')->middleware('admin');
Route::post('/delete-timesheet', [DeleteTimesheetController::class, 'deleteTimesheet'])->name('delete')->middleware('admin');
>>>>>>> f58775f77562ad71ad161a7d605dfe98ddd52580
