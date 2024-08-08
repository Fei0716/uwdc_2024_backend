<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\api\v1\GameSnapshotController;
use App\Models\Game;
use App\Models\GameRound;
use App\Models\GameSnapshot;
use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PlayerGameRoundController extends Controller
{
//    for updating the player's game round data at the start of each round
    public function update(Game $game, Player $player, Request $request){
        //check if there's a game round registered for the player
        $gameRound = $player->gameRound;
        $playerOrder = $player->gameRound->order;
        $gameEnded  = false;
        $finalGif = null;
        if($gameRound)
        //if there's a game round, update the order
        {
            $newRoundLeft = max($gameRound->round_left - 1, 0);
            $gameRound->round_left = $newRoundLeft;
            $gameRound->save();

            //check whether the last player has finished the last round
            Log::info('player order '.$playerOrder);
            Log::info('round left '.$newRoundLeft);
            if($playerOrder == $game->gameRounds->count() && $newRoundLeft == 0){
                $gameEnded = true;
            }
        }
        //store the data url of the current image drawn to create gif
        $gameSnapshot = new GameSnapshot();
        $gameSnapshot->data_url = $request->drawing_data;
        $gameSnapshot->game_id = $game->id;
        $gameSnapshot->save();
        //if the game ended generate a gif
        if($gameEnded){
            $gameSnapshotController = new GameSnapshotController();
            $finalGif = $gameSnapshotController->generateGif($game);
        }
        //update the game here
        $game->drawing_data = $request->drawing_data;//current drawing_data
        $game->round_countdown = $request->round_countdown-= 8;
        $game->has_ended  = $gameEnded;
        $game->final_gif = $finalGif;
        $game->save();

        //save the image
        return response()->json($gameRound, 200);
    }
    public function index(Game $game, Request $request){

        //long polling will be implemented here

        $gameRounds =$game->gameRounds;

        //check for instant response flag
        if($request->instantResponse){
            $gameRounds->map(function($gr){
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
            return response()->json($gameRounds, 200);
        }

        $originalGameRounds = clone $gameRounds;//clone another object or else will still referencing the original object
        $originalGame = $game->updated_at;
        $attempt = 0;
        //if no update
        while($attempt < 10){
            if(!$this->checkForUpdate($originalGameRounds , $gameRounds , $originalGame, $game) ){
                sleep(2);
                $gameRounds = $game->refresh()->gameRounds;
            }else{
                break;
            }
            $attempt++;

        }

        $gameRounds->map(function($gr){
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

        return response()->json($gameRounds, 200);
    }

    private function checkForUpdate($old, $new ,$old1 , $new1){
        $length = count($old);
        //check for any updated value for rows in game rounds
        for($i = 0 ; $i < $length; $i++){
            //if there's discrepancy
            if(!$old[$i]->updated_at->equalTo($new[$i]->updated_at)){
                return true;
            }
        }

        //check for any update for game
        return !$old1->equalTo($new1->updated_at);
    }
}
