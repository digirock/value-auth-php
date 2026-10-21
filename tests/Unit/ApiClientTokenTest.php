<?php

namespace ValueAuth\Tests\Unit;

use PHPUnit\Framework\TestCase;
use ValueAuth\ApiClient;

/**
 * Access token handling of ApiClient. No network access: the client is built but never sends a request.
 */
class ApiClientTokenTest extends TestCase
{
    const BaseUrl = 'https://api.valueauth.invalid';

    /**
     * @var TokenFactory
     */
    static $site;

    public static function setUpBeforeClass(): void
    {
        self::$site = new TokenFactory();
    }

    public function testAccessTokenSetsCustomerKeyAndRole()
    {
        $client = new ApiClient(null, null, self::$site->issue(['aud' => 42, 'role' => 'auth', 'iat' => time()]), false, 'v2', self::BaseUrl);
        $this->assertSame(42, $client->currentCustomerKey);
        $this->assertSame('auth', $client->currentRole->getValue());
    }

    public function testAccessTokenWithoutRoleThrowsOutOfBounds()
    {
        $this->expectException(\OutOfBoundsException::class);
        new ApiClient(null, null, self::$site->issue(['aud' => 42, 'iat' => time()]), false, 'v2', self::BaseUrl);
    }

    public function testClientWithoutAccessTokenHasNoCustomerKey()
    {
        $client = new ApiClient('api-key', 'auth-code', null, false, 'v2', self::BaseUrl);
        $this->assertNull($client->currentCustomerKey);
        $this->assertNull($client->currentRole);
    }
}
