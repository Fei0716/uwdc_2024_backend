<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\GameRound;
use http\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GameController extends Controller
{
    //for storing the game informations
    public function store(Request $request){
        $game = new Game();
        $game->player_count = $request->player_count;
        $game->privacy = $request->privacy;
        $game->mode = $request->mode;
        $game->round_count = $request->round_count;
        $game->link = Str::random(16);
        $game->draw_item_index = random_int(0 , 9);
        $game->save();

        return response()->json($game, 201);
    }
    //for getting a game info including the drawing progress
    public function show(Game $game, Request $request){
        try{
            //check for instant response flag
            if($request->instantResponse){
                return response()->json($game, 200);
            }
            //implement long polling here
            $lastUpdatedAt = $game->updated_at;
            $attempt = 0;
            while($game->updated_at->equalTo($lastUpdatedAt) && $attempt < 5){
                sleep(1);
                $game->refresh();
                $attempt++;
            }
            return response()->json($game, 200);
        }catch (\Exception $e){
            return response($e->getMessage());
        }

    }
    public function update(Game $game, Request $request){
        $game->round_countdown = $request->round_countdown;
        $game->save();

        return response()->json($game, 200);
    }
    //for updating the drawing progress
    public function updateDrawingData(Game $game, Request $request){
        $game->drawing_data = $request->drawing_data;
        $game->save();

        return response()->json($game, 200);
    }
    public function startGame(Game $game, Request $request){
        $game->has_started = true;
        $game->round_countdown = ($game->round_count * $game->players()->count()) + 8;//if 2 rounds 2 players = 2(8) + 8[initial waiting time]
        $game->save();

        //generate the rounds details for each of the players
        $orders = range(1, $game->player_count);
        shuffle($orders);
        $i = 0;
        $game->players->each(function($player) use ($game, $orders, &$i){
            $gameRound  = new GameRound();
            $gameRound->game_id = $game->id;
            $gameRound->player_id = $player->id;
            $gameRound->round_left = $game->round_count;
            $gameRound->order = $orders[$i];
            $gameRound->save();
            $i++;
        });
        return response()->json($game, 200);
    }

    public function joinRandomGame(Request $request){
        //check for random unstarted game
        $game = Game::where([
            'has_started' => 0,
        ])->first();
        return response()->json($game ,200);
    }
}
