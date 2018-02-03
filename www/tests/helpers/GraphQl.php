<?php

namespace App\Tests\Helpers;

use Exception;

class GraphQl
{
    public const MUTATION = 'mutation';
    public const QUERY = 'query';

    public function __construct()
    {
    }

    public static function request(string $type, string $name, array $variables): array
    {
        $mutationFileName = PROJECTROOT . 'tests/models/graphql/' . $type . '/' . $name . '.txt';
        if (!file_exists($mutationFileName)) {
            throw new Exception('Mutation not found');
        }

        $handle = fopen($mutationFileName, "r");
        $contents = fread($handle, filesize($mutationFileName));
        fclose($handle);

        $data = [
            'query' => $type . ' ' . $contents,
            'variables' => $variables,
            'operationName' => $name
        ];

        return $data;
    }
}
