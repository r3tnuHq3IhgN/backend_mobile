<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Chair;

class Room extends Model
{
    use HasFactory;

    public function chairs() {
        return $this->hasMany(Chair::class);
    }
}
