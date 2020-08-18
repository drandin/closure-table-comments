<?php


namespace Drandin\ClosureTableComments\Exceptions;

use RuntimeException;
use Throwable;

/**
 * Class ExceptionCreateStructure
 *
 * @package Drandin\ClosureTableComments\Exceptions
 */
class ExceptionStructure extends RuntimeException
{
    /**
     * ExceptionCreateStructure constructor.
     *
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
