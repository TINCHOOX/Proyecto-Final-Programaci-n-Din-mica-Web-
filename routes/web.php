<?php

use App\Http\Controllers\AccountAccessibilityController;
use App\Http\Controllers\AdminLugarController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\LugarController;
use App\Http\Controllers\MapaController;
use App\Http\Controllers\RecorridoController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\TrafficReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MapaController::class, 'index'])->name('inicio');
Route::get('/transito', [MapaController::class, 'transito'])->name('transito');
Route::get('/recorrido/{lugar}', [MapaController::class, 'recorrido'])->name('recorrido.mapa');
Route::post('/recorrido/{lugar}', [RecorridoController::class, 'show'])->middleware('throttle:12,1')->name('recorrido.calcular');
Route::middleware(['auth', 'throttle:30,1'])->group(function (): void {
    Route::get('/api/presence', [PresenceController::class, 'index'])->name('presence.index');
    Route::put('/api/presence', [PresenceController::class, 'update'])->name('presence.update');
    Route::delete('/api/presence', [PresenceController::class, 'destroy'])->name('presence.destroy');
});
Route::get('/api/reports', [TrafficReportController::class, 'index'])->name('reports.index');
Route::middleware(['auth', 'throttle:30,1'])->group(function (): void {
    Route::post('/reports', [TrafficReportController::class, 'store'])->name('reports.store');
    Route::post('/reports/{report}/votes', [TrafficReportController::class, 'vote'])->name('reports.votes.store');
    Route::delete('/reports/{report}', [TrafficReportController::class, 'destroy'])->name('reports.destroy');
});
Route::get('/mis-reportes', [TrafficReportController::class, 'mine'])->middleware('auth')->name('reports.mine');
Route::get('/configuracion', [AccountAccessibilityController::class, 'showSettings'])->name('cuenta.configuracion');
Route::prefix('admin')->name('admin.')->middleware(['auth', 'can:gestionar-catalogo'])->group(function (): void {
    Route::resource('lugares', AdminLugarController::class)
        ->except('show')->parameters(['lugares' => 'lugar']);
});
Route::middleware('auth')->group(function (): void {
    Route::get('/cuenta', [AccountAccessibilityController::class, 'show'])->name('cuenta.index');
    Route::patch('/cuenta/accesibilidad', [AccountAccessibilityController::class, 'update'])->name('cuenta.accesibilidad');
    Route::get('/tienda', [ShopController::class, 'index'])->name('shop.index');
    Route::get('/coleccion', [ShopController::class, 'collection'])->name('shop.collection');
    Route::post('/tienda/{dinosaur}/canjear', [ShopController::class, 'redeem'])->middleware('throttle:30,1')->name('shop.redeem');
    Route::post('/coleccion/{dinosaur}/equipar', [ShopController::class, 'equip'])->name('shop.equip');
    Route::post('/lugares/{lugar}/resenas', [ReviewController::class, 'store'])->middleware('throttle:30,1')->name('reviews.store');
    Route::post('/resenas/{review}/votos', [ReviewController::class, 'vote'])->middleware('throttle:30,1')->name('reviews.votes.store');
});
Route::get('/mapa/puntos', [MapaController::class, 'puntos'])->name('mapa.puntos');
Route::get('/lugares', [LugarController::class, 'index'])->name('lugares.index');
Route::get('/lugares/{lugar}', [LugarController::class, 'show'])->name('lugares.show');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/login', [AuthController::class, 'storeSession'])->middleware('throttle:6,1')->name('login.store');
    Route::post('/register', [AuthController::class, 'storeUser'])->middleware('throttle:6,1')->name('register.store');
});

Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');
