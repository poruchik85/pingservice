<?php

namespace Services\Router;

/**
 * @property string|null requestMethod
 * @property string|null serverProtocol
 * @property string|null requestUri
 */
class Request
{
    function __construct() {
        array_walk($_SERVER, function ($value, $key) {
            $this->{$this->toCamelCase($key)} = $value;
        });
    }

    /**
     * @param $string
     *
     * @return mixed|string
     */
    private function toCamelCase($string) {
        $result = strtolower($string);

        preg_match_all('/_[a-z]/', $result, $matches);
        foreach($matches[0] as $match) {
            $c = str_replace('_', '', strtoupper($match));
            $result = str_replace($match, $c, $result);
        }

        return $result;
    }

    /**
     * @param string|null $parameter
     *
     * @return array|mixed|null
     */
    public function get(string $parameter = null) {
        if ($this->requestMethod === "GET") {
            return null;
        }
        if ($this->requestMethod === "POST") {
            if ($parameter != null) {
                return isset($_POST[$parameter]) ? filter_input(INPUT_POST, $parameter, FILTER_SANITIZE_SPECIAL_CHARS) : null;
            }

            $body = [];
            foreach($_POST as $key => $value) {
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }

            return $body;
        }

        return null;
    }
}
