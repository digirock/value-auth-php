<?php

namespace ValueAuth\ApiResult;

use ValueAuth\ApiInput\LocationKbn;

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