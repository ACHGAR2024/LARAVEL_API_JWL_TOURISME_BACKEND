<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PlaceController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\PhotoController;
use App\Http\Controllers\API\MessageController; 
use App\Http\Controllers\API\EventController; 
use App\Http\Controllers\API\PlaceReservationController; 

// Authentication routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login'])->name('login');

// Route pour récupérer l'utilisateur actuellement authentifié
Route::get('user/{user}', [UserController::class, 'indexprofil']);

// Routes des places (avec middleware auth:api pour protéger les routes nécessitant une authentification)
Route::middleware('auth:api')->group(function () {

    Route::post('places', [PlaceController::class, 'store']);
    Route::put('places/{place}', [PlaceController::class, 'update']);
    Route::delete('places/{place}', [PlaceController::class, 'destroy']);

    // Nouvelle route pour gérer les photos des places
    Route::post('places/{place}/photos', [PhotoController::class, 'store']);
    Route::put('places/{place}/photos/{photo}', [PhotoController::class, 'update']);
    Route::delete('places/{place}/photos/{photo}', [PhotoController::class, 'destroy']);
    // destroyAllPhotos : Supprimer toutes les photos d'une place
    Route::delete('places/{place}/photos', [PhotoController::class, 'destroyAllPhotos']);


    Route::post('categories', [CategoryController::class, 'store']);
    Route::put('categories/{category}', [CategoryController::class, 'update']);   
    Route::delete('categories/{category}', [CategoryController::class, 'destroy']);

    // Routes pour les messages
    Route::post('messages', [MessageController::class, 'store']);
    Route::post('messages/favorite', [MessageController::class, 'addFavorite']);
    Route::post('messages/report', [MessageController::class, 'report']);
    Route::get('messages', [MessageController::class, 'index']);
    Route::delete('messages/{message}', [MessageController::class, 'destroy']);


    // Routes pour les événements ********************
    Route::post('events', [EventController::class, 'store']);
    Route::put('events/{event}', [EventController::class, 'update']);
    Route::delete('events/{event}', [EventController::class, 'destroy']);

    // Routes pour les réservations de places*********
    Route::post('places_reservations', [PlaceReservationController::class, 'store']);
    Route::put('places_reservations/{reservation}', [PlaceReservationController::class, 'update']);
    Route::delete('places_reservations/{reservation}', [PlaceReservationController::class, 'destroy']);


    Route::get('users', [AuthController::class, 'index']);


    Route::post('logout', [AuthController::class, 'logout']);
    Route::put('update/{user}', [AuthController::class, 'update']);
    Route::delete('users/{user}', [AuthController::class, 'destroy']);
    
    // Route pour récupérer l'utilisateur actuellement authentifié
    Route::get('user', [UserController::class, 'currentUser']);

    //Fin des routes avec middleware
    
});


    
// Routes des places accessibles publiquement
Route::get('places', [PlaceController::class, 'index']);
Route::get('places/{place}', [PlaceController::class, 'show']);
Route::get('places/category/{categoryId}', [PlaceController::class, 'getPlacesByCategory']);

// Routes des photos accessibles publiquement
Route::get('places/{place}/photos', [PhotoController::class, 'index']);
Route::get('places/{place}/photos/{photo}', [PhotoController::class, 'show']);

// Routes des catégories accessibles publiquement
Route::get('categories', [CategoryController::class, 'index']);
Route::get('categories/{category}', [CategoryController::class, 'show']);


// Routes des événements accessibles publiquement*****************
Route::get('events', [EventController::class, 'index']);
Route::get('events/{event}', [EventController::class, 'show']);

// Routes des réservations accessibles publiquement***************
Route::get('places_reservations', [PlaceReservationController::class, 'index']);
Route::get('places_reservations/{reservation}', [PlaceReservationController::class, 'show']);


// Routes des catégories protégées par auth:api et admin role
Route::middleware(['auth:api', 'role:admin'])->group(function () {
    // Des routes spécifiques aux admins ici
    
    
});