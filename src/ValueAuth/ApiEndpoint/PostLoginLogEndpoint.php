<?php

namespace ValueAuth\ApiEndpoint;

use ValueAuth\ApiResult\LoginLogResult;

class PostLoginLogEndpoint extends ApiEndpoint
{
    public static $method = 'post';
    public static $path = '/twofactor/loginlog';
    public static $bodyParams = ['customer_key', 'ip', 'user_agent', 'is_success'];
    public static $resultType = LoginLogResult::class;
}