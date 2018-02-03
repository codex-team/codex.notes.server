<?php

namespace App\Components\OAuth;

use App\Components\Api\Models\User;
use App\Components\Sockets\Sockets;
use App\System\{
    Config,
    HTTP,
    Log
};
use Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class OAuth
{
    const GOOGLE_TOKEN_URL = 'https://www.googleapis.com/oauth2/v4/token';
    const GOOGLE_PROFILE_URL = 'https://www.googleapis.com/userinfo/v2/me';

    /**
     * Provide Google OAuth authorization flow
     *
     * @param Request  $req
     * @param Response $res
     * @param $args
     *
     * @return Response
     */
    public function code(Request $req, Response $res, $args)
    {
        $params = $req->getQueryParams();

        $googleCredentials = [
            'code' => $params['code'],
            'client_id' => Config::get('GOOGLE_CLIENT_ID'),
            'client_secret' => Config::get('GOOGLE_CLIENT_SECRET'),
            'redirect_uri' => Config::get('SERVER_URI') . 'oauth/code',
            'grant_type' => 'authorization_code'
        ];

        $result = HTTP::request('POST', self::GOOGLE_TOKEN_URL, $googleCredentials);

        $token = @json_decode($result);

        $header = 'Authorization: ' . $token->token_type . ' ' . $token->access_token;

        $profileInfo = HTTP::request('GET', self::GOOGLE_PROFILE_URL, [], [$header]);
        $profileInfo = @json_decode($profileInfo);

        Log::instance()->debug('[OAuth] Profile info from Google: ' . json_encode($profileInfo));

        if (!empty($profileInfo->error)) {
            Log::instance()->warning('[OAuth] Google OAuth failed. Reason: ' . $profileInfo->error->message);

            return $res->withStatus(HTTP::CODE_SERVER_ERROR, $profileInfo->error->message);
        }

        $userData = [
            'name' => $profileInfo->name,
            'email' => $profileInfo->email,
            'google_id' => $profileInfo->id,
            'photo' => $profileInfo->picture,
        ];

        /** Save new user to database */
        $user = new User();
        $user->sync($userData);

        Log::instance()->debug('[OAuth] User model from DB: ' . json_encode($user));

        $jwt = JWT::encode([
            'iss' => Config::get('JWT_ISS'),
            'aud' => Config::get('JWT_AUD'),
            'iat' => time(),
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'photo' => $userData['photo'],
            'google_id' => $userData['google_id'],
        ], self::generateSignatureKey($user->id));

        if (isset($params['state'])) {
            Sockets::push($params['state'], $jwt);
        }

        $body = $res->getBody();
        $body->write('<div id="jwt">' . $jwt . '</div>');

        return $res;
    }

    /**
     * Return key for JWT sign algorithm using $userId
     *
     * @param string $userId
     *
     * @return string
     */
    public static function generateSignatureKey($userId)
    {
        return $userId . Config::get('USER_SALT');
    }
}
