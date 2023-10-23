<?php

/**
 * Worker class exception.
 */
class WorkerException extends Exception
{
    /**
     * Constructor.
     *
     * @param string $message the error message
     * @param int $code the error code
     * @param Exception $previous the previous exception
     */
    public function __construct($message, $code = 0, Exception $previous = NULL)
    {
        // Make sure everything is assigned properly
        parent::__construct($message, $code);
    }
}
?>