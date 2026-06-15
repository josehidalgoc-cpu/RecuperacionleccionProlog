<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;

// Ruta para cargar la pantalla del juego
Route::get('/', [GameController::class, 'index'])->name('game.index');

// Ruta para procesar el comando que envíe el usuario
Route::post('/query', [GameController::class, 'query'])->name('game.query');

// Ruta para ver la documentación de las reglas logicas del proyecto
Route::get('/documentacion', [GameController::class, 'documentacion'])->name('game.docs');
