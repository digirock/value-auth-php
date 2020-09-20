<?php


namespace ValueAuth\ApiEndpoint;


use phpDocumentor\Reflection\Type;
use ValueAuth\Enum\ApiAuthentication;

class ApiEndpoint
{
    /**
     * @var string|null
     */
    public static $method;

    /**
     * @var string|null
     */
    public static $path;

    /**
     * @var string[]
     */
    public static $pathParams = [];

    /**
     * @var string[]
     */
    public static $queryParams = [];

    /**
     * @var string[]
     */
    public static $bodyParams = [];

    /**
     * @var Type
     */
    public static $resultType;

    /**
     * @var string
     */
    public static $authentication = ApiAuthentication::AccessToken;

    public function method(): string
    {
        return self::$method;
    }

    public function path(): string
    {
        return self::$path;
    }

    public function pathParams(): array
    {
        return self::$pathParams;

    }

    public function queryParams(): array
    {
        return self::$queryParams;
    }

    public function bodyParams(): array
    {
        return self::$bodyParams;
    }

    public function resultType(): Type
    {
        return self::$resultType;
    }

    public function authentication(): ApiAuthentication
    {
        return new ApiAuthentication(self::$authentication);
    }
}
