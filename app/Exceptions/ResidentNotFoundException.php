<?php
namespace App\Exceptions;

use Exception;

class ResidentNotFoundException extends Exception
{
    public function __construct(string $message = "Resident not found", int $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
?>
