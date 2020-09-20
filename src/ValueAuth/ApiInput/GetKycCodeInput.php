<?php

namespace ValueAuth\ApiInput;

use ValueAuth\Enum\SendKbn;

class GetKycCodeInput extends ApiInput
{
    /**
     * @var SendKbn
     * @Type('ValueAuth\Enum\SendKbn')
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