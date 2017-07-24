<?php
namespace Roboto;


class InvalidMovementException extends \Exception
{
    public function __construct($message = 'Invalid Movement', $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}