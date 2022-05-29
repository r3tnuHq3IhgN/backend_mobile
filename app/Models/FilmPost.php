<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FilmPost extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'desc',
        'image',
        'url',
    ];
} 
