<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    public function gameRounds(){
        return $this->hasMany(GameRound::class, 'player_id', 'id');
    }
}
