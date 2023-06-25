<?php

declare(strict_types=1);

require_once "../vendor/autoload.php";

use App\Domain\ImageService;
use App\Infrastructure;

$requestUri = $_SERVER['REQUEST_URI'];

$service = new ImageService(new Infrastructure\ImagickTransformer());
try {
    ini_set('memory_limit', '1G');
    $transformationReq = $service->process($requestUri);
} catch (Throwable $t) {
    if ($t instanceof InvalidArgumentException) {
        http_response_code(404);
    } else {
        http_response_code(500);
    }
    echo $t->getMessage();
    die();
}

header(sprintf('Location: %s', $transformationReq->cacheUri), true, 301);
header(sprintf('Content-Disposition: inline; filename="%s"', $transformationReq->filename));

exit;
