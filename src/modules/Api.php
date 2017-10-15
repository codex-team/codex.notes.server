<?php

namespace App\Modules;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * @method void setVersion()
 * @method void setDefaultResponse()
 * @method json sendResponse()
 */
class Api
{
	/**
	 * Contain Slim request
	 * @var \Psr\Http\Message\ServerRequestInterface
	 */
	protected $request;

	/**
	 * Contain Slim response
	 * @var \Psr\Http\Message\ResponseInterface
	 */
	protected $response;

	/**
	 * Contain API response
	 * @var array
	 */
	protected $_response;

	/**
	 * Used in uri /v{$version}/<method>
	 * @var string
	 */
	protected $version = 'v1';
	
	/**
	 * Init API
	 * @param Request  $request  [description]
	 * @param Response $response [description]
	 */
	function __construct(Request $request, Response $response)
	{
		$this->request = $request;
		$this->response = $response;

		$this->setVersion();
		$this->setDefaultResponse();
	}

	/**
	 * Set API version
	 */
	private function setVersion()
	{
		global $logger;

		$version = (string) $this->request->getAttribute('apiVer');

		if ($version) {
			$this->version = $version;
		}
		else {
			$logger->error('API version is not setted');
		}
	}

	/**
	 * Set default output format
	 */
	private function setDefaultResponse()
	{
		$this->_response = [
			'code' => 200,
			'success' => TRUE,
			'result' => NULL
		];
	}

	/**
	 * @see \App\Modules\Api::_response
	 *  {
	 *      "code":200,
	 *	    "success":true,
	 *	    "result":{}
	 *	}
	 * @return json 
	 */
	public function sendResponse()
	{
		if (is_string($this->_response['result'])) {
        	$this->_response['success'] = FALSE;
        	$this->_response['code'] = 500;
        }

		return $this->response->withJson(
			$this->_response,
			$this->_response['code']
		);
	}
}