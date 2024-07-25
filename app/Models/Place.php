<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
    use HasFactory;


        protected $fillable = [
           'title',
        'description',
        'price',
        'publication_date',
        'photo',
        'address',
        'latitude',
        'longitude',
        'type',
        'user_id'
        ];
        


    protected $casts = [
        'date_de_publication' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function photos()
    {
        return $this->hasMany(Photo::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'ad_categories', 'place_id', 'category_id');
    }
}