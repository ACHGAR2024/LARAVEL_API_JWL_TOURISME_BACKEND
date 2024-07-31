<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PlaceReservation;
use App\Models\Event;

class PlaceReservationController extends Controller
{
    // Méthode pour récupérer toutes les réservations
    public function index()
    {
        $places_reservations = PlaceReservation::orderBy('created_at', 'desc')->get();
        return response()->json(['places_reservations' => $places_reservations]);
    }

    // Méthode pour créer une nouvelle réservation
    public function store(Request $request)
    {
        // Validation des données de la requête
        $request->validate([
            'name_place_tiket' => 'nullable|string|max:255',
            'address_place' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:255',
            'reservation_start_date' => 'nullable|date',
            'reservation_end_date' => 'nullable|date',
            'id_events' => 'required|exists:events,id',
        ]);

        // Création de la réservation
        $reservation = PlaceReservation::create($request->all());

        return response()->json(['reservation' => $reservation, 'message' => 'Reservation created successfully']);
    }

    // Méthode pour afficher une réservation spécifique
    public function show($id)
    {
        $reservation = PlaceReservation::findOrFail($id);
        return response()->json(['reservation' => $reservation]);
    }

    // Méthode pour mettre à jour une réservation
    public function update(Request $request, $id)
    {
        // Récupération de la réservation existante ou renvoie une erreur 404 si non trouvée
        $reservation = PlaceReservation::findOrFail($id);

        // Validation des données de la requête
        $request->validate([
            'name_place_tiket' => 'nullable|string|max:255',
            'address_place' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:255',
            'reservation_start_date' => 'nullable|date',
            'reservation_end_date' => 'nullable|date',
            'id_events' => 'required|exists:events,id',
        ]);

        // Mise à jour de la réservation
        $reservation->update($request->all());

        // Retourner une réponse JSON avec la réservation mise à jour et un message de succès
        return response()->json(['reservation' => $reservation, 'message' => 'Reservation updated successfully']);
    }

    // Méthode pour supprimer une réservation
    public function destroy($id)
    {
        $reservation = PlaceReservation::findOrFail($id);
        $reservation->delete();
        return response()->json(['message' => 'Reservation deleted successfully']);
    }
}