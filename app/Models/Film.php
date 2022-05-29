<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FilmDetail;
use App\Models\FilmPost;

class Film extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'release_day',
        'limit_age',
        'director',
        'desc',
        'duration',
        'subtitle',
        'poster',
        'trailer',
    ];

    public function details() {
        return $this->hasMany(FilmDetail::class);
    }

    public function posts() {
        return $this->hasMany(FilmPost::class);
    }
} 
