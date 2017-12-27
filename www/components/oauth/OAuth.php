<?php

namespace App\Components\OAuth;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

class OAuth
{

    /**
     * Provide Google OAuth authorization flow
     *
     * @param Request $req
     * @param Response $res
     * @param $args
     * @return Response
     */
    public function code(Request $req, Response $res, $args)
    {

        $params = $req->getQueryParams();

        $data = [
            'code' => $params['code'],
            'client_id' => $_SERVER['GOOGLE_CLIENT_ID'],
            'client_secret' => $_SERVER['GOOGLE_CLIENT_SECRET'],
            'redirect_uri' => $params['state'],
            'grant_type' => 'authorization_code'
        ];

        $url = 'https://www.googleapis.com/oauth2/v4/token';
        $result = \App\System\HTTP::Request('POST', $url, $data);

        $token = @json_decode($result);
        $url =  'https://www.googleapis.com/userinfo/v2/me';
        $header = 'Authorization: ' . $token->token_type . ' ' . $token->access_token;

        $profileInfo = \App\System\HTTP::Request('GET', $url, [], [$header]);
        $profileInfo = @json_decode($profileInfo);

        $userData = [
            'id' => 'g' . $profileInfo->id,
            'photo' => $profileInfo->picture,
            'name' => $profileInfo->name
        ];

        $user = new \App\Components\Api\Models\User($userData['id']);

        $user->sync($userData);

        $jwt = \Firebase\JWT\JWT::encode([
            'iss' => 'CodeX Notes API Server',
            'aud' => 'CodeX Notes Application',
            'iat' => time(),
            'id' => $userData['id'],
            'photo' => $userData['photo'],
            'name' => $userData['name'],
        ], $userData['id'] . $_SERVER['USER_SALT']);

        $body = $res->getBody();
        $body->write('<div id="jwt">' . $jwt . '</div>');

        return $res;

    }

}