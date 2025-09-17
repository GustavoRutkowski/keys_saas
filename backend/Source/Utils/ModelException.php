<?php

namespace Source\Utils;
use Exception;

class ModelException extends Exception {
    private int $httpStatus;

    public function __construct(
        string $message,
        int $httpStatus = 400,
        int $code = 0,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->httpStatus = $httpStatus;
    }

    public function getHttpStatus(): int {
        return $this->httpStatus;
    }
}
