<?php

namespace App\Components\Middleware;

use App\Components\Base\Models\Exceptions\AuthException;
use App\Components\OAuth\OAuth;
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
            $authHeader = $req->getHeader('Authorization');

            if (empty($authHeader[0])) {
                throw new AuthException('HTTPAuth header is missing');
            }
            Log::instance()->notice($authHeader[0]);

            $parsedAuthHeader = explode(' ', $authHeader[0]);

            if (empty($parsedAuthHeader[0]) || empty($parsedAuthHeader[1])) {
                throw new AuthException('HTTPAuth header doesn\'t match the Bearer schema');
            }

            list($type, $token) = $parsedAuthHeader;

            if (!$this->isSupported($type)) {
                throw new AuthException('Unsupported HTTPAuth type');
            }

            $jwtParts = explode('.', $token);


            if (empty($jwtParts[1])) {
                throw new AuthException('JWT payload is missing');
            }

            $payload = JWT::jsonDecode(JWT::urlsafeB64Decode($jwtParts[1]));

            if (empty($payload->user_id)) {
                throw new AuthException('JWT is invalid: user_id is missing');
            }

            $key = OAuth::generateSignatureKey($payload->user_id);

            $decoded = JWT::decode($token, $key, ['HS256']);
            $GLOBALS['user'] = (array) $decoded;
        } catch (AuthException $e) {
            return $res->withStatus(HTTP::CODE_UNAUTHORIZED, $e->getMessage());
        } catch (\UnexpectedValueException $e) {
            Log::instance()->notice(sprintf("[Auth] %s", $e->getMessage()));

            return $res->withStatus(HTTP::CODE_UNAUTHORIZED, 'JWT is invalid');
        } catch (\DomainException $e) {
            Log::instance()->notice(sprintf("[Auth] %s", $e->getMessage()));

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
     * @param string $type - HTTPAuth type
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
        return $GLOBALS['user']['user_id'] ?: '';
    }
}
