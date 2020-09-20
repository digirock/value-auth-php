<?php

namespace ValueAuth\ApiInput;

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