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

        $jwt = self::generateJwtWithUserData($profileInfo);

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
     * Google OAuth for mobile Apps
     * Return a new jwt access token for Cloud
     *
     * @param Request  $req
     * @param Response $res
     * @param $args
     *
     * @return Response
     */
    public function mobile(Request $req, Response $res, $args)
    {
        try {
            $params    = $req->getQueryParams();
            $CLIENT_ID = Config::get('GOOGLE_CLIENT_ID');

            /** Check for existing "token" param */
            if (empty($params['token'])) {
                Log::instance()
                   ->warning('[OAuth] Mobile Google OAuth failed. No token was provided');

                return $res->withStatus(HTTP::CODE_SERVER_ERROR,
                    'No token param was provided');
            }

            $token = $params['token'];

            $client = new \Google_Client(['client_id' => $CLIENT_ID]);

            /**
             * object|bool $payload - data from jwt or false
             *
             * $payload["azp"] string - the client ID of the Android app component of project
             * $payload["aud"] string - the client ID of the web component of the project
             * $payload["sub"] string - An identifier for the user, unique among all Google accounts and never reused.
             * $payload["email"] string - The user's email address.
             * $payload["email_verified"] bool - True if the user's e-mail address has been verified; otherwise false.
             * $payload["exp"] int - The time the ID token expires, represented in Unix time (integer seconds).
             * $payload["iss"] string - The Issuer Identifier for the Issuer of the response. Always accounts.google.com
             * $payload["iat"] int - The time the ID token was issued, represented in Unix time (integer seconds).
             * $payload["name"] string - The user's full name, in a displayable form.
             * $payload["picture"] string - The URL of the user's profile picture.
             * $payload["given_name"] string - User's first name
             * $payload["family_name"] string - User's second name
             * $payload["locale"] string — "ru"
             */
            $payload = $client->verifyIdToken($token);

            if ($payload) {
                /** set user id as param */
                $payload['id'] = $payload['sub'];

                /** Convert array to object */
                $payload = (object)$payload;

                /** Get JWT */
                $jwt = self::generateJwtWithUserData($payload);

                Log::instance()->debug('[OAuth] Mobile Google OAuth OK');

                /** Print new jwt access token */
                return $res->write($jwt);
            } else {
                throw new \Exception('Bad token was passed');
            }

        } catch (\Exception $e) {
            Log::instance()
               ->warning('[OAuth] Mobile Google OAuth failed. ' . $e->getMessage());

            /** Return 500 with message */
            return $res->withStatus(HTTP::CODE_SERVER_ERROR,
                 $e->getMessage());
        }
    }

    /**
     * Find (or create) user in DB and generate JWT
     *
     * @param $profileInfo object - user data: name, email, id, picture
     *
     * @return string
     *
     * @throws \Exception
     */
    private static function generateJwtWithUserData($profileInfo)
    {
        try {
            $userData = [
                'name'     => $profileInfo->name,
                'email'    => $profileInfo->email,
                'googleId' => $profileInfo->id,
                'photo'    => $profileInfo->picture,
                'dtModify' => time(),
            ];

            /** Find user in database */
            $user = new User('', $userData['googleId']);

            /** If no user in base with this googleId then create a new one */
            if ( ! $user->id) {
                $user->sync($userData);
            }

            $jwt = JWT::encode([
                'iss'      => Config::get('JWT_ISS'),
                'aud'      => Config::get('JWT_AUD'),
                'iat'      => time(),
                'user_id'  => $user->id,
                'name'     => $user->name,
                'email'    => $user->email,
                'photo'    => $user->photo,
                'googleId' => $user->googleId,
                'dtModify' => $user->dtModify,
            ], self::generateSignatureKey($user->id));

            return $jwt;
        } catch (\Exception $e) {
            Log::instance()->warning('[OAuth] Generating JWT was failed because of ' . $e->getMessage());

            throw new \Exception('Cannot generate JWT');
        }
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
