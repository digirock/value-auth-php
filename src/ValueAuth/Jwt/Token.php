<?php


namespace ValueAuth\Jwt;


use InvalidArgumentException;
use OutOfBoundsException;
use RuntimeException;

/**
 * JSON Web Token issued by the ValueAuth API.
 * parse() does not check the signature. verify() checks RS256 only, as lcobucci/jwt 3.x did for this SDK.
 */
class Token
{
    /**
     * @var array
     */
    protected $headers;

    /**
     * @var array
     */
    protected $claims;

    /**
     * Encoded header and claims as received. This is what the signature covers.
     * @var string
     */
    protected $payload;

    /**
     * @var string
     */
    protected $signature;

    /**
     * Token constructor.
     * @param array $headers
     * @param array $claims
     * @param string $payload
     * @param string $signature
     */
    function __construct(array $headers, array $claims, string $payload, string $signature)
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
     * @throws RuntimeException when the header or the claims are not JSON
     */
    static function parse(string $jwt): Token
    {
        $parts = explode('.', $jwt);
        if (count($parts) != 3) {
            throw new InvalidArgumentException('The JWT string must have two dots');
        }

        $headers = (array)self::jsonDecode(self::base64UrlDecode($parts[0]));
        if (isset($headers['enc'])) {
            throw new InvalidArgumentException('Encryption is not supported yet');
        }
        $claims = (array)self::jsonDecode(self::base64UrlDecode($parts[1]));

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
     * @param mixed $default
     * @return mixed
     * @throws OutOfBoundsException when the claim is missing and no default is given
     */
    function getClaim(string $name, $default = null)
    {
        if ($this->hasClaim($name)) {
            return $this->claims[$name];
        }

        if ($default === null) {
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
        $details = openssl_pkey_get_details($key);
        if ($details === false || $details['type'] !== OPENSSL_KEYTYPE_RSA) {
            throw new InvalidArgumentException('This key is not compatible with this signer');
        }

        return openssl_verify($this->payload, $this->signature, $key, OPENSSL_ALGO_SHA256) === 1;
    }

    /**
     * @param string $json
     * @return mixed
     * @throws RuntimeException
     */
    protected static function jsonDecode(string $json)
    {
        $data = json_decode($json);

        if (json_last_error() != JSON_ERROR_NONE) {
            throw new RuntimeException('Error while decoding to JSON: ' . json_last_error_msg());
        }

        return $data;
    }

    /**
     * @param string $data
     * @return string
     */
    protected static function base64UrlDecode(string $data): string
    {
        if ($remainder = strlen($data) % 4) {
            $data .= str_repeat('=', 4 - $remainder);
        }

        return (string)base64_decode(strtr($data, '-_', '+/'));
    }
}
