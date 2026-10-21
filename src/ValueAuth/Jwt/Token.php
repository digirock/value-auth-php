<?php


namespace ValueAuth\Jwt;


use InvalidArgumentException;
use OutOfBoundsException;
use RuntimeException;
use stdClass;

/**
 * JSON Web Token issued by the ValueAuth API.
 * parse() does not check the signature. verify() checks RS256 only, as lcobucci/jwt 3.x did for this SDK.
 */
final class Token
{
    /**
     * @var array
     */
    private $headers;

    /**
     * @var array
     */
    private $claims;

    /**
     * Encoded header and claims as received. This is what the signature covers.
     * @var string
     */
    private $payload;

    /**
     * @var string
     */
    private $signature;

    /**
     * Use parse(). The claims and the payload must come from the same token, or verify() says nothing about the claims.
     * @param array $headers
     * @param array $claims
     * @param string $payload
     * @param string $signature
     */
    private function __construct(array $headers, array $claims, string $payload, string $signature)
    {
        $this->headers = $headers;
        $this->claims = $claims;
        $this->payload = $payload;
        $this->signature = $signature;
    }

    /**
     * @param string $jwt
     * @return Token
     * @throws InvalidArgumentException when the token does not have three parts or is encrypted
     * @throws RuntimeException when a part is not canonical base64url, or the header or the claims are not a JSON object
     */
    static function parse(string $jwt): Token
    {
        $parts = explode('.', $jwt);
        if (count($parts) != 3) {
            throw new InvalidArgumentException('The JWT string must have two dots');
        }

        $headers = self::jsonObject(self::base64UrlDecode($parts[0]), 'header');
        if (isset($headers['enc'])) {
            throw new InvalidArgumentException('Encryption is not supported yet');
        }
        $claims = self::jsonObject(self::base64UrlDecode($parts[1]), 'claims');

        return new self($headers, $claims, $parts[0] . '.' . $parts[1], self::base64UrlDecode($parts[2]));
    }

    /**
     * @param string $name
     * @return bool
     */
    function hasClaim(string $name): bool
    {
        return array_key_exists($name, $this->claims);
    }

    /**
     * @param string $name
     * @param mixed $default returned when the claim is missing, even when it is null
     * @return mixed
     * @throws OutOfBoundsException when the claim is missing and no default is given
     */
    function getClaim(string $name, $default = null)
    {
        if ($this->hasClaim($name)) {
            return $this->claims[$name];
        }

        if (func_num_args() === 1) {
            throw new OutOfBoundsException('Requested claim is not configured');
        }

        return $default;
    }

    /**
     * Checks the RS256 signature. Time claims (nbf, iat, exp) are not checked.
     *
     * @param string $publicKey PEM, or a path prefixed with file://
     * @return bool
     * @throws InvalidArgumentException when the key cannot be read or is not an RSA key
     */
    function verify(string $publicKey): bool
    {
        // An unsigned token is not verified, before the key is read: a key that cannot be read does not turn it into an exception.
        if (($this->headers['alg'] ?? null) !== 'RS256' || $this->signature === '') {
            return false;
        }

        // Drop errors left by earlier OpenSSL calls, so that the reason below is about this key.
        while (openssl_error_string() !== false) {
        }
        $key = openssl_pkey_get_public($publicKey);
        if ($key === false) {
            throw new InvalidArgumentException('It was not possible to parse your key, reason: ' . openssl_error_string());
        }

        try {
            $details = openssl_pkey_get_details($key);
            if ($details === false || $details['type'] !== OPENSSL_KEYTYPE_RSA) {
                throw new InvalidArgumentException('This key is not compatible with this signer');
            }

            return openssl_verify($this->payload, $this->signature, $key, OPENSSL_ALGO_SHA256) === 1;
        } finally {
            // PHP 7 keeps the key resource until the end of the request unless it is freed. PHP 8 frees the key object itself.
            if (PHP_VERSION_ID < 80000) {
                openssl_free_key($key);
            }
        }
    }

    /**
     * @param string $json
     * @param string $part
     * @return array the top level of the JSON object
     * @throws RuntimeException when the text is not a JSON object
     */
    private static function jsonObject(string $json, string $part): array
    {
        $data = json_decode($json);

        if (json_last_error() != JSON_ERROR_NONE) {
            throw new RuntimeException('Error while decoding to JSON: ' . json_last_error_msg());
        }

        if (!$data instanceof stdClass) {
            throw new RuntimeException('The JWT ' . $part . ' must be a JSON object');
        }

        return (array)$data;
    }

    /**
     * Only the canonical form is accepted, so that one token has one string: no padding, no characters outside base64url,
     * and no bits set after the last byte.
     *
     * @param string $data
     * @return string
     * @throws RuntimeException
     */
    private static function base64UrlDecode(string $data): string
    {
        $decoded = base64_decode(strtr($data, '-_', '+/'), true);

        if ($decoded === false || rtrim(strtr(base64_encode($decoded), '+/', '-_'), '=') !== $data) {
            throw new RuntimeException('Error while decoding from Base64Url, invalid base64 characters detected');
        }

        return $decoded;
    }
}
