<?php

namespace ValueAuth\ApiEndpoint;

use ValueAuth\ApiResult\CountryRestrictionListResult;

class GetCountryRestrictionEndpoint extends ApiEndpoint
{

    public static $method = 'put';
    public static $authentication = '/twofactor/oversea';
    public static $resultType = CountryRestrictionListResult::class;
}