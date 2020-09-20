<?php

namespace ValueAuth\ApiInput;

use ValueAuth\Enum\SendKbn;

class PostContactInput extends ApiInput
{
    /**
     * @var string
     */
    public $address;

    /**
     * @var SendKbn
     * @Type('ValueAuth\Enum\SendKbn')
     */
    public $send_kbn;
}