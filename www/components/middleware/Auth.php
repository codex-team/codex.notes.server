<?php

namespace App\Components\Middleware;

use App\Components\Api\Models\User;
use App\Components\Base\Models\Exceptions\AuthException;
use App\Components\OAuth\OAuth;
use App\Components\Sockets\Sockets;
use App\System\{
    Config,
    HTTP,
    Log
};
use Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Auth
{
    const SUPPORTED_TYPES = ['Bearer'];

    /**
     * Authenticate user by JWT
     *
     * @param Request  $req
     * @param Response $res
     * @param $next
     *
     * @return Response - with 403 status if auth failed
     */
    public function jwt(Request $req, Response $res, $next): Response
    {
        try {
            /**
             * Get Authorization header
             *
             * Authorization: "Bearer eyJ0...OSA_ISHpI"
             */
            $authHeader = $req->getHeader('Authorization');
            if (empty($authHeader[0])) {
                throw new AuthException('HTTPAuth header is missing');
            }

            /**
             * Parse Authorization header
             *
             * "Bearer eyJ0...OSA_ISHpI" --> ["Bearer", "eyJ0...OSA_ISHpI"]
             */
            $parsedAuthHeader = explode(' ', $authHeader[0]);
            if (empty($parsedAuthHeader[0]) || empty($parsedAuthHeader[1])) {
                throw new AuthException('HTTPAuth header doesn\'t match the Bearer schema');
            }

            /**
             * Check for authorization type
             */
            list($type, $token) = $parsedAuthHeader;
            if (!$this->isSupported($type)) {
                throw new AuthException('Unsupported HTTPAuth type');
            }

            /**
             * Explode JWT to parts: header, payload, signature
             */
            $jwtParts = explode('.', $token);
            if (empty($jwtParts[1])) {
                throw new AuthException('JWT payload is missing');
            }

            /**
             * Decode JWT payload and get user_id
             * It needs to generate signature key and check JWT validness
             */
            $payload = JWT::jsonDecode(JWT::urlsafeB64Decode($jwtParts[1]));
            if (empty($payload->user_id)) {
                throw new AuthException('JWT is invalid: user_id is missing');
            }

            /**
             * Generate JWT signature key
             */
            $key = OAuth::generateSignatureKey($payload->user_id);

            /**
             * Check JWT validness
             *
             * @var object $decoded
             *      string $decoded->iss
             *      string $decoded->aud
             *      string $decoded->iat
             *      string $decoded->user_id
             *      string $decoded->googleId
             *      string $decoded->email
             */
            $decoded = JWT::decode($token, $key, ['HS256']);

            /**
             * Put User's model to $GLOBALS['user']
             */
            $userId = $decoded->user_id;
            $GLOBALS['user'] = new User($userId);

            /**
             * Get Device-Id header and put it to $GLOBALS
             *
             * @used-by Sockets::push()
             */
            $deviceIdHeader = $req->getHeader('Device-Id');
            $GLOBALS['device-id'] = null;
            if (!empty($deviceIdHeader) && !empty($deviceIdHeader[0])) {
                $GLOBALS['device-id'] = $deviceIdHeader[0];
            }
        } catch (AuthException $e) {
            return $res->withStatus(HTTP::CODE_UNAUTHORIZED, $e->getMessage());
        } catch (\UnexpectedValueException $e) {
            Log::instance()->notice(sprintf("[Auth] UnexpectedValueException: %s", $e->getMessage()));

            return $res->withStatus(HTTP::CODE_UNAUTHORIZED, 'JWT is invalid');
        } catch (\DomainException $e) {
            Log::instance()->notice(sprintf("[Auth] DomainException: %s", $e->getMessage()));

            return $res->withStatus(HTTP::CODE_UNAUTHORIZED, 'JWT is invalid');
        } catch (\Exception $e) {
            Log::instance()->notice(sprintf("[Auth] Exception: %s", $e->getMessage()));

            return $res->withStatus(HTTP::CODE_UNAUTHORIZED, 'JWT is invalid');
        }

        return $next($req, $res);
    }

    /**
     * Return true if current user has passed $userId
     *
     * @param string $userId
     *
     * @return bool
     */
    public static function checkUserAccess($userId): bool
    {
        if (!Config::getBool('JWT_AUTH')) {
            return true;
        }

        if ($userId != self::userId()) {
            return false;
        }

        return true;
    }

    /**
     * Check if passed HTTPAuth type is supported
     *
     * @param string $type HTTPAuth type
     *
     * @return bool
     */
    private function isSupported($type): bool
    {
        return in_array($type, self::SUPPORTED_TYPES);
    }

    /**
     * Get User's id from GLOBALS after auth
     *
     * @return string
     */
    public static function userId(): string
    {
        return !empty($GLOBALS['user']->id) ? $GLOBALS['user']->id : '';
    }

    /**
     * Get User's model
     *
     * @return User
     */
    public static function getUser(): User
    {
        return $GLOBALS['user'];
    }
}
