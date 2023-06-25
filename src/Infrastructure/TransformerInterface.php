<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Application\TransformationRequestor;

interface TransformerInterface
{
    public function c_crop(TransformationRequestor $transformationReq): void;

    public function c_rescale(TransformationRequestor $transformationReq): void;
}
