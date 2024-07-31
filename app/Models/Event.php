<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title_event',
        'content_event',
        'event_date',
        'event_end_date',
        'address_event',
        'price_event',
        'photo_event',
        'publication_date',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function placeReservations()
    {
        return $this->hasMany(PlaceReservation::class, 'id_events');
    }
}