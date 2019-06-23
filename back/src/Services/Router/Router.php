<?php

namespace Services\Router;

use HttpRequestException;

/**
 * @method get(string $route, string $method)
 * @method post(string $route, string $method)
 * @method delete(string $string, string $string1)
 */
class Router
{
    /**
     * @var Request $request
     */
    private $request;

    /**
     * @var array $supportedHttpMethods
     */
    private $supportedHttpMethods = array(
        "GET",
        "POST",
        "DELETE",
    );

    /**
     * @param Request $request
     */
    function __construct(Request $request) {
        $this->request = $request;
    }

    /**
     * @param string $name
     * @param array $args
     */
    function __call(string $name, array $args) {
        list($route, $method) = $args;

        if (!in_array(strtoupper($name), $this->supportedHttpMethods)) {
            $this->invalidMethodHandler();
        }

        $this->{strtolower($name)}[$this->formatRoute($route)] = $method;
    }

    private function formatRoute($route) {
        $result = rtrim($route, '/');

        if ($result === '') {
            return '/';
        }

        return $result;
    }

    private function invalidMethodHandler() {
        header(sprintf("%s 405 Method Not Allowed",  $this->request->serverProtocol));
    }

    function resolve() {
        $requestMethod = strtolower($this->request->requestMethod);
        $methodDictionary = $this->$requestMethod;
        if (!$methodDictionary) {
            echo "Wrong request method";

            die();
        }
        $formattedRoute = $this->formatRoute($this->request->requestUri);
        try {
            list($route, $method) = $this->routeSearch($formattedRoute, $methodDictionary);
        } catch (HttpRequestException $e) {
            echo $e->getMessage();

            die();
        }

        $parameters = $this->routeGetParameters($route, $formattedRoute);

        $this->callMethod($method, $this->request, $parameters);
    }

    /**
     * @param string $method
     * @param Request $request
     * @param array $parameters
     */
    private function callMethod(string $method, Request $request, array $parameters) {
        list($controllerClass, $controllerMethod) = explode("@", $method);

        $controllerInstance = new $controllerClass();

        echo $controllerInstance->$controllerMethod($request, $parameters);
    }

    /**
     * @param string $route
     * @param string $formattedRoute
     *
     * @return mixed
     */
    private function routeGetParameters(string $route, string $formattedRoute) {
        preg_match_all("/(\{\w*?\})/", $route, $matches);

        $parameters = [];

        $formattedRouteParts = explode("/", $formattedRoute);
        $routeParts = explode("/", $route);

        foreach ($routeParts as $k => $part) {
            if (in_array($part, $matches[1])) {
                if (isset($formattedRouteParts[$k])) {
                    $parameters[str_replace(["{", "}"], "", $part)] = $formattedRouteParts[$k];
                } else {
                    $parameters[str_replace(["{", "}"], "", $part)] = null;
                }
            }
        }

        return $parameters;
    }

    /**
     * @param string $route
     * @param array $methodDictionary
     *
     * @return array
     *
     * @throws HttpRequestException
     */
    private function routeSearch(string $route, array $methodDictionary) {
        foreach ($methodDictionary as $key => $method) {
            $routeReg = sprintf(
                "/^%s/",
                preg_replace("/\{\w+\}/", "\w+", str_replace("/", "\/", $key))
            );

            if (preg_match($routeReg, $route)) {
                return [$key, $method];
            }
        }

        throw new HttpRequestException("Wrong URL");
    }

    function __destruct() {
        $this->resolve();
    }
}
