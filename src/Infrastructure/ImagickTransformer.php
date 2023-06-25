<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Application\AbstractTransformer;
use App\Application\TransformationRequestor;
use Imagick;
use ImagickException;

final class ImagickTransformer extends AbstractTransformer
{
    /**
     * @throws ImagickException
     */
    public function c_crop(TransformationRequestor $transformationReq): void
    {
        $file = $this->load($transformationReq);
        $file->cropImage($transformationReq->width, $transformationReq->height, 1000, 1000);
        $this->write($file, $transformationReq);
    }

    /**
     * @throws ImagickException
     */
    public function c_rescale(TransformationRequestor $transformationReq): void
    {
        $file = $this->load($transformationReq);
        $file->resizeImage($transformationReq->width, $transformationReq->height, Imagick::FILTER_CUBIC, 1);
        $this->write($file, $transformationReq);
    }

    /**
     * @throws ImagickException
     */
    protected function load(TransformationRequestor $transformationReq): Imagick
    {
        return new Imagick($transformationReq->filepath);
    }

    /**
     * @throws ImagickException
     */
    protected function write(mixed $image, TransformationRequestor $transformationReq): void
    {
        if ($image instanceof Imagick) {
            $image->writeImage($transformationReq->cachePath);
        }
    }
}
