<?php

namespace App\Tests\Helpers;


use Exception;

class MockHTTP
{
    const CODE_SERVER_ERROR = 500;

    public static function request($filename)
    {
        $responseFileName = PROJECTROOT . 'tests/oauth/' . $filename;
        if (!file_exists($responseFileName)) {
            throw new Exception('Mock JSON not found');
        }

        $handle = fopen($responseFileName, "r");
        $contents = fread($handle, filesize($responseFileName));
        fclose($handle);

        return $contents;
    }
}
