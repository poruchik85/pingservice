<?php

use PHPUnit\Framework\TestCase;
use Services\Router\Request;

class RequestTest extends TestCase
{
    public function testConstructor(): void
    {
        $_SERVER["REQUEST_METHOD"] = "GET";
        $_SERVER["SERVER_NAME"] = "pingservice.local";
        $request = new Request();

        $this->assertEquals(
            [
                $request->requestMethod,
                $request->serverName,
            ], [
                "GET",
                "pingservice.local",
            ]
        );
    }

    public function camelCaseDataProvider() {
        return [
            ["REQUEST_METHOD", "requestMethod"],
            ["", ""]
        ];
    }

    /**
     * @dataProvider camelCaseDataProvider
     */
    public function testToCamelCase(string $parameter, string $expected): void
    {
        $request = new Request();
        $reflector = new ReflectionClass(Request::class);
        $method = $reflector->getMethod("toCamelCase");
        $method->setAccessible(true);

        $result = $method->invokeArgs($request, [$parameter]);

        $this->assertEquals(
            $result,
            $expected
        );
    }
}
