<?php


namespace ValueAuth\ApiInput;


use ValueAuth\Enum\ApiAuthentication;

class PostLoginCheckEndpoint extends \ValueAuth\ApiEndpoint\ApiEndpoint
{
    public static $method = 'post';
    public static $path = '/{auth_code}/twofactor/checklogin';
    public static $pathParams = ['auth_code'];
    public static $bodyParams = ['customer_key', 'ip', 'user_agent'];
    public static $authentication = ApiAuthentication::ApiKey;
}