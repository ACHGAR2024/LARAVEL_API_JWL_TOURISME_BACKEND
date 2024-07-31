<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class CombinedSeeder extends Seeder
{
    /**
     * seeds.
     */
    public function run()
    {
        $faker = Faker::create();

        // Seed users
        $users = [];
        for ($i = 0; $i < 10; $i++) {
            $users[] = [
                'name' => $faker->name,
                'image' => $faker->imageUrl(),
                'email' => $faker->unique()->safeEmail,
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => $faker->randomElement(['agent', 'user']),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('users')->insert($users);

        
        $userIds = DB::table('users')->pluck('id');

        // Seed events
        $events = [];
        for ($i = 0; $i < 10; $i++) {
            $events[] = [
                'title_event' => $faker->sentence,
                'content_event' => $faker->paragraph,
                'event_date' => $faker->date,
                'event_end_date' => $faker->date,
                'address_event' => $faker->address,
                'price_event' => $faker->randomFloat(2, 10, 500),
                'photo_event' => $faker->imageUrl(),
                'publication_date' => $faker->date,
                'user_id' => $faker->randomElement($userIds),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('events')->insert($events);

        
        $eventIds = DB::table('events')->pluck('id');

        // Seed places
        $places = [];
        for ($i = 0; $i < 10; $i++) {
            $places[] = [
                'title' => $faker->sentence,
                'description' => $faker->paragraph,
                'price' => $faker->randomFloat(2, 10, 500),
                'publication_date' => $faker->date,
                'photo' => $faker->imageUrl(),
                'address' => $faker->address,
                'latitude' => $faker->latitude,
                'longitude' => $faker->longitude,
                'type' => $faker->word,
                'user_id' => $faker->randomElement($userIds),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('places')->insert($places);

        
        $placeIds = DB::table('places')->pluck('id');

        // Seed messages
        $messages = [];
        for ($i = 0; $i < 10; $i++) {
            $messages[] = [
                'user_id' => $faker->randomElement($userIds),
                'place_id' => $faker->randomElement($placeIds),
                'content' => $faker->paragraph,
                'status' => $faker->word,
                'is_favorite' => $faker->boolean,
                'is_report' => $faker->boolean,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('messages')->insert($messages);

        // Seed places_reservations
        $placesReservations = [];
        for ($i = 0; $i < 10; $i++) {
            $placesReservations[] = [
                'name_place_tiket' => $faker->sentence,
                'address_place' => $faker->address,
                'reservation_start_date' => $faker->date,
                'reservation_end_date' => $faker->date,
                'id_events' => $faker->randomElement($eventIds),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('places_reservations')->insert($placesReservations);

        // Seed photos
        $photos = [];
        for ($i = 0; $i < 10; $i++) {
            $photos[] = [
                'place_id' => $faker->randomElement($placeIds),
                'photo_path' => $faker->imageUrl(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('photos')->insert($photos);

        // Seed categories
        $categories = [];
        for ($i = 0; $i < 10; $i++) {
            $categories[] = [
                'name_cat' => $faker->word,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('categories')->insert($categories);

        
        $categoryIds = DB::table('categories')->pluck('id');

        // Seed ad_categories
        $adCategories = [];
        $usedCombinations = [];
        while (count($adCategories) < 10) {
            $placeId = $faker->randomElement($placeIds);
            $categoryId = $faker->randomElement($categoryIds);
            $combination = $placeId . '-' . $categoryId;

            // Assurez-vous que la combinaison est unique
            
            if (!in_array($combination, $usedCombinations)) {
                $adCategories[] = [
                    'place_id' => $placeId,
                    'category_id' => $categoryId,
                    
                ];
                $usedCombinations[] = $combination;
            }
        }
        DB::table('ad_categories')->insert($adCategories);
    }
}