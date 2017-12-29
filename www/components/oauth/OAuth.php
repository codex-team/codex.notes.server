<?php

namespace App\Components\OAuth;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use \Firebase\JWT\JWT;

use \App\Components\Api\Models\User;

use \App\System\{
    Config,
    HTTP
};

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

        $googleCredentials = [
            'code' => $params['code'],
            'client_id' => Config::get('GOOGLE_CLIENT_ID'),
            'client_secret' => Config::get('GOOGLE_CLIENT_SECRET'),
            'redirect_uri' => $params['state'],
            'grant_type' => 'authorization_code'
        ];

        $tokenURL = 'https://www.googleapis.com/oauth2/v4/token';
        $result = HTTP::Request('POST', $tokenURL, $googleCredentials);

        $token = @json_decode($result);

        $profileURL =  'https://www.googleapis.com/userinfo/v2/me';
        $header = 'Authorization: ' . $token->token_type . ' ' . $token->access_token;

        $profileInfo = HTTP::Request('GET', $profileURL, [], [$header]);
        $profileInfo = @json_decode($profileInfo);

        if (!is_null($profileInfo->error)) {
            return $res->withStatus(500);
        }

        $userData = [
            'id' => $profileInfo->id,
            'auth_type' => 'google',
            'photo' => $profileInfo->picture,
            'name' => $profileInfo->name
        ];

        /** Save new user to database */
        $user = new User($userData['id']);
        $user->sync($userData);

        $jwt = JWT::encode([
            'iss' => Config::get('JWT_ISS'),
            'aud' => Config::get('JWT_AUD'),
            'iat' => time(),
            'id' => $userData['id'],
            'photo' => $userData['photo'],
            'name' => $userData['name'],
        ], $userData['id'] . $userData['auth_type'] . Config::get('USER_SALT'));

        $body = $res->getBody();
        $body->write('<div id="jwt">' . $jwt . '</div>');

        return $res;

    }

}