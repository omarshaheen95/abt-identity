<?php
namespace App\Helpers;


class ResponseStatus
{
    const SUCCESS = 200;
    const CREATED = 201;
    const NO_CONTENT = 204;

    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const NOT_ALLOWED = 405 ;
    const VALIDATION_ERROR = 422;
    const TOO_MANY_REQUESTS = 429;

    const GENERAL_ERROR = 500;

}