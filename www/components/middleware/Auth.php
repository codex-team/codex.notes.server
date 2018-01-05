<?php

namespace App\Components\Middleware;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use \Firebase\JWT\JWT;
use App\Components\OAuth\OAuth;
use App\System\Log;

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
    public function jwt(Request $req, Response $res, $next)
    {

        $authHeader = $req->getHeader('Authorization');

        list($type, $token) = explode(' ', $authHeader[0]);

        if (!$this->isSupported($type)) {
            return $res->withStatus(403);
        }

        $payload = explode('.', $token)[1];
        $payload = JWT::jsonDecode(JWT::urlsafeB64Decode($payload));

        $key = OAuth::generateSignatureKey($payload->google_id);

        try {
            // TODO: pass needed values from JWT to route handler
            $decoded = JWT::decode($token, $key, ['HS256']);
        } catch (\Exception $e) {

            $logger = new Log();
            $logger->notice("Auth for {$payload->google_id} failed because of {$e->getMessage()}");

            return $res->withStatus(403);

        }

        return $next($req, $res);
    }

    /**
     * Check if passed HTTPAuth type is supported
     *
     * @param string $type - HTTPAuth type
     * @return bool
     */
    private function isSupported($type)
    {
        return in_array($type, self::SUPPORTED_TYPES);
    }
}