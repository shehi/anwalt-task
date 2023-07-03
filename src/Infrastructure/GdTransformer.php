<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Application\TransformationRequestor;
use GdImage;
use UnexpectedValueException;

final class GdTransformer implements TransformerInterface
{
    public function c_crop(TransformationRequestor $transformationReq): void
    {
        $file = $this->load($transformationReq);
        $file = imagecrop($file, ['x' => 1000, 'y' => 1000, 'width' => $transformationReq->width, 'height' => $transformationReq->height]);
        $this->write($file, $transformationReq);
    }

    public function c_rescale(TransformationRequestor $transformationReq): void
    {
        $file = $this->load($transformationReq);
        $file = imagescale($file, $transformationReq->width, $transformationReq->height, IMG_BICUBIC);
        $this->write($file, $transformationReq);
    }

    protected function load(TransformationRequestor $transformationReq): GdImage
    {
        $fileContent = file_get_contents($transformationReq->filepath);

        return imagecreatefromstring($fileContent);
    }

    protected function write(mixed $image, TransformationRequestor $transformationReq): void
    {
        if ($image instanceof GdImage) {
            switch ($transformationReq->mime) {
                case 'image/avif':
                    imageavif($image, $transformationReq->cachePath);
                    break;
                case 'image/jpeg':
                    imagejpeg($image, $transformationReq->cachePath);
                    break;
                case 'image/png':
                    imagepng($image, $transformationReq->cachePath);
                    break;
                case 'image/webp':
                    imagewebp($image, $transformationReq->cachePath);
                    break;
                default:
                    throw new UnexpectedValueException('Source image-type not supported in our GD implementation!');
            }
        }
    }
}
