<?php

/**
 * MySQL SQLException.
 *
 * @package MySQL_Database_Utilities
 * @license GNU Lesser General Public License
 * @since Version 1.0
 */
class SQLException extends Exception
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
