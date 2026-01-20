<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CaseFileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::view('/', 'welcome');

// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

// Route::view('profile', 'profile')
//     ->middleware(['auth'])
//     ->name('profile');

// require __DIR__.'/auth.php';

Route::post('/logout', function (Request $request) {
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
})->name('logout');

// Route::get('/', fn() => view('welcome'))->name('home');
Route::view('/', 'landing.index')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->hasRole('admin'))  return redirect()->route('admin.dashboard');
        if ($user->hasRole('lawyer')) return redirect()->route('lawyer.dashboard');
        return redirect()->route('client.dashboard');
    })->name('dashboard');

    Route::prefix('admin')->name('admin.')->middleware(['auth','role:admin'])->group(function () {
        Route::get('/dashboard', \App\Livewire\Admin\Dashboard::class)->name('dashboard');
        Route::get('/users', \App\Livewire\Admin\Users\Index::class)->name('users.index');


    Route::get('/cases/approvals', \App\Livewire\Admin\Cases\Approvals::class)->name('cases.approvals');

        Route::get('/cases', \App\Livewire\Admin\Cases\Index::class)->name('cases.index');
        Route::get('/cases/{legalCase}', \App\Livewire\Admin\Cases\Show::class)->name('cases.show');
    });

    Route::prefix('lawyer')->name('lawyer.')->middleware(['auth','role:lawyer'])->group(function () {
        Route::get('/dashboard', \App\Livewire\Lawyer\Dashboard::class)->name('dashboard');

        Route::get('/cases/pool', \App\Livewire\Lawyer\Cases\Pool::class)->name('cases.pool');
        Route::get('/cases', \App\Livewire\Lawyer\Cases\Index::class)->name('cases.index');
        Route::get('/cases/{legalCase}', \App\Livewire\Lawyer\Cases\Show::class)->name('cases.show');
    });

    Route::prefix('client')->name('client.')->middleware(['auth','role:client'])->group(function () {
        Route::get('/dashboard', \App\Livewire\Client\Dashboard::class)->name('dashboard');

        Route::get('/cases', \App\Livewire\Client\Cases\Index::class)->name('cases.index');
        Route::get('/cases/create', \App\Livewire\Client\Cases\Create::class)->name('cases.create');
        Route::get('/cases/{legalCase}', \App\Livewire\Client\Cases\Show::class)->name('cases.show');
    });

    Route::get('/case-files/{caseFile}/thumb', [CaseFileController::class, 'thumb'])->name('case-files.thumb');
    Route::middleware(['auth', 'signed'])->group(function () {
        Route::get('/case-files/{caseFile}/view', [CaseFileController::class, 'view'])->name('case-files.view');
        Route::get('/case-files/{caseFile}/download', [CaseFileController::class, 'download'])->name('case-files.download');
    });
});

require __DIR__.'/auth.php';
