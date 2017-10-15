<?php

namespace App\Modules;

use Katzgrau\KLogger\Logger;

/**
 * @method info  (string $text)
 * @method error (string $text)
 * @method debug (string $text, array $params)
 */
class Log extends Logger
{
	/**
	 * Path to put log files
	 * DIR_ROOT / $logDir
	 * @var string
	 */
	private $logDir = 'logs';
	
	function __construct(string $logDir = '')
	{
		global $config;

		if ($logDir) {
			$path = $logDir;
		}
		else {
			$path = $config['dir']['root'] . $this->logDir;
		}

		parent::__construct($path);
	}
}