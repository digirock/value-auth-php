<?php


namespace ValueAuth\ApiResult;


use ValueAuth\Enum\AuthKbn;

class SiteSettingResultContent
{
    /**
     * @var string
     */
    public $code;

    /**
     * @var AuthKbn
     */
    public $auth_kbn;

    /**
     * @var bool
     */
    public $is_sms_send;


    /**
     * @var bool
     */
    public $is_mail_send;

    /**
     * @var bool
     */
    public $is_location_auth;

    /**
     * @var bool
     */
    public $is_active;
}