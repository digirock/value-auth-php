<?php


namespace ValueAuth;

use Tebru\Gson\Annotation\Type;

class ApiErrorContent
{
    /**
     * @var string
     */
    public $code;

    /**
     * @var string
     */
    public $message;
}

class ApiError
{
    /**
     * @Type("ValueAuth\ApiErrorContent")
     * @var ApiErrorContent
     */
    public $errors;
}