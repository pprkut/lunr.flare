<?php

/**
 * This file contains the InputGuardFromBase64Test class.
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
class InputGuardFromBase64Test extends InputGuardTestCase
{

    private string $value = 'eyJpZGVudGlmaWVyIjoiZm9vIn0=';

    private string $decoded = '{"identifier":"foo"}';

    /**
     * Testcase Constructor.
     */
    public function setUp(): void
    {
        parent::setUpWithoutClass();

        $this->subject = new InputValue('key1', $this->value);

        $this->class = new InputGuard($this->request, $this->filterFactory, $this->subject);

        parent::baseSetUp($this->class);
    }

    /**
     * Test that fromBase64() throws an exception when the value is NULL.Subject
     *
     * @covers Lunr\Flare\InputGuard::fromBase64
     */
    public function testFromBase64ThrowsExceptionWhenValueIsNull(): void
    {
        $this->valueFilter->shouldReceive('validate')
                          ->once()
                          ->with($this->value, 'null')
                          ->andReturn(TRUE);

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Key1 is missing!');

        $this->class->fromBase64();
    }

    /**
     * Test that fromBase64() throws an exception when the value is blank.
     *
     * @covers Lunr\Flare\InputGuard::fromBase64
     */
    public function testFromBase64ThrowsExceptionWhenValueIsBlank(): void
    {
        $this->valueFilter->shouldReceive('validate')
                          ->once()
                          ->with($this->value, 'null')
                          ->andReturn(FALSE);

        $this->valueFilter->shouldReceive('validate')
                          ->once()
                          ->with($this->value, 'blank')
                          ->andReturn(TRUE);

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Key1 should not have validated as "blank"!');

        $this->class->fromBase64();
    }

    /**
     * Test that fromBase64() throws an exception when the value is not a string.
     *
     * @covers Lunr\Flare\InputGuard::fromBase64
     */
    public function testFromBase64ThrowsExceptionWhenValueIsNoString(): void
    {
        $this->valueFilter->shouldReceive('validate')
                          ->once()
                          ->with($this->value, 'null')
                          ->andReturn(FALSE);

        $this->valueFilter->shouldReceive('validate')
                          ->once()
                          ->with($this->value, 'blank')
                          ->andReturn(FALSE);

        $this->valueFilter->shouldReceive('validate')
                          ->once()
                          ->with($this->value, 'string')
                          ->andReturn(FALSE);

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Key1 did not validate as "string"!');

        $this->class->fromBase64();
    }

    /**
     * Test that fromBase64() throws an exception when the value is invalid base64.
     *
     * @covers Lunr\Flare\InputGuard::fromBase64
     */
    public function testFromBase64ThrowsExceptionWhenValueIsInvalidBase64(): void
    {
        $value = '{}';

        $this->valueFilter->shouldReceive('validate')
                          ->once()
                          ->with($value, 'null')
                          ->andReturn(FALSE);

        $this->valueFilter->shouldReceive('validate')
                          ->once()
                          ->with($value, 'blank')
                          ->andReturn(FALSE);

        $this->valueFilter->shouldReceive('validate')
                          ->once()
                          ->with($value, 'string')
                          ->andReturn(TRUE);

        $subject = new InputValue('key1', $value);

        $class = new InputGuard($this->request, $this->filterFactory, $subject);

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Key1 is invalid Base64!');

        $class->fromBase64();
    }

    /**
     * Test that fromBase64() returns InputGuard instance with decoded base64 as subject.
     *
     * @covers Lunr\Flare\InputGuard::fromBase64
     */
    public function testFromBase64ReturnsInputGuardWithDecodedBase64AsSubject(): void
    {
        $this->valueFilter->shouldReceive('validate')
                          ->once()
                          ->with($this->value, 'null')
                          ->andReturn(FALSE);

        $this->valueFilter->shouldReceive('validate')
                          ->once()
                          ->with($this->value, 'blank')
                          ->andReturn(FALSE);

        $this->valueFilter->shouldReceive('validate')
                          ->once()
                          ->with($this->value, 'string')
                          ->andReturn(TRUE);

        $object = $this->class->fromBase64();

        $this->assertInstanceOf(InputGuard::class, $object);

        $property = $this->getReflectionProperty('subject');

        $subject = $property->getValue($object);

        $this->assertInstanceOf(InputValue::class, $subject);
        $this->assertSame('key1', $subject->name);
        $this->assertSame($this->decoded, $subject->value);
        $this->assertSame($this->value, $subject->rawValue);
    }

}

?>
