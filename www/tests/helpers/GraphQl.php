<?php

namespace App\Tests\Helpers;

use Exception;

/**
 * Class GraphQl
 *
 * @package App\Tests\Helpers
 */
class GraphQl
{
    public const MUTATION = 'mutation';
    public const QUERY = 'query';

    /**
     * Merge operation name, variables and request body
     *
     * @param string $type      – mutation or query
     * @param string $name      – operation name equals to the .graphql base filename
     * @param array  $variables – array with variables for request
     *
     * @throws Exception
     *
     * @return array – array with merged query, varibles and operationName
     */
    public static function request(string $type, string $name, array $variables): array
    {
        $mutationFileName = PROJECTROOT . 'tests/graphql/' . $type . '/' . $name . '.graphql';
        if (!file_exists($mutationFileName)) {
            throw new Exception('Mutation not found');
        }

        $handle = fopen($mutationFileName, "r");
        $contents = fread($handle, filesize($mutationFileName));
        fclose($handle);

        $data = [
            'query' => $contents,
            'variables' => $variables,
            'operationName' => $name
        ];

        return $data;
    }
}
