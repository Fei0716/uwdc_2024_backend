<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\GameRound;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


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
    public function testStore($id , Request $request){
        return response()->json(['message' => 'test store'], 200);
    }
//    get list of all players in a game
    public function index(Game $game , Request $request){
        if($request->instantResponse){
            return response()->json($game->players, 200);
        }
       //store the last amount of players in the game
       $lastPlayerCount = $game->players->count();
       $count = $lastPlayerCount;
       $attempt = 0;

       while($lastPlayerCount === $count && $attempt < 5){
            sleep(1);
            $count= $game->refresh()->players()->count();//fetch the latest list of players
            $attempt++;
        }


        return response()->json($game->players, 200);
    }


}
