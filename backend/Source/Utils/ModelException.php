<?php

namespace Source\Utils;
use Exception;

/**
 * ModelException class:
    * This class is a custom exception handler designed for model and business logic errors.
    * It extends the base PHP Exception class, adding support for an associated HTTP status code.
    * This allows throwing structured errors that can be easily translated into API responses.

 * Constructor:
    * @param string $message -> The error message describing what went wrong.
    * @param int $httpStatus -> Optional. The HTTP status code that represents the nature of the error. Defaults to 400 (Bad Request).
    * @param int $code -> Optional. A custom internal code for the exception (default is 0).
    * @param ?Exception $previous -> Optional. A previous exception for exception chaining.

    * @example:
     * # e.g. 1 - Default usage:
     * throw new ModelException("Invalid user data");
     * 
     * # e.g. 2 - Custom HTTP status:
     * throw new ModelException("User not found", 404);
     * 
     * # e.g. 3 - With custom internal code:
     * throw new ModelException("Permission denied", 403, 1001);

 * getHttpStatus method:
    * Returns the HTTP status code associated with the exception.

    * @return int -> The HTTP status code defined when the exception was thrown.

    * @example:
     * # e.g.:
     * try {
     *     throw new ModelException("Resource not found", 404);
     * } catch (ModelException $e) {
     *     http_response_code($e->getHttpStatus());
     *     echo json_encode(["error" => $e->getMessage()]);
     * }

 * Example integration in a controller:
    * @example:
     * try {
     *     $user = $userModel->findById($id);
     *     if (!$user) {
     *         throw new ModelException("User not found", 404);
     *     }
     * } catch (ModelException $e) {
     *     http_response_code($e->getHttpStatus());
     *     echo json_encode(["error" => $e->getMessage()]);
     * }
 */
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
