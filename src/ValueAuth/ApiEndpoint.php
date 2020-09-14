<?php


namespace ValueAuth;


const GetAccessTokenEndpoint = [
    'method' => 'get',
    'path' => '/{auth_code}/auth/accesstoken',
    'pathParams' => ['auth_code'],
    'queryParams' => ['customer_key', 'role'],
    'resultType' => AccessTokenResult::class
];

const GetKycEndpoint = [
    'method' => 'get',
    'path' => '/;auth',
    'pathParams' => [],
    'queryParams' => [],
    'bodyParams' => ['send_kbn', 'address', 'ip'],
    'resultType' => DueDateResult::class
];

const PostKycEndpoint = [
    'method' => 'post',
    'path' => '/auth',
    'pathParams' => [],
    'queryParams' => [],
    'bodyParams' => ['address', 'number'],
    'resultType' => StringResult::class
];

const Get2FACodeEndpoint = [
    'method' => 'get',
    'path' => '/twofactor',
    'pathParams' => [],
    'queryParams' => ['customer_key', 'ip'],
    'bodyParams' => [],
    'resultType' => TwoFactorAuthSendResult::class
];

const Post2FACodeEndpoint = [
    'method' => 'post',
    'path' => '/twofactor',
    'pathParams' => [],
    'queryParams' => [],
    'bodyParams' => ['customer_key', 'number'],
    'resultType' => AuthTokenResult::class
];

const GetContactEndpoint = [
    'method' => 'get',
    'path' => '/twofactor/contact',
    'pathParams' => [],
    'queryParams' => ['customer_key', 'send_kbn'],
    'bodyParams' => [],
    'resultType' => ContactResult::class
];

const PostContactEndpoint = [
    'method' => 'post',
    'path' => '/twofactor/contact',
    'pathParams' => [],
    'queryParams' => [],
    'bodyParams' => ['customer_key', 'address', 'send_kbn'],
    'resultType' => ContactListResult::class
];

const PutContactEndpoint = [
    'method' => 'put',
    'path' => '/twofactor/contact/{id}',
    'pathParams' => ['id'],
    'queryParams' => ['id'],
    'bodyParams' => ['customer_key', 'address', 'send_kbn'],
    'resultType' => ContactResult::class
];

const DeleteContactEndpoint = [
    'method' => 'delete',
    'path' => '/twofactor/contact/{id}',
    'pathParams' => ['id'],
    'queryParams' => ['customer_key'],
    'bodyParams' => [],
    'resultType' => ContactResult::class
];

const GetLocationRestrictionEndpoint = [
    'method' => 'get',
    'path' => '/twofactor/location',
    'pathParams' => [],
    'queryParams' => ['customer_key'],
    'bodyParams' => [],
    'resultType' => LocationRestrictionListResult::class
];

const PostLocationRestrictionEndpoint = [
    'method' => 'post',
    'path' => '/twofactor/location',
    'pathParams' => [],
    'queryParams' => [],
    'bodyParams' => ['customer_key', 'location_kbn', 'country', 'state', 'city', 'memo'],
    'resultType' => LocationRestrictionResult::class
];

const PutLocationRestrictionEndpoint = [
    'method' => 'put',
    'path' => '/twofactor/location/{id}',
    'pathParams' => ['id'],
    'queryParams' => [],
    'bodyParams' => ['customer_key', 'location_kbn', 'country', 'state', 'city', 'memo'],
    'resultType' => LocationRestrictionResult::class
];

const DeleteLocationRestrictionEndpoint = [
    'method' => 'delete',
    'path' => '/twofactor/location/{id}',
    'pathParams' => ['id'],
    'queryParams' => [],
    'bodyParams' => [],
    'resultType' => LocationRestrictionResult::class
];

const GetIpAddressRestrictionEndpoint = [
    'method' => 'get',
    'path' => '/twofactor/ip',
    'pathParams' => [],
    'queryParams' => ['customer_key'],
    'bodyParams' => [],
    'resultType' => IpAddressRestrictionListResult::class
];

const PostIpAddressRestrictionEndpoint = [
    'method' => 'post',
    'path' => '/twofactor/ip',
    'pathParams' => [],
    'queryParams' => ['customer_key', 'ip', 'access_kbn'],
    'bodyParams' => [],
    'resultType' => IpAddressRestrictionResult::class
];
const PutIpAddressRestrictionEndpoint = [
    'method' => 'put',
    'path' => '/twofactor/ip/{id}',
    'pathParams' => ['id'],
    'queryParams' => ['customer_key', 'ip', 'access_kbn'],
    'bodyParams' => [],
    'resultType' => IpAddressRestrictionResult::class
];

const DeleteIpAddressRestrictionEndpoint = [
    'method' => 'delete',
    'path' => '/twofactor/ip/{id}',
    'pathParams' => ['id'],
    'queryParams' => [],
    'bodyParams' => [],
    'resultType' => IpAddressRestrictionResult::class
];

const GetCountryRestrictionEndpoint = [
    'method' => 'put',
    'path' => '/twofactor/oversea',
    'pathParams' => [],
    'queryParams' => [],
    'bodyParams' => [],
    'resultType' => CountryRestrictionListResult::class
];

const PostCountryRestrictionEndpoint = [
    'method' => 'post',
    'path' => '/twofactor/oversea',
    'pathParams' => [],
    'queryParams' => [],
    'bodyParams' => ['customer_key', 'country', 'access_kbn'],
    'resultType' => CountryRestrictionResult::class
];

const PutCountryRestrictionEndpoint = [
    'method' => 'put',
    'path' => '/twofactor/oversea/{id}',
    'pathParams' => ['id'],
    'queryParams' => [],
    'bodyParams' => ['customer_key', 'country', 'access_kbn'],
    'resultType' => CountryRestrictionResult::class
];


const DeleteCountryRestrictionEndpoint = [
    'method' => 'delete',
    'path' => '/twofactor/oversea/{id}',
    'pathParams' => ['id'],
    'queryParams' => [],
    'bodyParams' => [],
    'resultType' => CountryRestrictionResult::class
];

const GetLoginLogEndpoint = [
    'method' => 'get',
    'path' => '/twofactor/loginlog',
    'pathParams' => [],
    'queryParams' => ['limit', 'page'],
    'bodyParams' => [],
    'resultType' => LoginLogListResult::class
];

const PostLoginLogEndpoint = [
    'method' => 'post',
    'path' => '/twofactor/loginlog',
    'pathParams' => [],
    'queryParams' => [],
    'bodyParams' => ['customer_key', 'ip', 'user_agent', 'is_success'],
    'resultType' => LoginLogResult::class
];


const GetCustomerSettingEndpoint = [
    'method' => 'get',
    'path' => '/twofactor/setting',
    'pathParams' => [],
    'queryParams' => ['customer_key'],
    'bodyParams' => [],
    'resultType' => CustomerSettingResult::class
];

const PutCustomerSettingEndpoint = [
    'method' => 'put',
    'path' => '/twofactor/setting',
    'pathParams' => [],
    'queryParams' => [],
    'bodyParams' => ['customer_key', 'max_attempts', 'security_level'],
    'resultType' => CustomerSettingResult::class
];
