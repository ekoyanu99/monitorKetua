<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Livewire\Dashboard;
use App\Livewire\History;
use App\Livewire\Monitors;

Route::get('/', Dashboard::class)->name('dashboard');
Route::get('/history', History::class)->name('history');

Route::get('/monitors', Monitors::class)->middleware(['auth'])->name('monitors');

// Route::view('/', 'welcome');
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('home');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// Route::get(
//     'send-wa',
//     function () {
//         $response = Http::withoutVerifying()->withHeaders([
//             'Authorization' => 'TOKEN',
//         ])->post('https://api.fonnte.com/send', [
//             'target' => '6285786609192',
//             'message' => 'Hello from Fonnte API, Laravel',
//         ]);

//         return $response->json();
//     }
// );

require __DIR__ . '/auth.php';
