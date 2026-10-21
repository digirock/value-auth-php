<?php

namespace ValueAuth\Tests\Unit;

use DateTime;
use PHPUnit\Framework\TestCase;
use ValueAuth\Adapter;

/**
 * Token handling of Adapter. No network access: the adapter is built but never sends a request.
 */
class AdapterTokenTest extends TestCase
{
    /**
     * @var TokenFactory
     */
    static $site;

    /**
     * @var TokenFactory
     */
    static $other;

    public static function setUpBeforeClass(): void
    {
        self::$site = new TokenFactory(4096);
        self::$other = new TokenFactory();
    }

    function adapter(?string $publicKey = null): Adapter
    {
        return new Adapter('api-key', 'auth-code', $publicKey ?? self::$site->publicKey, 'https://api.valueauth.invalid', 'v2', false);
    }

    /**
     * Claims of the 2FA auth token issued by the ValueAuth API.
     */
    function payload(array $claims = []): array
    {
        $now = time();
        return array_merge([
            'iss' => 'site-code',
            'aud' => 42,
            'iat' => $now,
            'nbf' => $now,
            'exp' => $now + 999 * 365 * 24 * 60 * 60,
        ], $claims);
    }

    function secondsAgo(int $seconds): DateTime
    {
        return (new DateTime())->setTimestamp(time() - $seconds);
    }

    function unsigned(array $header, array $payload): string
    {
        return TokenFactory::encode(json_encode($header)) . '.' . TokenFactory::encode(json_encode($payload)) . '.';
    }

    public function testExtractCustomerKeyReturnsAudienceAsIssued()
    {
        $this->assertSame(42, Adapter::extractCustomerKey(self::$site->issue($this->payload(['aud' => 42]))));
        $this->assertSame('customer-1', Adapter::extractCustomerKey(self::$site->issue($this->payload(['aud' => 'customer-1']))));
    }

    public function testExtractIssuedAt()
    {
        $token = self::$site->issue($this->payload(['iat' => 1700000000]));
        $this->assertSame(1700000000, Adapter::extractIssuedAt($token)->getTimestamp());
    }

    public function testVerifyAuthTokenAcceptsTokenSignedBySiteKeyAfterLastLogin()
    {
        $token = self::$site->issue($this->payload());
        $this->assertSame([true, 42], $this->adapter()->verifyAuthToken($token, $this->secondsAgo(60)));
    }

    public function testVerifyAuthTokenRejectsTokenIssuedBeforeOrAtLastLogin()
    {
        $issuedAt = time() - 60;
        $token = self::$site->issue($this->payload(['iat' => $issuedAt, 'nbf' => $issuedAt]));
        $this->assertSame([false, 42], $this->adapter()->verifyAuthToken($token, $this->secondsAgo(30)));
        $this->assertSame([false, 42], $this->adapter()->verifyAuthToken($token, (new DateTime())->setTimestamp($issuedAt)));
    }

    public function testVerifyAuthTokenRejectsTokenSignedByAnotherKey()
    {
        $token = self::$other->issue($this->payload());
        $this->assertSame([false, 42], $this->adapter()->verifyAuthToken($token, $this->secondsAgo(60)));
    }

    public function testVerifyAuthTokenRejectsTamperedClaims()
    {
        [$header, , $signature] = explode('.', self::$site->issue($this->payload()));
        $tampered = $header . '.' . TokenFactory::encode(json_encode($this->payload(['aud' => 7]))) . '.' . $signature;
        $this->assertSame([false, 7], $this->adapter()->verifyAuthToken($tampered, $this->secondsAgo(60)));
    }

    public function testVerifyAuthTokenRejectsHs256SignedWithThePublicKey()
    {
        $input = TokenFactory::encode(json_encode(['typ' => 'JWT', 'alg' => 'HS256'])) . '.' . TokenFactory::encode(json_encode($this->payload()));
        $token = $input . '.' . TokenFactory::encode(hash_hmac('sha256', $input, self::$site->publicKey, true));
        $this->assertSame([false, 42], $this->adapter()->verifyAuthToken($token, $this->secondsAgo(60)));
    }

    public function testVerifyAuthTokenRejectsUnsignedTokens()
    {
        $adapter = $this->adapter();
        $this->assertSame([false, 42], $adapter->verifyAuthToken($this->unsigned(['typ' => 'JWT', 'alg' => 'none'], $this->payload()), $this->secondsAgo(60)));
        $this->assertSame([false, 42], $adapter->verifyAuthToken($this->unsigned(['typ' => 'JWT', 'alg' => 'RS256'], $this->payload()), $this->secondsAgo(60)));
    }

    public function testVerifyAuthTokenRejectsSignedTokensWhoseHeaderIsNotRs256()
    {
        $adapter = $this->adapter();
        foreach (['HS256', 'RS512', 'rs256'] as $alg) {
            $token = self::$site->sign(['typ' => 'JWT', 'alg' => $alg], $this->payload());
            $this->assertSame([false, 42], $adapter->verifyAuthToken($token, $this->secondsAgo(60)), $alg);
        }
    }

    public function testVerifyAuthTokenRejectsSignedTokensWithoutAlgorithm()
    {
        $adapter = $this->adapter();
        $none = self::$site->sign(['typ' => 'JWT', 'alg' => 'none'], $this->payload());
        $absent = self::$site->sign(['typ' => 'JWT'], $this->payload());
        $this->assertSame([false, 42], $adapter->verifyAuthToken($none, $this->secondsAgo(60)));
        $this->assertSame([false, 42], $adapter->verifyAuthToken($absent, $this->secondsAgo(60)));
    }

    public function testVerifyAuthTokenUsesTheReceivedJsonAsIs()
    {
        // firebase/php-jwt 5.x escapes "/" in the JSON.
        // The signature covers the received text, so the claims must not be encoded again before verifying.
        $payload = json_encode($this->payload(['iss' => 'https://example.com/site']), JSON_PRETTY_PRINT);
        $token = self::$site->signJson(json_encode(['typ' => 'JWT', 'alg' => 'RS256']), $payload);
        $this->assertSame([true, 42], $this->adapter()->verifyAuthToken($token, $this->secondsAgo(60)));
        $this->assertSame('https://example.com/site', Adapter::parseAccessToken($token)->getClaim('iss'));
    }

    public function testVerifyAuthTokenDoesNotCheckTimeClaims()
    {
        $future = time() + 3600;
        $notYetValid = self::$site->issue($this->payload(['iat' => $future, 'nbf' => $future]));
        $this->assertSame([true, 42], $this->adapter()->verifyAuthToken($notYetValid, $this->secondsAgo(60)));

        $expired = self::$site->issue($this->payload(['exp' => time() - 3600]));
        $this->assertSame([true, 42], $this->adapter()->verifyAuthToken($expired, $this->secondsAgo(60)));
    }

    public function testPublicKeyCanBeAFilePath()
    {
        $path = tempnam(sys_get_temp_dir(), 'value-auth-php-test');
        file_put_contents($path, self::$site->publicKey);
        try {
            $token = self::$site->issue($this->payload());
            $this->assertSame([true, 42], $this->adapter('file://' . $path)->verifyAuthToken($token, $this->secondsAgo(60)));
        } finally {
            unlink($path);
        }
    }

    public function testTokenWithoutTwoDotsThrowsInvalidArgument()
    {
        $this->expectException(\InvalidArgumentException::class);
        Adapter::extractCustomerKey('not-a-token');
    }

    public function testTokenWithBrokenJsonThrowsRuntime()
    {
        $thrown = null;
        try {
            Adapter::extractCustomerKey(TokenFactory::encode('{"alg":"RS256"}') . '.' . TokenFactory::encode('not-json') . '.' . TokenFactory::encode('signature'));
        } catch (\Exception $e) {
            $thrown = $e;
        }
        $this->assertInstanceOf(\RuntimeException::class, $thrown);
        // OutOfBoundsException is a RuntimeException too. It would mean the broken JSON was read as a token without claims.
        $this->assertNotInstanceOf(\OutOfBoundsException::class, $thrown);
    }

    public function testEncryptedTokenThrowsInvalidArgument()
    {
        $this->expectException(\InvalidArgumentException::class);
        Adapter::extractCustomerKey(self::$site->sign(['typ' => 'JWT', 'alg' => 'RS256', 'enc' => 'A128GCM'], $this->payload()));
    }

    public function testTokenWithoutAudienceThrowsOutOfBounds()
    {
        $payload = $this->payload();
        unset($payload['aud']);
        $this->expectException(\OutOfBoundsException::class);
        Adapter::extractCustomerKey(self::$site->issue($payload));
    }

    public function testUnreadablePublicKeyThrowsInvalidArgument()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->adapter('not a key')->verifyAuthToken(self::$site->issue($this->payload()), $this->secondsAgo(60));
    }

    public function testNonRsaPublicKeyThrowsInvalidArgument()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->adapter(TokenFactory::ecPublicKey())->verifyAuthToken(self::$site->issue($this->payload()), $this->secondsAgo(60));
    }

    public function testParseAccessTokenExposesClaims()
    {
        $token = Adapter::parseAccessToken(self::$site->issue($this->payload(['role' => 'auth'])));
        $this->assertSame(42, $token->getClaim('aud'));
        $this->assertSame('auth', $token->getClaim('role'));
        $this->assertSame('default', $token->getClaim('missing', 'default'));
        $this->assertTrue($token->hasClaim('aud'));
        $this->assertFalse($token->hasClaim('missing'));
    }
}
