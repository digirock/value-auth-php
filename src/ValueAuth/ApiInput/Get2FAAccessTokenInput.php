<?php

namespace ValueAuth\ApiInput;

use ValueAuth\ApiEndpoint\Get2FAAccessTokenEndpoint;

class Get2FAAccessTokenInput extends ApiInput
{
    /**
     * @var ?string
     */
    public $login_key;

    /**
     * @var string
     */
    public $role;

    /**
     * @var string
     */
    public static $endpointType = Get2FAAccessTokenEndpoint::class;
}