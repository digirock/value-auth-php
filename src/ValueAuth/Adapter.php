<?php


namespace ValueAuth;


use Cassandra\Date;
use DateTime;
use Lcobucci\JWT\Signer\Key;
use ValueAuth\ApiInput\Get2FACodeInput;
use ValueAuth\ApiInput\PostLoginCheckInput;
use ValueAuth\ApiInput\PostLoginLogInput;
use ValueAuth\ApiResult\ApiError;
use ValueAuth\ApiResult\LoginCheckResult;
use ValueAuth\Enum\AuthenticationStatus;

class Adapter
{
    const ApiUrl = "https://api-test.homestead.test";
    /**
     * @var string
     */
    public $publicKey;

    /**
     * @var string
     */
    public $apiKey;
    /**
     * @var string
     */
    public $authCode;
    /**
     * @var ApiClient
     */
    public $client;
    /**
     * @var string
     */
    public $apiUrl = self::ApiUrl;

    /**
     * @var bool
     */
    public $debug = false;

    /**
     * @var string
     */
    public $apiVersion = "v2";


    /**
     * Adapter constructor.
     * @param string $apiKey
     * @param string $authCode
     * @param string $publicKey
     */
    function __construct(string $apiKey, string $authCode, string $publicKey)
    {
        $this->apiKey = $apiKey;
        $this->authCode = $authCode;
        $this->publicKey = $publicKey;
        $this->initializeApiClient();

    }


    /**
     * @param string $token
     * @param DateTime $lastLogin
     * @return array [bool $vefiried, string $customerKey]
     */
    function verifyAuthToken(string $token, DateTime $lastLogin)
    {
        $parser = new \Lcobucci\JWT\Parser();
        $parsed = $parser->parse($token);
        $customerKey = $parsed->getClaim('aud');
        $pubKey = new Key($this->publicKey);
        $signer = new \Lcobucci\JWT\Signer\Rsa\Sha256();
        $issuedAt = new DateTime();
        $issuedAt->setTimestamp((int)$parsed->getClaim('iat'));
        $verified = $parsed->verify($signer, $pubKey);
        $publishedAfterLogin = $lastLogin < $issuedAt;

        return [$verified && $publishedAfterLogin, $customerKey];
    }

    /**
     * @param $customerKey
     * @return LoginCheckResult | ApiError
     */
    function postCheckLogin($customerKey)
    {

        $input = new PostLoginCheckInput();
        $input->ip = $this->getClientIp();
        $input->user_agent = $_SERVER['HTTP_USER_AGENT'];
        $input->customer_key = $customerKey;
        /**
         * @var $result \ValueAuth\ApiResult\LoginCheckResult
         */
        $result = $this->client->process($input)->wait();

        return $result;
    }

    /**
     * @return string|null
     */
    function getClientIp()
    {
        $ip = null;
        foreach (
            array(
                'HTTP_CLIENT_IP',
                'HTTP_X_FORWARDED_FOR',
                'HTTP_X_FORWARDED',
                'HTTP_X_CLUSTER_CLIENT_IP',
                'HTTP_FORWARDED_FOR',
                'HTTP_FORWARDED',
                'REMOTE_ADDR'
            ) as $key
        ) {
            if (array_key_exists($key, $_SERVER)) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if ((bool)filter_var($ip, FILTER_VALIDATE_IP,
                        FILTER_FLAG_IPV4 |
                        FILTER_FLAG_NO_PRIV_RANGE |
                        FILTER_FLAG_NO_RES_RANGE)) {
                        break;
                    }
                }
            }
        }

        return $ip;
    }

    /**
     * @param string | int $customerKey
     * @param string $role
     * @return string
     */
    function fetchAccessToken($customerKey, string $role)
    {
        $input = new \ValueAuth\ApiInput\GetAccessTokenInput();
        $input->customer_key = $customerKey;
        $input->role = $role;
        /**
         * @var $result \ValueAuth\ApiResult\AccessTokenResult
         */
        $result = $this->client->process($input)->wait();

        return $result->results->access_token;
    }

    function initializeApiClient()
    {
        $this->client = new ApiClient($this - $this->apiUrl, $this->apiKey, null, null, $this->debug, $this->apiVersion, $this->authCode);
    }

    /**
     * @param string $customerKey
     * @param bool $loginWillSuccess
     * @param bool $is2FAEnabled
     * @param callable $lastLoginUpdateCallback (string $customerKey, bool $loginWillSuccess, bool $is2FAEnabled, DateTime $now)
     * @return AuthenticationStatus
     */
    function checkLoginAvailability(string $customerKey, bool $loginWillSuccess, bool $is2FAEnabled, callable $lastLoginUpdateCallback): AuthenticationStatus
    {
        $result = $this->postCheckLogin($customerKey);
        if ($result instanceof \ValueAuth\ApiResult\ApiError) {
            $error = AuthenticationStatus::ApiError();
            $error->reason = $result;
            return $error;
        }
        $loginKey = $result->results->login_key;
        /**
         * @var $input PostLoginLogInput
         */
        $input = new PostLoginLogInput();
        $input->is_logged_in = $loginWillSuccess
        $input->login_key = $loginKey;
        $input->customer_key = $customerKey;
        $result = $this->client->process($input)->wait();
        if ($result instanceof \ValueAuth\ApiResult\ApiError) {
            $error = AuthenticationStatus::ApiError();
            $error->reason = $result;
            return $error;
        }
        if (!$loginWillSuccess) {
            return AuthenticationStatus::Failed();
        } else {
            $lastLoginUpdateCallback($customerKey, $loginWillSuccess, $is2FAEnabled, new DateTime());
            if ($is2FAEnabled) {
                /**
                 * @var $input \ValueAuth\ApiInput\GetAccessTokenInput
                 */
                $input = new \ValueAuth\ApiInput\GetAccessTokenInput();
                $input->customer_key = $customerKey;
                $input->login_key = $loginKey;
                $input->role = \ValueAuth\Enum\AccessTokenRole::Auth;
                /**
                 * @var $result \ValueAuth\ApiResult\AccessTokenResult
                 */
                $result = $this->client->process($input)->wait();
                if ($result instanceof \ValueAuth\ApiResult\ApiError) {
                    $error = AuthenticationStatus::ApiError();
                    $error->reason = $result;
                    return $error;
                } else {
                    /**
                     * @var $accessTokenClient ApiClient
                     */
                    $accessToken = $result->results->access_token;
                    $accessTokenClient = new ApiClient(self::ApiUrl, null, $accessToken, null, $this->client->debug);
                    /**
                     * @var $input Get2FACodeInput
                     */
                    $input = new Get2FACodeInput();
                    $input->customer_key = $customerKey;
                    /**
                     * @var $result \ValueAuth\ApiResult\TwoFactorAuthSendResult
                     */
                    $result = $accessTokenClient->process($input)->wait();
                    if ($result instanceof \ValueAuth\ApiResult\ApiError) {
                        $error = AuthenticationStatus::ApiError();
                        $error->reason = $result;
                        return $error;
                    } else {
                        $response = AuthenticationStatus::Pending2FA();
                        $response->reason = $result;
                        return $response;
                    }
                }

            } else {
                return AuthenticationStatus::Succeeded();
            }
        }
    }
}