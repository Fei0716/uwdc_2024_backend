<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\GameRound;
use App\Models\Player;
use Illuminate\Http\Request;

class PlayerGameRoundController extends Controller
{
//    for updating the player's game round data at the start of each round
    public function update(Game $game, Player $player, Request $request){
        //check if there's a game round registered for the player
        $gameRound = $player->gameRounds()->where('game_id' , $game->id)->first();
        if(!$gameRound){
            //if not, create a new game round for the player
            $gameRound = new GameRound();
            $gameRound->game_id = $game->id;
            $gameRound->player_id = $player->id;
            $gameRound->round_left = $game->round_count;
            $gameRound->order = $request->order;
            $gameRound->save();
        }else{
            //if there's a game round, update the order
            $gameRound->round_left = $request->round_left;
            $gameRound->save();
        }
        return response()->json($player, 200);
    }
    public function index(Game $game){
        $gameRound =$game->gameRounds;
        $gameRound->map(function($gr){
            return [
                'id' => $gr->id,
                'game_id' => $gr->game_id,
                'player_id' => $gr->player_id,
                'round_left' => $gr->round_left,
                'order' => $gr->order,
                'player' => $gr->player,
                'game' => $gr->game,
            ];
        });

        return response()->json($gameRound, 200);
    }
}
