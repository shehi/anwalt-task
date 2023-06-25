<?php

declare(strict_types=1);

namespace App\Application;

use App\Infrastructure\TransformerInterface;

abstract class AbstractTransformer implements TransformerInterface
{
    abstract protected function load(TransformationRequestor $transformationReq): mixed;

    abstract protected function write(mixed $image, TransformationRequestor $transformationReq): void;
}
