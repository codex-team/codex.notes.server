<?php

namespace App\Components\OAuth;

use App\Components\Api\Models\User;
use App\Components\Sockets\Sockets;
use App\System\{
    Config,
    HTTP,
    Log,
    Renderer
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

        $token = json_decode($result);

        /**
         * Check for correct payload
         */
        if (!empty($token->error)) {
            Log::instance()->warning('[OAuth] Google OAuth failed: ' . $token->error);

            return $res->withStatus(HTTP::CODE_SERVER_ERROR, 'Incorrect payload.');
        }

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
            'googleId' => $profileInfo->id,
            'photo' => $profileInfo->picture,
            'dtModify' => time(),
        ];

        /** Find user in database */
        $user = new User('', $userData['googleId']);

        /** If no user in base with this googleId then create a new one */
        if (!$user->id) {
            $user->sync($userData);
        }

        Log::instance()->debug('[OAuth] User model from DB: ' . json_encode($user));

        $jwt = JWT::encode([
            'iss' => Config::get('JWT_ISS'),
            'aud' => Config::get('JWT_AUD'),
            'iat' => time(),
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'photo' => $user->photo,
            'googleId' => $user->googleId,
            'dtModify' => $user->dtModify,
        ], self::generateSignatureKey($user->id));

        if (isset($params['state'])) {
            Sockets::push($params['state'], $jwt);
        } else {
            Log::instance()->warning('[OAuth] Can not send User\'s token because Channel\'s name was not passed. ', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);
        }

        $res->write(Renderer::render('loader.php', ['title' => 'CodeX Notes']));

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
