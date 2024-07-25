<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlaceReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_place_tiket',
        'address_place',
        'reservation_start_date',
        'reservation_end_date',
        'id_events'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'id_events');
    }
}