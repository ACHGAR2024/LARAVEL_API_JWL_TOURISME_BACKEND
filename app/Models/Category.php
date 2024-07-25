<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_cat',
    ];

    public function places()
    {
        return $this->belongsToMany(Place::class, 'ad_categories', 'category_id', 'place_id');
    }
}