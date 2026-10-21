<?php

namespace ValueAuth\Tests\Unit;

/**
 * Creates key pairs and RS256 tokens for the unit tests.
 * issue() builds the token the same way JWT::encode of firebase/php-jwt 6 and later does with RS256.
 * firebase/php-jwt 5.x escapes "/" in the JSON; AdapterTokenTest builds that shape separately.
 */
class TokenFactory
{
    /**
     * @var string
     */
    public $privateKey;

    /**
     * @var string
     */
    public $publicKey;

    /**
     * @param int $bits
     */
    function __construct(int $bits = 2048)
    {
        $key = openssl_pkey_new([
            'digest_alg' => 'sha512',
            'private_key_bits' => $bits,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);
        openssl_pkey_export($key, $privateKey);
        $this->privateKey = $privateKey;
        $this->publicKey = openssl_pkey_get_details($key)['key'];
    }

    /**
     * @param array $payload
     * @return string
     */
    function issue(array $payload): string
    {
        return $this->sign(['typ' => 'JWT', 'alg' => 'RS256'], $payload);
    }

    /**
     * Signs with RS256 whatever the header says.
     * @param array $header
     * @param array $payload
     * @return string
     */
    function sign(array $header, array $payload): string
    {
        return $this->signJson(json_encode($header, JSON_UNESCAPED_SLASHES), json_encode($payload, JSON_UNESCAPED_SLASHES));
    }

    /**
     * @param string $header
     * @param string $payload
     * @return string
     */
    function signJson(string $header, string $payload): string
    {
        $input = self::encode($header) . '.' . self::encode($payload);
        openssl_sign($input, $signature, $this->privateKey, OPENSSL_ALGO_SHA256);
        return $input . '.' . self::encode($signature);
    }

    /**
     * @param string $data
     * @return string
     */
    static function encode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * @return string
     */
    static function ecPublicKey(): string
    {
        $key = openssl_pkey_new([
            'private_key_type' => OPENSSL_KEYTYPE_EC,
            'curve_name' => 'prime256v1',
        ]);
        return openssl_pkey_get_details($key)['key'];
    }
}
