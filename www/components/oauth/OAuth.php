<?php

namespace App\Components\OAuth;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use \Firebase\JWT\JWT;

use \App\Components\Api\Models\User;

use \App\System\{
    Config,
    Http,
    Log
};

class OAuth
{
    const GOOGLE_TOKEN_URL = 'https://www.googleapis.com/oauth2/v4/token';
    const GOOGLE_PROFILE_URL = 'https://www.googleapis.com/userinfo/v2/me';

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

        $result = HTTP::request('POST', self::GOOGLE_TOKEN_URL, $googleCredentials);

        $token = @json_decode($result);

        $header = 'Authorization: ' . $token->token_type . ' ' . $token->access_token;

        $profileInfo = HTTP::request('GET', self::GOOGLE_PROFILE_URL, [], [$header]);
        $profileInfo = @json_decode($profileInfo);

        if (!is_null($profileInfo->error)) {
            Log::instance()->warning('Google OAuth failed. Reason: ' . $profileInfo->error->reason);
            return $res->withStatus(HTTP::CODE_SERVER_ERROR, $profileInfo->error->reason);
        }

        $userData = [
            'google_id' => $profileInfo->id,
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
            'google_id' => $userData['google_id'],
            'photo' => $userData['photo'],
            'name' => $userData['name'],
            'user_id' => $user->id,
        ], self::generateSignatureKey($user->id));

        $body = $res->getBody();
        $body->write('<div id="jwt">' . $jwt . '</div>');

        return $res;
    }

    /**
     * Return key for JWT sign algorithm using $userId
     *
     * @param string $userId
     * @return string
     */
    public static function generateSignatureKey($userId)
    {
        return $userId . Config::get('USER_SALT');
    }
}