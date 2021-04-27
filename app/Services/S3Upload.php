<?php

namespace App\Services;

use Intervention\Image\Facades\Image as Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class S3Upload {
    public $s3;
    public $pictures;
    public $highResolutionPictures;
    public $thumbnails;

    public function __construct()
    {
        $this->s3 =  Storage::disk('s3');
    }

    public function uploadAndGenerateThumbnail($pictures) {
        $this->pictures = $pictures;
        $this->highResolutionPictures = [];
        $this->thumbnails = [];

        foreach($this->pictures as $picture) {
            $pictureName = (string) Str::uuid().".".$picture->getClientOriginalExtension();
            $s3 = Storage::disk('s3');

            $filePath = "/uploads/" . $pictureName;
            $s3->put($filePath, file_get_contents($picture), 'public');

            $thumbnailFilePath = "/uploads/thumbnail-" . $pictureName;
            $thumbnailImg = Image::make($picture)->resize(100, 100);

            $s3->put($thumbnailFilePath, $thumbnailImg->stream(), 'public');

            array_push($this->highResolutionPictures, env('AWS_URL').$filePath);
            array_push($this->thumbnails, env('AWS_URL').$thumbnailFilePath);
        }

        return json_encode(['highResolutions' => $this->highResolutionPictures, 'thumbnails' => $this->thumbnails], false);
    }
}