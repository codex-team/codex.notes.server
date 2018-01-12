<?php

namespace App\Components\Middleware;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use \Firebase\JWT\JWT;
use App\Components\OAuth\OAuth;
use App\System\{
    Log,
    HTTP
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
    public function jwt(Request $req, Response $res, $next) : Response
    {

        $authHeader = $req->getHeader('Authorization');

        list($type, $token) = explode(' ', $authHeader[0]);

        if (!$this->isSupported($type)) {
            return $res->withStatus(HTTP::CODE_UNAUTHORIZED, 'Unsupported HTTPAuth type');
        }

        $payload = explode('.', $token)[1];
        $payload = JWT::jsonDecode(JWT::urlsafeB64Decode($payload));

        $key = OAuth::generateSignatureKey($payload->google_id);

        try {
            $decoded = JWT::decode($token, $key, ['HS256']);
            $GLOBALS['user'] = (array) $decoded;
        } catch (\Exception $e) {
            $logger = new Log();
            $logger->notice("Auth for {$payload->google_id} failed because of {$e->getMessage()}");

            return $res->withStatus(HTTP::CODE_UNAUTHORIZED, 'Invalid JWT');
        }

        return $next($req, $res);
    }

    /**
     * Return true if current user has passed $userId
     *
     * @param string $userId
     * @return bool
     */
    public static function checkUserAccess($userId) : bool
    {
        if ($userId != $GLOBALS['user']['google_id']) {
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
    private function isSupported($type) : bool
    {
        return in_array($type, self::SUPPORTED_TYPES);
    }
}