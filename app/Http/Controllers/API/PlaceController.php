<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Place;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Auth;

class PlaceController extends Controller
{
    // Méthode pour récupérer toutes les annonces
    public function index()
    {
        $places = Place::orderBy('created_at', 'desc')->get();
        return response()->json(['places' => $places]);
    }

    // Méthode pour créer une nouvelle annonce
    public function store(Request $request)
    {
        // Validation des données
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'user_id' => 'required|exists:users,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'publication_date' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'type' => 'nullable|string|max:255',
        ]);

        $input = $request->except('photo');

        // Gestion de l'upload de l'image
        if ($request->hasFile('photo')) {
            $image = $request->file('photo');
            $name = time() . '_' . $image->getClientOriginalName();
            $filePath = $image->storeAs('images', $name, 'public');
            $input['photo'] = '/storage/' . $filePath;
        }

        // Création de l'annonce
        $place = Place::create($input);

        // Enregistrement de l'association avec la catégorie
        if ($request->has('category_ids')) {
            foreach ($request->category_ids as $categoryId) {
                DB::table('ad_categories')->insert([
                    'place_id' => $place->id,
                    'category_id' => $categoryId,
                ]);
            }
        }

        return response()->json(['place' => $place, 'message' => 'Place created successfully']);
    }


    // Méthode pour afficher une annonce spécifique
    public function show($id)
    {
        $place = Place::findOrFail($id);
        return response()->json(['place' => $place]);
    }

    // Méthode pour mettre à jour une annonce
    public function update(Request $request, $id)
    {
        // Validation des données
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'user_id' => 'required|exists:users,id',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'publication_date' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'type' => 'nullable|string|max:255',
        ]);

        $place = Place::findOrFail($id);
        $input = $request->except('photo');

        // Gestion de l'upload de l'image
        if ($request->hasFile('photo')) {
            $image = $request->file('photo');
            $name = time() . '_' . $image->getClientOriginalName();
            $filePath = $image->storeAs('images', $name, 'public');
            $input['photo'] = '/storage/' . $filePath;
        }

        // Mise à jour de l'annonce
        $place->update($input);

        // Suppression des associations existantes
        DB::table('ad_categories')->where('place_id', $id)->delete();

        // Enregistrement des nouvelles associations
        if ($request->has('category_ids')) {
            foreach ($request->category_ids as $categoryId) {
                DB::table('ad_categories')->insert([
                    'place_id' => $place->id,
                    'category_id' => $categoryId,
                ]);
            }
        }

        return response()->json(['place' => $place, 'message' => 'Place updated successfully']);
    }
    // Méthode pour supprimer une annonce
    public function destroy($id)
    {
        $place = Place::findOrFail($id);

        // Suppression des associations
        DB::table('ad_categories')->where('place_id', $id)->delete();

        // Suppression de l'annonce
        $place->delete();

        return response()->json(['message' => 'Place deleted successfully']);
    }

    // Méthode pour récupérer les annonces par catégorie
    public function getPlacesByCategory($categoryId)
    {
        $places = DB::table('places')
            ->join('ad_categories', 'places.id', '=', 'ad_categories.place_id')
            ->where('ad_categories.category_id', $categoryId)
            ->select('places.*')
            ->orderBy('places.created_at', 'desc')
            ->get();

        return response()->json(['places' => $places]);
    }
}