<?php

declare(strict_types=1);

namespace Tests;

trait CanInvokeProtectedOrPrivateMethods
{
    /**
     * Call protected/private method of a class.
     *
     * @throws \ReflectionException
     */
    private function invokeMethod(object $object, string $methodName, array $parameters = []): mixed
    {
        $reflection = new \ReflectionClass($object::class);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
