<?php

namespace App\Tests\Helpers;

use Slim\App;
use Slim\Http\Environment;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\RequestBody;
use Slim\Http\Response;
use Slim\Http\Uri;

/**
 * Class WebTestClient
 *
 * @package App\Tests\Helpers
 *
 * Class for performing HTTP queries to a REST API
 */
class WebTestClient
{
    /** @var \Slim\App */
    public $app;

    /** @var  \Slim\Http\Request */
    public $request;

    /** @var  \Slim\Http\Response */
    public $response;

    private $cookies = [];

    public function __construct(App $slim)
    {
        $this->app = $slim;
    }

    public function __call($method, $arguments)
    {
        throw new \BadMethodCallException(strtoupper($method) . ' is not supported');
    }

    public function get($path, $data = [], $optionalHeaders = [])
    {
        return $this->request('get', $path, $data, $optionalHeaders);
    }

    public function post($path, $data = [], $optionalHeaders = [])
    {
        return $this->request('post', $path, $data, $optionalHeaders);
    }

    // Abstract way to make a request to SlimPHP, this allows us to mock the
    // slim environment
    public function request($method, $path, $data = [], $optionalHeaders = [], $returnString=true)
    {
        //Make method uppercase
        $method = strtoupper($method);
        $options = [
            'REQUEST_METHOD' => $method,
            'REQUEST_URI' => $path
        ];

        if ($method === 'GET') {
            $options['QUERY_STRING'] = http_build_query($data);
        } else {
            $params = json_encode($data);
        }

        // Prepare a mock environment
        $env = Environment::mock(array_merge($options, $optionalHeaders));
        $uri = Uri::createFromEnvironment($env);
        $headers = Headers::createFromEnvironment($env);
        $cookies = $this->cookies;
        $serverParams = $env->all();
        $body = new RequestBody();

        // Attach JSON request
        if (isset($params)) {
            $headers->set('Content-Type', 'application/json;charset=utf8');
            $body->write($params);
        }

        $this->request = new Request($method, $uri, $headers, $cookies, $serverParams, $body);
        $response = new Response();

        $this->response = $this->app->process($this->request, $response);

        // Return the application output.
        if ($returnString) {
            return (string) $this->response->getBody();
        } else {
            return $this->response;
        }
    }

    /**
     * Set cookie $name with $value
     *
     * @param $name
     * @param $value
     */
    public function setCookie($name, $value)
    {
        $this->cookies[$name] = $value;
    }
}
