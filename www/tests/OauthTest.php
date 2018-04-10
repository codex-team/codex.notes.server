<?php

namespace App\Tests;

use App\Components\Api\Models\User;
use App\Components\Base\Models\Mongo;
use App\Components\OAuth\OAuth;
use App\System\HTTP;
use App\Tests\Helpers\MockHTTP;
use App\Tests\Helpers\WebTestCase;
use Firebase\JWT\JWT;
use Mockery;

/**
 * Class ApiUserTest
 *
 * @package App\Tests
 *
 * Test GraphQl user API endpoint
 */
class OauthTest extends WebTestCase
{

    /**
     * Setup testing environment
     */
    public function setup()
    {
        parent::setup();
        $this->returnUri = "http://localhost:8081/oauth/code";
        $this->code = "googleCode";

        $dotenv = new \Dotenv\Dotenv(PROJECTROOT);
        $dotenv->load();

        Mockery::mock('overload:\Hawk\HawkCatcher')->shouldReceive('catchException');
        Mockery::namedMock('App\System\HTTP', 'App\Tests\Helpers\MockHTTP')
            ->shouldReceive('request')->andReturn(
                MockHTTP::request("invalid_code.json"),
                MockHTTP::request("invalid_token.json"),
                MockHTTP::request("valid_code.json"),
                MockHTTP::request("profile_info.json")
            );

        $this->dropCollection();
        $this->initDb();
    }

    /**
     * Initialize database with test user
     */
    private function initDb()
    {
        $this->testUser = new User();
        $this->testUser->sync([
            'name' => 'JohnDoe',
            'email' => 'JohnDoe@ifmo.su',
            'dtReg' => 1517651704,
            'dtModify' => 1777651705,
            'photo' => 'userphoto',
            'googleId' => '114100000001297733282'
        ]);
    }

    private function verifyAndDecodeJwt($jwt, $user_id)
    {
        self::assertArrayHasKey('jwt', $jwt);
        self::assertArrayHasKey('photo', $jwt);
        self::assertArrayHasKey('dtModify', $jwt);
        self::assertArrayHasKey('channel', $jwt);
        self::assertArrayHasKey('name', $jwt);

        $decodedJwt = JWT::decode($jwt['jwt'], OAuth::generateSignatureKey($user_id), ['HS256']);
        $decodedJwt = json_decode(json_encode($decodedJwt), true);

        self::assertArrayHasKey('iss', $decodedJwt);
        self::assertArrayHasKey('aud', $decodedJwt);
        self::assertArrayHasKey('iat', $decodedJwt);
        self::assertArrayHasKey('user_id', $decodedJwt);
        self::assertArrayHasKey('googleId', $decodedJwt);
        self::assertArrayHasKey('email', $decodedJwt);

        return $decodedJwt;
    }

    private function generateJwtWithUserData($profile_info)
    {
        $obj = new OAuth();
        $reflection = new \ReflectionClass(get_class($obj));
        $method = $reflection->getMethod('generateJwtWithUserData');
        $method->setAccessible(true);
        return $method->invokeArgs($obj, [$profile_info]);
    }

    /**
     * Drop user collection from test database
     */
    private function dropCollection()
    {
        Mongo::connect()
            ->{User::getCollectionName()}
            ->drop();
    }

    public function test_oauth()
    {
        $errorCreds = (new OAuth)->prepareGoogleCredentials([]);
        self::assertEquals($errorCreds, false, 'Empty code argument');
        $errorCodeResponse = $this->client->get('/oauth/code', ['c2ode' => '11']);
        self::assertEquals($errorCodeResponse, "", 'Empty code argument');

        $validCreds = (new OAuth)->prepareGoogleCredentials(['code' => $this->code]);
        self::assertArrayHasKey("code", $validCreds);
        self::assertArrayHasKey("client_id", $validCreds);
        self::assertArrayHasKey("client_secret", $validCreds);
        self::assertArrayHasKey("redirect_uri", $validCreds);
        self::assertArrayHasKey("grant_type", $validCreds);

        self::assertEquals($validCreds["code"], $this->code, "code argument");
        self::assertEquals($validCreds["client_id"], "GOOGLE_CLIENT_ID", "client_id argument");
        self::assertEquals($validCreds["client_secret"], "GOOGLE_CLIENT_SECRET", "client_secret argument");
        self::assertEquals($validCreds["redirect_uri"], $this->returnUri, "redirect_uri argument");
        self::assertEquals($validCreds["grant_type"], "authorization_code", "grant_type argument");


    }

    public function test_oauth_response()
    {
        $invalidResponse = $this->client->request('get', '/oauth/code', ['code' => $this->code], [], false);
        self::assertEquals($invalidResponse->getStatusCode(), 500);
        self::assertEquals($invalidResponse->getReasonPhrase(), "Incorrect payload.");

        $invalidResponse = $this->client->request('get', '/oauth/code', ['code' => $this->code], [], false);
        self::assertEquals($invalidResponse->getStatusCode(), 500);
        self::assertEquals($invalidResponse->getReasonPhrase(), "Incorrect token.");

        $validResponse = $this->client->request('get', '/oauth/code', ['code' => $this->code], [], false);
        self::assertEquals($validResponse->getStatusCode(), 200);

    }

    public function test_jwt()
    {
        $jwt = $this->generateJwtWithUserData(json_decode(MockHTTP::request("profile_info.json")));

        $decodedJwt = $this->verifyAndDecodeJwt($jwt, $this->testUser->id);

        self::assertEquals($jwt['photo'], "userphoto");
        self::assertEquals($jwt['dtModify'], "1777651705");
        self::assertEquals($jwt['channel'], $this->testUser->getSocketChannelName());
        self::assertEquals($jwt['name'], $this->testUser->name);

        self::assertEquals($decodedJwt['iss'], getenv('JWT_ISS'));
        self::assertEquals($decodedJwt['aud'], getenv('JWT_AUD'));
        self::assertEquals($decodedJwt['user_id'], $this->testUser->id);
        self::assertEquals($decodedJwt['googleId'], $this->testUser->googleId);
        self::assertEquals($decodedJwt['email'], $this->testUser->email);
    }

    public function test_jwt_new_user()
    {
        $profile_info = json_decode(MockHTTP::request("profile_info.json"));
        $profile_info->id = '1111';
        $jwt = $this->generateJwtWithUserData($profile_info);

        $user = new User('', $profile_info->id);

        self::assertNotEquals($user->id, $this->testUser->id);

        $decodedJwt = $this->verifyAndDecodeJwt($jwt, $user->id);

        self::assertEquals($jwt['photo'], $profile_info->picture);
        self::assertEquals($jwt['channel'], $user->getSocketChannelName());
        self::assertEquals($jwt['name'], $user->name);

        self::assertEquals($decodedJwt['iss'], getenv('JWT_ISS'));
        self::assertEquals($decodedJwt['aud'], getenv('JWT_AUD'));
        self::assertEquals($decodedJwt['user_id'], $user->id);
        self::assertEquals($decodedJwt['googleId'], $user->googleId);
        self::assertEquals($decodedJwt['email'], $user->email);
    }

    public function test_generate_key()
    {
        $key = OAuth::generateSignatureKey($this->testUser->id);
        self::assertEquals($key, $this->testUser->id . getenv('USER_SALT'));
    }
}
