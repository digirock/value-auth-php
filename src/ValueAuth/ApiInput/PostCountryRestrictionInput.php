<?php

namespace ValueAuth\ApiInput;

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