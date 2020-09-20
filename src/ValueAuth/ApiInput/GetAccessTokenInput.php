<?php

namespace ValueAuth\ApiInput;

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