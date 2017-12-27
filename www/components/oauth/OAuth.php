<?php

namespace App\Components\OAuth;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class OAuth
{

    public function code(Request $req, Response $res, $args)
    {

        $params = $req->getQueryParams();

        $data = [
            'code' => $params['code'],
            'client_id' => $_SERVER['GOOGLE_CLIENT_ID'],
            'client_secret' => $_SERVER['GOOGLE_CLIENT_SECRET'],
            'redirect_uri' => 'http://localhost:8081/oauth/code',
            'grant_type' => 'authorization_code'
        ];

        $url = 'https://www.googleapis.com/oauth2/v4/token';

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        curl_close($curl);

        $token = @json_decode($result, true);
        $curl = curl_init();


        $url =  'https://www.googleapis.com/userinfo/v2/me';
        $header = 'Authorization: ' . $token['token_type'] . ' ' . $token['access_token'];
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [$header]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $profileinfo = curl_exec($curl);
        curl_close($curl);


        echo '<div id="info">' . $profileinfo . '</div>';

        return $res;

    }

}