<?php

namespace App\Tests;

use App\Tests\Helpers\WebTestCase;
use App\Tests\Helpers\WebTestClient;

define('PROJECTROOT', dirname(__FILE__, 2) . DIRECTORY_SEPARATOR);

/**
 * Enable autoloaders
 */
include_once PROJECTROOT . "public/autoload.php";
include_once PROJECTROOT . "vendor/autoload.php";

