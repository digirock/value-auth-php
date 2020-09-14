<?php


namespace ValueAuth;

class Model
{
    /**
     * @var ?int
     */
    public $id;
}

class ApiResultContent
{

}

class Customer extends Model
{
    /**
     * @var string
     */
    public $customer_key;
}

class  Contact extends Model
{
    /**
     * @var ?string
     */
    public $address;

    /**
     * @var ?SendKbn
     */
    public $send_kbn;
}

class LocationRestriction extends Model
{
    /**
     * @var LocationKbn
     */
    public $location_kbn;

    /**
     * @var ?string
     */
    public $country;

    /**
     * @var ?string
     */
    public $state;

    /**
     * @var ?string
     */
    public $city;

    /**
     * @var ?string
     */
    public $memo;
}

class IpAddressRestriction extends Model
{
    /**
     * @var string
     */
    public $ip;

    /**
     * @var AccessKbn
     */
    public $access_kbn;
}

class LoginLog extends Model
{
    /**
     * @var string
     */
    public $ip;

    /**
     * @var string
     */
    public $user_agent;

    /**
     * @var string
     */
    public $address;

    /**
     * @var bool
     */
    public $is_success;

    /**
     * @var string
     */
    public $fraud_point;

    /**
     * @var ?\DateTime
     */
    public $date;
}

class CountryRestriction extends Model
{
    /**
     * @var ?string
     */
    public $country;

    /**
     * @var ?AccessKbn
     */
    public $access_kbn;
}

class CustomerSetting extends Model
{

    /**
     * @var ?int
     */
    public $max_attempts;

    /**
     * @var ?int
     */
    public $security_level;
}

class DueDateResultContent extends ApiResultContent
{
    /**
     * @var string
     */
    public $due_date;
}

class  TwoFactorAuthSendResultContent extends ApiResultContent
{
    /**
     * @var string
     */
    public $customer_key;

    /**
     * @var string
     */
    public $due_date;
}

class AccessTokenResultContent extends ApiResultContent
{
    /**
     * @var string
     */
    public $access_token;
}

class AuthTokenResultContent extends ApiResultContent
{
    /**
     * @var string
     */
    public $state;

    /**
     * @var string
     */
    public $auth_token;
}

class ContactListResultContent extends ApiResultContent
{
    /**
     * @var Customer
     */
    public $customer;

    /**
     * @var Contact[]
     */
    public $customer_contacts;
}

class ContactResultContent extends ApiResultContent
{

    /**
     * @var Customer
     */
    public $customer;

    /**
     * @var Contact
     */
    public $customer_contact;
}

class LocationRestrictionListResultContent extends ApiResultContent
{
    /**
     * @var Customer
     */
    public $customer;

    /**
     * @var LocationRestriction[]
     */
    public $customer_locations;
}

class LocationRestrictionResultContent extends ApiResultContent
{
    /**
     * @var Customer
     */
    public $customer;

    /**
     * @var LocationRestriction
     */
    public $customer_location;
}

class IpAddressRestrictionListResultContent extends ApiResultContent
{
    /**
     * @var Customer
     */
    public $customer;

    /**
     * @var IpAddressRestriction[]
     */
    public $customer_ip;
}

class IpAddressRestrictionResultContent extends ApiResultContent
{
    /**
     * @var Customer
     */
    public $customer;

    /**
     * @var IpAddressRestriction
     */
    public $customer_ip;
}

class LoginLogListResultContent extends ApiResultContent
{
    /**
     * @var Customer
     */
    public $customer;

    /**
     * @var LoginLog[]
     */
    public $customer_login_logs;
}

class LoginLogResultContent extends ApiResultContent
{
    /**
     * @var Customer
     */
    public $customer;

    /**
     * @var LoginLog
     */
    public $customer_login_log;
}

class CountryRestrictionListResultContent extends ApiResultContent
{
    /**
     * @var Customer
     */
    public $customer;

    /**
     * @var CountryRestriction[]
     */
    public $customer_oversea;
}

class CountryRestrictionResultContent extends ApiResultContent
{
    /**
     * @var Customer
     */
    public $customer;

    /**
     * @var CountryRestriction
     */
    public $customer_oversea;
}

class CustomerSettingResultContent extends ApiResultContent
{

    /**
     * @var Customer
     */
    public $customer;

    /**
     * @var CustomerSetting
     */
    public $customer_setting;
}

class ApiResult
{

    public $results;
}

class ContactListResult extends ApiResult
{
    /**
     * @var ContactListResultContent
     */
    public $results;
}

class ContactResult extends ApiResult
{
    /**
     * @var ContactResultContent
     */
    public $results;
}

class AuthTokenResult extends ApiResult
{
    /**
     * @var AuthTokenResultContent
     */
    public $results;
}

class TwoFactorAuthSendResult extends ApiResult
{
    /**
     * @var TwoFactorAuthSendResultContent
     */
    public $results;
}

class AccessTokenResult extends ApiResult
{
    /**
     * @var AccessTokenResultContent
     */
    public $results;
}

class CountryRestrictionListResult extends ApiResult
{
    /**
     * @var CountryRestrictionListResultContent
     */
    public $results;
}

class CountryRestrictionResult extends ApiResult
{
    /**
     * @var CountryRestrictionResultContent
     */
    public $results;
}


class LocationRestrictionListResult extends ApiResult
{
    /**
     * @var LocationRestrictionListResultContent
     */
    public $results;
}

class LocationRestrictionResult extends ApiResult
{
    /**
     * @var LocationRestrictionResultContent
     */
    public $results;
}

class IpAddressRestrictionListResult extends ApiResult
{
    /**
     * @var IpAddressRestrictionListResultContent
     */
    public $results;
}

class IpAddressRestrictionResult extends ApiResult
{
    /**
     * @var IpAddressRestrictionResultContent
     */
    public $results;
}

class LoginLogListResult extends ApiResult
{
    /**
     * @var LoginLogListResultContent
     */
    public $results;
}

class LoginLogResult extends ApiResult
{
    /**
     * @var LoginLogResultContent
     */
    public $results;
}

class CustomerSettingResult extends ApiResult
{
    /**
     * @var CustomerSettingResultContent
     */
    public $results;
}

class DueDateResult extends ApiResult
{
    /**
     * @var DueDateResultContent
     */
    public $results;
}

class StringResult extends ApiResult
{
    /**
     * @var string
     */
    public $results;
}