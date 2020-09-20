<?php

namespace ValueAuth\ApiEndpoint;

use ValueAuth\ApiResult\AccessTokenResult;

class GetAccessTokenEndpoint extends ApiEndpoint
{
    public static $method = 'get';
    public static $path = '/{auth_code}/auth/accesstoken';
    public static $pathParams = ['auth_code'];
    public static $queryParams = ['customer_key', 'role'];
    public static $resultType = AccessTokenResult::class;
}