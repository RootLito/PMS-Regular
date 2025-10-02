<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;

//MAIN
Route::get('/', function () {
    return view('main');
});

//AUTH
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');









// ROUTES
Route::middleware('auth')->group(function () {

    Route::get('/regular-dashboard', function () {
        return view('regular.regular-dashboard');
    });

    Route::get('/regular-employee', function () {
        return view('regular.regular-employee');
    })->name('regular-employee');

    Route::get('/regular-employee/new', function () {
        return view('regular.regular-new-emp');
    })->name('employee.new');

    Route::get('/regular-employee/update/{id}', function ($id) {
        return view('regular.employee.update-emp', ['id' => $id]);
    })->name('employee.update');

    Route::get('/regular-payroll', function () {
        return view('regular.regular-payroll');
    });

    Route::get('/regular-payroll/voucher', function () {
        return view('regular.raw.voucher');
    })->name('payroll.voucher');

    Route::get('/regular-computation', function () {
        return view('regular.regular-computation');
    })->name('computation');

    Route::get('/regular-contribution/new', function () {
        return view('regular.contribution.new-cont');
    })->name('contribution.new');

    Route::get('/regular-computation/voucher', function () {
        return view('regular.raw.voucher');
    })->name('computation.voucher');

    Route::get('/regular-configuration/signatory', function () {
        return view('regular.regular-signatory');
    })->name('signatory');

    Route::get('/regular-configuration/salary', function () {
        return view('regular.regular-monthly-rate');
    })->name('salary');

    Route::get('/regular-contribution', function () {
        return view('regular.regular-contribution');
    })->name('contribution');

    Route::get('/regular-configuration/designation', function () {
        return view('regular.regular-designation');
    })->name('designation');

    Route::get('/regular-archive', function () {
        return view('regular.regular-archive');
    })->name('archive');

    Route::get('/regular-analysis', function () {
        return view('regular.regular-analysis'); 
    })->name('analysis');

    Route::get('/regular-configuration/position', function () {
        return view('regular.regular-position');
    })->name('position');

    Route::get('/regular-configuration', function () {
        return view('regular.regular-dashboard'); // or create regular-config.blade.php if needed
    })->name('configuration');

    Route::get('/regular-configuration/account', function () {
        return view('regular.regular-account');
    })->name('account');
});
