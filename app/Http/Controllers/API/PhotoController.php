<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Photo;
use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;


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
            'photos.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);
    
        $place = Place::findOrFail($placeId);
    
        $photos = $request->file('photos');
        $uploadedPhotos = [];
    
        foreach ($photos as $photo) {
            $name = time() . '_' . $photo->getClientOriginalName();
            $filePath = $photo->storeAs('images', $name, 'public');
    
            $newPhoto = new Photo();
            $newPhoto->place_id = $place->id;
            $newPhoto->photo_path = '/storage/' . $filePath;
            $newPhoto->save();
    
            $uploadedPhotos[] = $newPhoto;
        }
    
        return response()->json(['photos' => $uploadedPhotos, 'message' => 'Photos uploaded successfully']);
    }
    
    
    /*public function store(Request $request, $placeId)
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
    }*/

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

    // PhotoController.php
// PhotoController.php
public function destroyAllPhotos($placeId)
{
    try {
        // Retrieve photos to delete their files from storage later
        $photos = DB::table('photos')->where('place_id', $placeId)->get();

        if ($photos->isEmpty()) {
            return response()->json(['message' => 'No photos found for this place'], 404);
        }

        // Delete photos from the database
        DB::table('photos')->where('place_id', $placeId)->delete();

        // Delete photos from storage
        foreach ($photos as $photo) {
            $filePath = str_replace('/storage/', '', $photo->photo_path);
            if (Storage::exists($filePath)) {
                Storage::delete($filePath);
            }
        }

        return response()->json(['message' => 'All photos deleted successfully']);
    } catch (\Exception $e) {
        // Log the error for debugging
        Log::error('Error deleting photos for place: ' . $e->getMessage());
        return response()->json(['message' => 'Error deleting photos', 'error' => $e->getMessage()], 500);
    }
}





}