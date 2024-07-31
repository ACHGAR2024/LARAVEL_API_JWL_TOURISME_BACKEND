<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    // Méthode pour récupérer tous les événements
    public function index()
    {
        $events = Event::orderBy('created_at', 'desc')->get();
        return response()->json(['events' => $events]);
    }

    // Méthode pour créer un nouvel événement
    public function store(Request $request)
    {
        // Validation des données de la requête
        $validatedData = $request->validate([
            'title_event' => 'required|string|max:255',
            'content_event' => 'required|string',
            'event_date' => 'required|date',
            'event_end_date' => 'nullable|date',
            'address_event' => 'required|string|max:255',
            'price_event' => 'required|numeric',
            'photo_event' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'publication_date' => 'nullable|date',
            'user_id' => 'required|exists:users,id',
        ]);

        // Gestion de l'upload de l'image
        if ($request->hasFile('photo_event')) {
            $image = $request->file('photo_event');
            $name = time() . '_' . $image->getClientOriginalName();
            $filePath = $image->storeAs('images', $name, 'public');
            $validatedData['photo_event'] = '/storage/' . $filePath;
        }

        // Création de l'événement
        $event = Event::create($validatedData);

        return response()->json(['event' => $event, 'message' => 'Event created successfully']);
    }

    // Méthode pour afficher un événement spécifique
    public function show($id)
    {
        $event = Event::findOrFail($id);
        return response()->json(['event' => $event]);
    }

    // Méthode pour mettre à jour un événement
    public function update(Request $request, $id)
    {
        // Récupération de l'événement existant ou renvoie une erreur 404 si non trouvé
        $event = Event::findOrFail($id);
        $file_temp = $event->photo_event;

        // Validation des données de la requête
        $request->validate([
            'title_event' => 'required|string|max:255',
            'content_event' => 'required|string',
            'event_date' => 'required|date',
            'event_end_date' => 'nullable|date',
            'address_event' => 'required|string|max:255',
            'price_event' => 'nullable|numeric',
            'photo_event' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'publication_date' => 'nullable|date',
            'user_id' => 'required|exists:users,id',
        ]);

        // Extraction des données de la requête
        $input = $request->except('photo_event');

        // Si une nouvelle image est téléchargée, la traiter et la sauvegarder
        if ($request->hasFile('photo_event')) {
            // Générer un nom unique pour l'image
            $filenameWithExt = $request->file('photo_event')->getClientOriginalName();
            $filenameWithoutExt = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('photo_event')->getClientOriginalExtension();
            $filename = $filenameWithoutExt . '_' . time() . '.' . $extension;
            $path = $request->file('photo_event')->storeAs('images', $filename, 'public');

            // Supprimer l'ancienne image si elle existe
            if ($file_temp) {
                Storage::disk('public')->delete('images/' . basename($file_temp));
            }

            // Mettre à jour le chemin de la nouvelle image dans les données à sauvegarder
            $input['photo_event'] = '/storage/' . $path;
        }

        // Mettre à jour l'événement avec les nouvelles données
        $event->update($input);

        // Retourner une réponse JSON avec l'événement mis à jour et un message de succès
        return response()->json(['event' => $event, 'message' => 'Event updated successfully'], 200);
    }

    // Méthode pour supprimer un événement
    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        Storage::disk('public')->delete('images/' . basename($event->photo_event));
        Event::destroy($id);
        return response()->json(['message' => 'Event deleted successfully']);
    }
}