<?php

/**
 * This file contains the InputGuardCheckTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2025 Framna Netherlands B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Flare\Tests;

use Lunr\Corona\Exceptions\BadRequestException;
use Lunr\Flare\InputGuard;
use Lunr\Flare\InputValue;

/**
 * This class contains tests for the InputGuard class.
 *
 * @covers Lunr\Flare\InputGuard
 */
class InputGuardCheckTest extends InputGuardTestCase
{

    /**
     * Testcase Constructor.
     */
    public function setUp(): void
    {
        parent::setUpWithoutClass();

        $this->subject = new InputValue('key1', 'value1');

        $this->class = new InputGuard($this->request, $this->filterFactory, $this->subject);

        parent::baseSetUp($this->class);
    }

    /**
     * Test that is() throws an exception when check fails.
     *
     * @covers Lunr\Flare\InputGuard::is
     */
    public function testIsThrowsExceptionWhenCheckFails(): void
    {
        $this->valueFilter->shouldReceive('validate')
                          ->once()
                          ->with('value1', 'null')
                          ->andReturn(FALSE);

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Key1 did not validate as "null"!');

        $this->class->is('null');
    }

    /**
     * Test that is() throws an exception when check fails.
     *
     * @covers Lunr\Flare\InputGuard::is
     */
    public function testIsThrowsExceptionWithCustomErrorMessageWhenCheckFails(): void
    {
        $this->valueFilter->shouldReceive('validate')
                          ->once()
                          ->with('value1', 'null')
                          ->andReturn(FALSE);

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Key1 is not NULL!');

        $this->class->is('null', message: 'Key1 is not NULL!');
    }

    /**
     * Test that is() with rule arguments.
     *
     * @covers Lunr\Flare\InputGuard::is
     */
    public function testIsWithRuleArguments(): void
    {
        $this->valueFilter->shouldReceive('validate')
                          ->once()
                          ->with('value1', 'between', 1, 3)
                          ->andReturn(TRUE);

        $value = $this->class->is('between', [ 1, 3 ]);

        $this->assertSame($this->class, $value);
    }

    /**
     * Test that is() returns itself on success.
     *
     * @covers Lunr\Flare\InputGuard::is
     */
    public function testIsReturnsItselfOnSuccess(): void
    {
        $this->valueFilter->shouldReceive('validate')
                          ->once()
                          ->with('value1', 'null')
                          ->andReturn(TRUE);

        $value = $this->class->is('null');

        $this->assertSame($this->class, $value);
    }

    /**
     * Test that not() throws an exception when check fails.
     *
     * @covers Lunr\Flare\InputGuard::not
     */
    public function testNotThrowsExceptionWhenCheckFails(): void
    {
        $this->valueFilter->shouldReceive('validate')
                          ->once()
                          ->with('value1', 'blank')
                          ->andReturn(TRUE);

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Key1 should not have validated as "blank"!');

        $this->class->not('blank');
    }

    /**
     * Test that not() throws an exception when check fails.
     *
     * @covers Lunr\Flare\InputGuard::not
     */
    public function testNotThrowsExceptionWithCustomErrorMessageWhenCheckFails(): void
    {
        $this->valueFilter->shouldReceive('validate')
                          ->once()
                          ->with('value1', 'blank')
                          ->andReturn(TRUE);

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Key1 is not blank!');

        $this->class->not('blank', message: 'Key1 is not blank!');
    }

    /**
     * Test that not() throws an exception when check fails.
     *
     * @covers Lunr\Flare\InputGuard::not
     */
    public function testNotThrowsExceptionWhenCheckFailsForNull(): void
    {
        $this->valueFilter->shouldReceive('validate')
                          ->once()
                          ->with('value1', 'null')
                          ->andReturn(TRUE);

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Key1 is missing!');

        $this->class->not('null');
    }

    /**
     * Test that not() throws an exception when check fails.
     *
     * @covers Lunr\Flare\InputGuard::not
     */
    public function testNotThrowsExceptionWithCustomErrorMessageWhenCheckFailsForNull(): void
    {
        $this->valueFilter->shouldReceive('validate')
                          ->once()
                          ->with('value1', 'null')
                          ->andReturn(TRUE);

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Key1 is NULL!');

        $this->class->not('null', message: 'Key1 is NULL!');
    }

    /**
     * Test that not() with rule arguments.
     *
     * @covers Lunr\Flare\InputGuard::not
     */
    public function testNotWithRuleArguments(): void
    {
        $this->valueFilter->shouldReceive('validate')
                          ->once()
                          ->with('value1', 'between', 1, 3)
                          ->andReturn(FALSE);

        $value = $this->class->not('between', [ 1, 3 ]);

        $this->assertSame($this->class, $value);
    }

    /**
     * Test that not() returns itself on success.
     *
     * @covers Lunr\Flare\InputGuard::not
     */
    public function testNotReturnsItselfOnSuccess(): void
    {
        $this->valueFilter->shouldReceive('validate')
                          ->once()
                          ->with('value1', 'null')
                          ->andReturn(FALSE);

        $value = $this->class->not('null');

        $this->assertSame($this->class, $value);
    }

}

?>
