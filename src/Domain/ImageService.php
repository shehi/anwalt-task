<?php

declare(strict_types=1);

namespace App\Domain;

use App\Application\TransformationRequestor;
use App\Infrastructure\TransformerInterface;
use BadMethodCallException;
use InvalidArgumentException;
use UnexpectedValueException;

class ImageService
{
    public const MSG_REQUESTED_TRANSFORMATION_NOT_SUPPORTED = 'Requested transformation not supported!';
    public const MSG_REQUIRED_PARAMS = 'Both "width" and "height" parameters are required!';
    public const MSG_NOT_FOUND = 'Resource not found!';
    public const ORIGINALS_FOLDER = '/var/www/originals';
    public const TRANSFORMED_FOLDER = '/var/www/public/cache';

    public function __construct(private readonly TransformerInterface $transformer)
    {
    }

    public function process(string $requestUri): TransformationRequestor
    {
        $transformationReq = $this->parseAndValidateTransformationRequest($requestUri);
        if (!file_exists($transformationReq->cachePath) || !is_file($transformationReq->cachePath)) {
            $this->transformer->{$transformationReq->transformation}($transformationReq);
        }

        return $transformationReq;
    }

    private function parseAndValidateTransformationRequest(string $requestUri): TransformationRequestor
    {
        $segments = explode('/', $requestUri);
        $filename = $segments[1];
        $filepath = sprintf('%s%s%s', self::ORIGINALS_FOLDER, DIRECTORY_SEPARATOR, $filename);
        if (!file_exists($filepath) || !is_file($filepath)) {
            throw new InvalidArgumentException(self::MSG_NOT_FOUND);
        }

        $transformationParams = $segments[2];
        if (
            !preg_match('#(?P<method>(c_[^,]+))#', $transformationParams, $matchesT)
            || !method_exists(TransformerInterface::class, $transformation = $matchesT['method'])
        ) {
            throw new BadMethodCallException(self::MSG_REQUESTED_TRANSFORMATION_NOT_SUPPORTED);
        }

        if (!preg_match('/w_(?<width>\d+)/', $transformationParams, $matchesW) || !preg_match('/h_(?<height>\d+)/', $transformationParams, $matchesH)) {
            throw new UnexpectedValueException(self::MSG_REQUIRED_PARAMS);
        }
        $width = $matchesW['width'];
        $height = $matchesH['height'];

        return new TransformationRequestor($filepath, $transformation, (int)$width, (int)$height);
    }
}
