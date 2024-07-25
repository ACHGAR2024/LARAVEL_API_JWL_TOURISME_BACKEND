<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Photo;
use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    public function index($placeId)
    {
        $place = Place::findOrFail($placeId);
        return response()->json($place->photos);
    }

    public function show($placeId, $photoId)
    {
        $photo = Photo::where('place_id', $placeId)->where('id', $photoId)->firstOrFail();
        return response()->json($photo);
    }

    public function store(Request $request, $placeId)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $place = Place::findOrFail($placeId);

        if ($request->hasFile('photo')) {
            $image = $request->file('photo');
            $name = time() . '_' . $image->getClientOriginalName();
            $filePath = $image->storeAs('images', $name, 'public');

            $photo = new Photo();
            $photo->place_id = $place->id;
            $photo->photo_path = '/storage/' . $filePath;
            $photo->save();

            return response()->json(['photo' => $photo, 'message' => 'Photo uploaded successfully']);
        }

        return response()->json(['message' => 'No photo uploaded'], 400);
    }

    public function update(Request $request, $placeId, $photoId)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $place = Place::findOrFail($placeId);
        $photo = Photo::where('place_id', $placeId)->where('id', $photoId)->firstOrFail();

        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne photo
            if (Storage::exists(str_replace('/storage/', '', $photo->photo_path))) {
                Storage::delete(str_replace('/storage/', '', $photo->photo_path));
            }

            $image = $request->file('photo');
            $name = time() . '_' . $image->getClientOriginalName();
            $filePath = $image->storeAs('images', $name, 'public');

            $photo->photo_path = '/storage/' . $filePath;
            $photo->save();

            return response()->json(['photo' => $photo, 'message' => 'Photo updated successfully']);
        }

        return response()->json(['message' => 'No photo uploaded'], 400);
    }

    public function destroy($placeId, $photoId)
    {
        $photo = Photo::where('place_id', $placeId)->where('id', $photoId)->firstOrFail();

        // Supprimer la photo du stockage
        if (Storage::exists(str_replace('/storage/', '', $photo->photo_path))) {
            Storage::delete(str_replace('/storage/', '', $photo->photo_path));
        }

        $photo->delete();

        return response()->json(['message' => 'Photo deleted successfully']);
    }
}