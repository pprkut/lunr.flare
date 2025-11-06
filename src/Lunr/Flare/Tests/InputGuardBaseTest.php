<?php

/**
 * This file contains the InputGuardBaseTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2025 Framna Netherlands B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Flare\Tests;

use Lunr\Flare\Exceptions\UninitializedException;
use Lunr\Flare\InputGuard;
use Lunr\Flare\InputValue;

/**
 * This class contains base tests for the InputGuard class.
 *
 * @covers Lunr\Flare\InputGuard
 */
class InputGuardBaseTest extends InputGuardTestCase
{

    /**
     * Testcase Constructor.
     */
    public function setUp(): void
    {
        parent::setUpWithoutClass();

        $this->subject = new InputValue('key', 'value1');

        $this->class = new InputGuard($this->request, $this->filterFactory, $this->subject);

        parent::baseSetUp($this->class);
    }

    /**
     * Test that the Api class was passed correctly.
     */
    public function testRequestPassedCorrectly(): void
    {
        $this->assertPropertySame('request', $this->request);
    }

    /**
     * Test that the FilterFactory class was passed correctly.
     */
    public function testFilterFactoryPassedCorrectly(): void
    {
        $this->assertPropertySame('factory', $this->filterFactory);
    }

    /**
     * Test that the ValueFilter class was set correctly.
     */
    public function testValueFilterSetCorrectly(): void
    {
        $this->assertPropertySame('filter', $this->valueFilter);
    }

    /**
     * Test that the InputValue class was passed correctly.
     */
    public function testInputValuePassedCorrectly(): void
    {
        $this->assertPropertySame('subject', $this->subject);
    }

    /**
     * Test that verifySubjectIsSet() does not throw an exception when subject is set.
     *
     * @covers Lunr\Flare\InputGuard::verifySubjectIsSet
     */
    public function testVerifySubjectIsSetDoesNotThrowException(): void
    {
        $method = $this->getReflectionMethod('verifySubjectIsSet');

        $this->assertNull($method->invoke($this->class));
    }

    /**
     * Test that verifySubjectIsSet() throws an exception when subject is not set.
     *
     * @covers Lunr\Flare\InputGuard::verifySubjectIsSet
     */
    public function testVerifySubjectIsSetThrowsException(): void
    {
        $class = new InputGuard($this->request, $this->filterFactory);

        $this->expectException(UninitializedException::class);
        $this->expectExceptionMessage('The input value topic has not been initialized yet!');

        $method = $this->getReflectionMethod('verifySubjectIsSet');

        $method->invoke($class);
    }

    /**
     * Test that getValue() returns the inspected input value.
     *
     * @covers Lunr\Flare\InputGuard::getValue
     */
    public function testGetValueReturnsInspectedValue(): void
    {
        $this->assertEquals('value1', $this->class->getValue());
    }

}

?>
