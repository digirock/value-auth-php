<?php

namespace ValueAuth\ApiEndpoint;

use ValueAuth\ApiResult\LoginLogResult;
use ValueAuth\Enum\ApiAuthentication;

class PostLoginLogEndpoint extends ApiEndpoint
{
    public static $method = 'post';
    public static $path = '/twofactor/loginlog';
    public static $bodyParams = ['customer_key', 'login_key', 'is_logged_in'];
    public static $resultType = LoginLogResult::class;
    public static $authentication = ApiAuthentication::ApiKey;
}