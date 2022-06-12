<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FilmDetail extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'film_id', 'type', 'time_start', 'time_end'];
}
