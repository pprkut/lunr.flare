<?php

/**
 * This file contains the InputGuardUninitializedSubjectTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2025 Framna Netherlands B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Flare\Tests;

use Lunr\Flare\Exceptions\UninitializedException;

/**
 * This class contains tests for the InputGuard class.
 *
 * @covers Lunr\Flare\InputGuard
 */
class InputGuardUninitializedSubjectTest extends InputGuardTestCase
{

    /**
     * Test that verifySubjectIsSet() throws an exception when subject is not set.
     *
     * @covers Lunr\Flare\Exceptions\UninitializedException::__construct()
     * @covers Lunr\Flare\InputGuard::verifySubjectIsSet
     */
    public function testVerifySubjectIsSetThrowsException(): void
    {
        $this->expectException(UninitializedException::class);
        $this->expectExceptionMessage('The input value topic has not been initialized yet!');

        $method = $this->getReflectionMethod('verifySubjectIsSet');

        $method->invoke($this->class);
    }

    /**
     * Test that getValue() throws an exception when subject is not set.
     *
     * @covers Lunr\Flare\Exceptions\UninitializedException::__construct()
     * @covers Lunr\Flare\InputGuard::getValue
     */
    public function testGetValueThrowsException(): void
    {
        $this->expectException(UninitializedException::class);
        $this->expectExceptionMessage('The input value topic has not been initialized yet!');

        $this->class->getValue();
    }

    /**
     * Test that is() throws an exception when subject is not set.
     *
     * @covers Lunr\Flare\Exceptions\UninitializedException::__construct()
     * @covers Lunr\Flare\InputGuard::is
     */
    public function testIsThrowsException(): void
    {
        $this->expectException(UninitializedException::class);
        $this->expectExceptionMessage('The input value topic has not been initialized yet!');

        $this->class->is('null');
    }

    /**
     * Test that not() throws an exception when subject is not set.
     *
     * @covers Lunr\Flare\Exceptions\UninitializedException::__construct()
     * @covers Lunr\Flare\InputGuard::not
     */
    public function testNotThrowsException(): void
    {
        $this->expectException(UninitializedException::class);
        $this->expectExceptionMessage('The input value topic has not been initialized yet!');

        $this->class->not('null');
    }

}

?>
