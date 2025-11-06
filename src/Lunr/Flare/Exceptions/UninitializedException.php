<?php

/**
 * This file contains the UninitializedException class.
 *
 * SPDX-FileCopyrightText: Copyright 2025 Framna Netherlands B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Flare\Exceptions;

use Exception;
use UnexpectedValueException;

/**
 * Exception class for uninitialized input topics.
 */
class UninitializedException extends UnexpectedValueException
{

    /**
     * Constructor.
     *
     * @param string         $message  Error message
     * @param int            $code     Application error code
     * @param Exception|null $previous The previously thrown exception
     */
    public function __construct(
        string $message = 'The input value topic has not been initialized yet!',
        int $code = 0,
        ?Exception $previous = NULL
    )
    {
        parent::__construct($message, $code, $previous);
    }

}

?>
