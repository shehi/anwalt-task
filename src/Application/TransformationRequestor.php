<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\ImageService;

class TransformationRequestor
{
    public readonly string $filename;

    public readonly string $mime;

    public readonly string $cachePath;

    public readonly string $cacheUri;

    public function __construct(public string $filepath, public string $transformation, public int $width, public int $height)
    {
        $this->filename = basename($this->filepath);

        $this->mime = trim(shell_exec(sprintf('file -b --mime-type %s', $this->filepath)));

        $this->cachePath = self::buildCachePath($this);

        $this->cacheUri = preg_replace('#^.*?public#', '', $this->cachePath);
    }

    public static function buildCachePath(self $transformationReq): string
    {
        [, $fileExtension] = explode('/', $transformationReq->mime);
        $cacheFilename = sprintf('%s:%s,w_%d,h_%d', $transformationReq->filename, $transformationReq->transformation, $transformationReq->width, $transformationReq->height);

        return sprintf(
            '%s%s%s.%s',
            ImageService::TRANSFORMED_FOLDER,
            DIRECTORY_SEPARATOR,
            sha1($cacheFilename),
            $fileExtension,
        );
    }
}
