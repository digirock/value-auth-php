<?php


namespace ValueAuth;


use MyCLabs\Enum\Enum;
use Tebru\Gson\Annotation\Type;

trait HasId
{
    /**
     * @var int
     */
    public $id;
}

trait HasPaging
{
    /**
     * @var int
     */
    public $page;

    /**
     * @var int
     */
    public $limit;

}

class SendKbn extends Enum
{
    const Sms = 1;
    const Email = 2;
}

class AccessKbn extends Enum
{

    const Allow = 1;
    const Deny = 2;
}

class LocationKbn extends Enum
{
    const Abst = 1;
    const Detailed = 2;
}

class ApiInput
{
    /**
     * @var ?string
     */
    public $customer_key;
}

class GetAccessTokenInput extends ApiInput
{
    /**
     * @var string
     */
    public $auth_code;

    /**
     * @var string
     */
    public $role;
}

class GetKycCodeInput extends ApiInput
{
    /**
     * @var SendKbn
     * @Type('ValueAuth\SendKbn')
     */
    public $send_kbn;

    /**
     * @var string
     */
    public $address;

    /**
     * @var ?string
     */
    public $ip;
}

class PostKycCodeInput extends ApiInput
{
    /**
     * @var string
     */
    public $address;

    /**
     * @var string
     */
    public $number;
}

class Get2FACodeInput extends ApiInput
{
    /**
     * @var ?string
     */
    public $ip;
}

class Post2FACodeInput extends ApiInput
{
    /**
     * @var string
     */
    public $number;
}

class GetContactInput extends ApiInput
{
    /**
     * @var ?SendKbn
     * @Type('ValueAuth\SendKbn')
     */
    public $send_kbn;
}

class PostContactInput extends ApiInput
{
    /**
     * @var string
     */
    public $address;

    /**
     * @var SendKbn
     * @Type('ValueAuth\SendKbn')
     */
    public $send_kbn;
}

class PutContactInput extends PostContactInput
{
    use HasId;
}

class DeleteContactInput extends ApiInput
{
    use HasId;
}

class GetLocationRestrictionInput extends ApiInput
{

}

class PostLocationRestrictionInput extends ApiInput
{
    /**
     * @var LocationKbn
     */
    public $location_kbn;

    /**
     * @var string
     */
    public $country;

    /**
     * @var string
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

class PutLocationRestrictionInput extends PostLocationRestrictionInput
{
    use HasId;
}

class DeleteLocationRestrictionInput extends ApiInput
{
    use HasId;
}

class GetIpAddressRestrictionInput extends ApiInput
{

}

class PostIpAddressRestrictionInput extends ApiInput
{
    /**
     * @var string
     */
    public $ip;

    /**
     * @var AccessKbn
     * @Type('ValueAuth\AccessKbn')
     */
    public $access_kbn;
}

class PutIpAddressRestrictionInput extends PostIpAddressRestrictionInput
{
    use HasId;
}

class DeleteIpAddressRestrictionInput extends ApiInput
{
    use HasId;
}

class GetCountryRestrictionInput extends ApiInput
{

}

class PostCountryRestrictionInput extends ApiInput
{
    /**
     * @var string
     */
    public $country;
    /**
     * @var AccessKbn
     */
    public $access_kbn;
}

class PutCountryRestrictionInput extends PostCountryRestrictionInput
{
    use HasId;
}

class DeleteCountryRestrictionInput extends ApiInput
{
    use HasId;
}

class GetLoginLogInput extends ApiInput
{
    use HasPaging;
}

class PostLoginLogInput extends ApiInput
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
     * @var bool
     */
    public $is_success;
}

class GetCustomerSettingInput extends ApiInput
{

}

class PutCustomerSettingInput extends ApiInput
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