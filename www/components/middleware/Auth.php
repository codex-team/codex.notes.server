<?php

namespace App\Components\Middleware;

use App\Components\Base\Models\Exceptions\AuthException;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use \Firebase\JWT\{
    JWT,
    BeforeValidException,
    ExpiredException,
    SignatureInvalidException
};

use App\Components\OAuth\OAuth;
use App\System\{
    Config, Log, Http
};

class Auth
{
    const SUPPORTED_TYPES = ['Bearer'];

    /**
     * Authenticate user by JWT
     *
     * @param Request $req
     * @param Response $res
     * @param $next
     * @return Response - with 403 status if auth failed
     */
    public function jwt(Request $req, Response $res, $next): Response
    {
        try {
            $authHeader = $req->getHeader('Authorization');

            if (empty($authHeader[0])) {
                throw new AuthException('JWT is missing');
            }

            list($type, $token) = explode(' ', $authHeader[0]);

            if (!$this->isSupported($type)) {
                throw new AuthException('Unsupported HTTPAuth type');
            }

            $jwtParts = explode('.', $token);

            if (empty($jwtParts[1])) {
                throw new AuthException('JWT is invalid');
            }

            $payload = JWT::jsonDecode(JWT::urlsafeB64Decode($jwtParts[1]));

            $key = OAuth::generateSignatureKey($payload->user_id);

            $decoded         = JWT::decode($token, $key, ['HS256']);
            $GLOBALS['user'] = (array)$decoded;

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
     * @return bool
     */
    public static function checkUserAccess($userId): bool
    {
        if (!Config::getBool('JWT_AUTH')) {
            return true;
        }

        if ($userId != $GLOBALS['user']['user_id']) {
            return false;
        }

        return true;
    }

    /**
     * Check if passed HTTPAuth type is supported
     *
     * @param string $type - HTTPAuth type
     * @return bool
     */
    private function isSupported($type): bool
    {
        return in_array($type, self::SUPPORTED_TYPES);
    }
}