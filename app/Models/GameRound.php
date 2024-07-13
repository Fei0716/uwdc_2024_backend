<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameRound extends Model
{
    protected $table = 'player_game_rounds';
    use HasFactory;

    public function player(){
        return $this->belongsTo(Player::class, 'player_id', 'id');
    }
    public function game(){
        return $this->belongsTo(Game::class, 'game_id', 'id');
    }
}
