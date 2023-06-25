<?php

namespace Tests\Domain;

use App\Application\TransformationRequestor;
use App\Domain\ImageService;
use App\Infrastructure\TransformerInterface;
use BadMethodCallException;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\Exception;
use ReflectionException;
use Tests\CanInvokeProtectedOrPrivateMethods;
use Tests\TestCase;
use UnexpectedValueException;

class ImageServiceTest extends TestCase
{
    use CanInvokeProtectedOrPrivateMethods;

    public static function data__parseAndValidateTransformationRequest(): array
    {
        return [
            ['requestUri' => '/e9b2sczowtx41.avif/c_bogus,w_10,h_10', 'exception' => BadMethodCallException::class, 'exceptionMessage' => ImageService::MSG_REQUESTED_TRANSFORMATION_NOT_SUPPORTED],
            ['requestUri' => '/bogus.avif/c_crop,w_10,h_10', 'exception' => InvalidArgumentException::class, 'exceptionMessage' => ImageService::MSG_NOT_FOUND],
            ['requestUri' => '/e9b2sczowtx41.avif/c_crop', 'exception' => UnexpectedValueException::class, 'exceptionMessage' => ImageService::MSG_REQUIRED_PARAMS],
            ['requestUri' => '/e9b2sczowtx41.avif/c_crop,w_10,h_10', 'exception' => null, 'exceptionMessage' => null],
            ['requestUri' => '/e9b2sczowtx41.avif/c_rescale,w_10,h_10', 'exception' => null, 'exceptionMessage' => null],
        ];
    }

    /**
     * @dataProvider data__parseAndValidateTransformationRequest
     *
     * @throws Exception
     * @throws ReflectionException
     */
    public function test__parseAndValidateTransformationRequest(string $requestUri, ?string $exception, ?string $exceptionMessage): void
    {
        if ($exception) {
            $this->expectException($exception);
            if ($exceptionMessage) {
                $this->expectExceptionMessage($exceptionMessage);
            }
        }

        $transformerMock = $this->createMock(TransformerInterface::class);
        $service = new ImageService($transformerMock);
        $result = $this->invokeMethod($service, 'parseAndValidateTransformationRequest', [$requestUri]);

        static::assertInstanceOf(TransformationRequestor::class, $result);
        switch (true) {
            case str_contains($requestUri, 'c_crop'):
                static::assertEquals('c_crop', $result->transformation);
                break;
            case str_contains($requestUri, 'c_rescale'):
                static::assertEquals('c_rescale', $result->transformation);
                break;
        }
        static::assertEquals(10, $result->width);
        static::assertEquals(10, $result->height);
    }

    public static function data__process(): array
    {
        return [
            [
                'requestUri' => '/e9b2sczowtx41.avif/c_crop,w_10,h_10',
                'cachePath' => sprintf(
                    '%s%s%s.avif',
                    ImageService::TRANSFORMED_FOLDER,
                    DIRECTORY_SEPARATOR,
                    sha1('e9b2sczowtx41.avif:c_crop,w_10,h_10'),
                )
            ],
            ['requestUri' => '/e9b2sczowtx41.avif/c_crop,w_10,h_10', 'cachePath' => null],
        ];
    }

    /**
     * @dataProvider data__process
     *
     * @throws Exception
     */
    public function test__process(string $requestUri, ?string $cachePath): void
    {
        $cachePath && touch($cachePath);

        $transformerMock = $this->createMock(TransformerInterface::class);
        $transformerMock->expects(static::exactly($cachePath ? 0 : 1))->method('c_crop');

        $service = new ImageService($transformerMock);
        $service->process($requestUri);

        $cachePath && unlink($cachePath);
    }
}
