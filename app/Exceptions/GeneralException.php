<?php
/**
 * Created by PhpStorm.
 * User: Dev Omar Shaheen
 * Date: 5/1/2019
 * Time: 12:12 AM
 */

namespace App\Exceptions;
use Exception;

class GeneralException extends Exception
{
    /**
     * message.
     *
     * @var string
     */
    public $message;

    /**
     * dontHide.
     *
     * @var bool
     */
    public $dontHide;

    /**
     * Constructor function.
     *
     * @param string $message
     * @param bool   $dontHide
     */
    public function __construct($message, $dontHide = false)
    {
        $this->message = $message;
        $this->dontHide = $dontHide;
    }
}