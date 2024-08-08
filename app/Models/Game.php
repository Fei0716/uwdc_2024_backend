<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'player_count', 'round_count', 'link' , 'draw_item_index' ];
    public $appends = ['playersJoined'];
//    use unique link
    public function getRouteKeyName()
    {
        return 'link';
    }

    public function players(){
        return $this->hasMany(Player::class, 'game_id' , 'id');
    }
    public function gameRounds(){
        return $this->hasMany(GameRound::class, 'game_id', 'id');
    }
    public function gameSnapshots(){
        return $this->hasMany(GameSnapshot::class, 'game_id' , 'id');
    }
    public function getPlayersJoinedAttribute(){
        return $this->players->count();
    }
}
