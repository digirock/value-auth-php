<?php

namespace ValueAuth\ApiInput;

use ValueAuth\Enum\SendKbn;

class GetContactInput extends ApiInput
{
    /**
     * @var ?SendKbn
     * @Type('ValueAuth\Enum\SendKbn')
     */
    public $send_kbn;
}