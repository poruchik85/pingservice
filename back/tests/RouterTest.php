<?php

use PHPUnit\Framework\TestCase;
use Services\Router\Request;
use Services\Router\Router;

class RouterTest extends TestCase
{
    /**
     * @var Router $router
     */
    private $router;

    /**
     * @var array $routesList
     */
    private $routesList;

    public function setUp(): void {
        $request = new Request();
        $this->router = new Router($request);
        $this->routesList = [
            "/group/list" => "Controllers\GroupController@list",
            "/group/{id}" => "Controllers\GroupController@get",
        ];
    }

    public function routeFormatDataProvider() {
        return [
            ["http://pingservice.local///", "http://pingservice.local"],
            ["", "/"]
        ];
    }

    public function routeSearchDataProvider() {
        return [
            ["/group/213", ["/group/{id}", "Controllers\GroupController@get"]],
            ["/group/list", ["/group/list", "Controllers\GroupController@list"]]
        ];
    }

    public function routeGetParametersDataProvider() {
        return [
            ["/group/{id}/{name}", "/group/123/g2", ["id" => 123, "name" => "g2"]],
            ["/group/{id}/{name}", "/group/123/", ["id" => 123, "name" => null]],
            ["/group/list", "/group/list", []],
            ["/group/list", "/group/list/123", []]
        ];
    }

    /**
     * @dataProvider routeFormatDataProvider
     *
     * @param string $parameter
     * @param string $expected
     *
     * @throws ReflectionException
     */
    public function testFormatRoute(string $parameter, string $expected): void {
        error_reporting(E_ERROR);

        $reflector = new ReflectionClass(Router::class);
        $method = $reflector->getMethod("formatRoute");
        $method->setAccessible(true);

        $result = $method->invokeArgs($this->router, [$parameter]);

        $this->assertEquals(
            $result,
            $expected
        );
    }

    /**
     * @dataProvider routeSearchDataProvider
     *
     * @param string $parameter
     * @param array $expected
     *
     * @throws ReflectionException
     */
    public function testRouteSearch(string $parameter, array $expected): void
    {
        error_reporting(E_ERROR);

        $reflector = new ReflectionClass(Router::class);
        $method = $reflector->getMethod("routeSearch");
        $method->setAccessible(true);

        $result = $method->invokeArgs($this->router, [$parameter, $this->routesList]);

        $this->assertEquals(
            $result,
            $expected
        );
    }

    /**
     * @dataProvider routeGetParametersDataProvider
     *
     * @param string $routeTemplate
     * @param string $route
     * @param array $expected
     *
     * @throws ReflectionException
     */
    public function testGetParameters(string $routeTemplate, string $route, array $expected): void
    {
        error_reporting(E_ERROR);

        $reflector = new ReflectionClass(Router::class);
        $method = $reflector->getMethod("routeGetParameters");
        $method->setAccessible(true);

        $result = $method->invokeArgs($this->router, [$routeTemplate, $route]);

        $this->assertEquals(
            $result,
            $expected
        );
    }
}
