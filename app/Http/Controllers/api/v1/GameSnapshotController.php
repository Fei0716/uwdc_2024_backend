<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver; // Use GD driver if Imagick is not available
use Intervention\Image\Decoders\Base64ImageDecoder;
use Intervention\Image\Decoders\DataUriImageDecoder;
use Intervention\Gif\Builder;
use App\Models\Game;

class GameSnapshotController extends Controller
{
    public function generateGif(Game $game)
    {
        // Create new manager instance with GD driver
        $manager = new ImageManager(new Driver());
        $gif = Builder::canvas(800, 500 , [255, 255, 255]); // Adjust width and height as needed
        $delay = 0.25; // Delay in seconds

        // Directory to store temporary image files
        $tempDir = storage_path('app/public/temp_frames');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true); // Create directory if it doesn't exist
        }

        // Read each snapshot and add to GIF
        foreach ($game->gameSnapshots as $s) {
            //$image = $this->convertToInterventionImage($s->data_url);

            // Save the Intervention Image instance to a temporary file
            $tempPath = $tempDir . '/frame' . uniqid() . '.gif';
            //$image->save($tempPath);
            $tempImage = imagecreatefrompng($s->data_url);
            imagesavealpha($tempImage, true);
            imagecolortransparent($tempImage, 127<<24);
            imagegif($tempImage, $tempPath);
            // Add frame to GIF using the file path
            $gif->addFrame($tempPath, $delay);
        }

        // Set loop count; 0 for infinite looping
        $gif->setLoops(0);

        // Encode the GIF
        $data = $gif->encode();

        // Save GIF to storage
        $gifName = $game->link . ".gif";
        Storage::put('public/gifs/' . $gifName, $data);

        // Clean up temporary files
        //$this->cleanupTempFiles($tempDir);

        // Return the path to the saved GIF
        return 'gifs/' . $gifName;
    }

    private function convertToInterventionImage($dataUrl)
    {
        // Create new manager instance with GD driver
        $manager = new ImageManager(new Driver());

        // Read the image from base64 or data URI
        return $manager->read($dataUrl, [
            DataUriImageDecoder::class,
            Base64ImageDecoder::class,
        ]);
    }

    private function cleanupTempFiles($dir)
    {
        foreach (glob($dir . '/*') as $file) {
            unlink($file); // Delete file
        }
        rmdir($dir); // Delete directory
    }

    public function getFinalGif(Game $game){
        return response()->json($game->final_gif, 200);
    }
}
