<?php

namespace ValueAuth\ApiInput;

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