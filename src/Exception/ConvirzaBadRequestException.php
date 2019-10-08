<?php

namespace Skidaatl\Convirza\Exception;

use Exception;

class ConvirzaBadRequestException extends ConvirzaException
{
    private $response;

    public function __construct($message = "", $code = 0, Exception $previous = null, $response = null)
    {
        parent::__construct($message, $code, $previous);
        $this->response = $response;
    }

    public function response(): array
    {
        return json_decode($this->response, true) ?? [];
    }
}
