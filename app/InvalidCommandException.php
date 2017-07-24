<?php
namespace Roboto;


class InvalidCommandException extends \Exception
{
    public function __construct($message = 'An invalid command has been given', $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}