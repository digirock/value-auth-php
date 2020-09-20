<?php


namespace ValueAuth\ApiResult;

use Tebru\Gson\Annotation\Type;

class ApiError
{
    /**
     * @Type("ValueAuth\ApiResult\ApiErrorContent")
     * @var ApiErrorContent
     */
    public $errors;
}