<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Place;
use App\Models\Photo;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    // Méthode pour récupérer toutes les photos d'une annonce spécifique
    public function index($place_id)
    {
        // Vérifier si l'annonce existe
        $place = Place::findOrFail($place_id);

        // Récupérer toutes les photos de l'annonce
        $photos = Photo::where('place_id', $place->id)->get();

        return response()->json(['photos' => $photos]);
    }

    // Méthode pour récupérer une photo spécifique d'une annonce
    public function show($place_id, $photo_id)
    {
        // Vérifier si l'annonce existe
        $place = Place::findOrFail($place_id);

        // Récupérer la photo spécifique de l'annonce
        $photo = Photo::where('place_id', $place->id)
                      ->where('id', $photo_id)
                      ->firstOrFail();

        return response()->json(['photo' => $photo]);
    }

    // Méthode pour ajouter une nouvelle photo à une annonce
    public function store(Request $request, $place_id)
    {
        // Valider la requête
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Vérifier si l'annonce existe
        $place = Place::findOrFail($place_id);

        // Gestion de l'upload de l'image
        $image = $request->file('photo');
        $originalName = $image->getClientOriginalName();
        $extension = $image->getClientOriginalExtension();

        // Nom du fichier avec un préfixe unique
        $fileName = time() . '_' . pathinfo($originalName, PATHINFO_FILENAME) . '.' . $extension;

        // Chemin complet du dossier pour l'annonce
        $placePhotoPath = 'photos/' . $place->id;

        // Stockage de l'image dans le dossier spécifique de l'annonce
        $image->storeAs($placePhotoPath, $fileName, 'public');

        // Chemin relatif de l'image dans le système de stockage
        $filePath = $placePhotoPath . '/' . $fileName;

        // Créer une nouvelle entrée dans la table photos
        $photo = new Photo([
            'place_id' => $place->id,
            'photo_path' => '/storage/' . $filePath,
        ]);
        $photo->save();

        return response()->json(['photo' => $photo, 'message' => 'Photo ajoutée avec succès'], 201);
    }


    // Méthode pour mettre à jour les informations d'une photo
    public function update(Request $request, $place_id, $photo_id)
{
    // Valider la requête
    $request->validate([
        'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    // Vérifier si l'annonce existe
    $place = Place::findOrFail($place_id);

    // Récupérer la photo spécifique à mettre à jour
    $photo = Photo::where('place_id', $place->id)
                  ->where('id', $photo_id)
                  ->firstOrFail();

    // Si une nouvelle image est téléchargée, la traiter et la sauvegarder
    if ($request->hasFile('photo')) {
        // Générer un nom de fichier unique
        $image = $request->file('photo');
        $originalName = $image->getClientOriginalName();
        $extension = $image->getClientOriginalExtension();
        $fileName = time() . '_' . pathinfo($originalName, PATHINFO_FILENAME) . '.' . $extension;

        // Chemin complet du dossier pour l'annonce
        $placePhotoPath = 'photos/' . $place->id;

        // Stockage de la nouvelle image dans le dossier spécifique de l'annonce
        $image->storeAs($placePhotoPath, $fileName, 'public');

        // Supprimer l'ancienne photo si elle existe
        Storage::disk('public')->delete($photo->photo_path);

        // Mettre à jour le chemin de la nouvelle photo dans la base de données
        $photo->photo_path = '/storage/' . $placePhotoPath . '/' . $fileName;
        $photo->save();
    }

    return response()->json(['photo' => $photo, 'message' => 'Photo mise à jour avec succès']);
}

    // Méthode pour supprimer une photo spécifique d'une annonce
    public function destroy($place_id, $photo_id)
    {
        // Vérifier si l'annonce existe
        $place = Place::findOrFail($place_id);
    
        // Récupérer la photo spécifique à supprimer
        $photo = Photo::where('place_id', $place->id)
                      ->where('id', $photo_id)
                      ->firstOrFail();
    
        // Supprimer la photo du système de fichiers
        Storage::disk('public')->delete($photo->photo_path);
    
        // Supprimer l'entrée de la photo de la base de données
        $photo->delete();
    
        return response()->json(['message' => 'Photo supprimée avec succès']);
    }
    
}