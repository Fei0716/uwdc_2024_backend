<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\GameRound;
use App\Models\Player;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    //creating a new user for a game
    public function store(Game $game, Request $request){

//        check if the game is full
        if($game->players->count() >= $game->player_count){
            return response()->json(['message' => 'Game is full'], 400);
        }
        $player = new Player();
        $player->nickname = $request->nickname;
        $player->game_id = $game->id;
        $player->save();

        return response()->json($player, 201);
    }
//    get list of all players in a game
    public function index(Game $game){
        return response()->json($game->players, 200);
    }


}
