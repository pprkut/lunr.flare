<?php

/**
 * This file contains the InputGuardFromJsonTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2025 Framna Netherlands B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Flare\Tests;

use Lunr\Corona\Exceptions\BadRequestException;
use Lunr\Flare\InputGuard;
use Lunr\Flare\InputValue;
use stdClass;

/**
 * This class contains tests for the InputGuard class.
 *
 * @covers Lunr\Flare\InputGuard
 */
class InputGuardFromJsonTest extends InputGuardTestCase
{

    private string $value = '{"identifier":"foo"}';

    private stdClass $decodedObject;

    private array $decodedArray = [ 'identifier' => 'foo' ];

    /**
     * Testcase Constructor.
     */
    public function setUp(): void
    {
        parent::setUpWithoutClass();

        $this->decodedObject = new stdClass();

        $this->decodedObject->identifier = 'foo';

        $this->subject = new InputValue('key1', $this->value);

        $this->class = new InputGuard($this->request, $this->filterFactory, $this->subject);

        parent::baseSetUp($this->class);
    }

    /**
     * Test that fromJson() throws an exception when the value is NULL.Subject
     *
     * @covers Lunr\Flare\InputGuard::fromJson
     */
    public function testFromJsonThrowsExceptionWhenValueIsNull(): void
    {
        $this->valueFilter->shouldReceive('validate')
                          ->once()
                          ->with($this->value, 'null')
                          ->andReturn(TRUE);

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Key1 is missing!');

        $this->class->fromJson();
    }

    /**
     * Test that fromJson() throws an exception when the value is blank.
     *
     * @covers Lunr\Flare\InputGuard::fromJson
     */
    public function testFromJsonThrowsExceptionWhenValueIsBlank(): void
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

        $this->class->fromJson();
    }

    /**
     * Test that fromJson() throws an exception when the value is not a string.
     *
     * @covers Lunr\Flare\InputGuard::fromJson
     */
    public function testFromJsonThrowsExceptionWhenValueIsNoString(): void
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

        $this->class->fromJson();
    }

    /**
     * Test that fromJson() throws an exception when the value is invalid json.
     *
     * @covers Lunr\Flare\InputGuard::fromJson
     */
    public function testFromJsonThrowsExceptionWhenValueIsInvalidJson(): void
    {
        $value = '{';

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
        $this->expectExceptionMessage('Key1 is invalid JSON!');

        $class->fromJson();
    }

    /**
     * Test that fromJson() returns InputGuard instance with decoded json as subject.
     *
     * @covers Lunr\Flare\InputGuard::fromJson
     */
    public function testFromJsonReturnsInputGuardWithJsonDecodedToArrayAsSubject(): void
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

        $object = $this->class->fromJson(object: FALSE);

        $this->assertInstanceOf(InputGuard::class, $object);

        $property = $this->getReflectionProperty('subject');

        $subject = $property->getValue($object);

        $this->assertInstanceOf(InputValue::class, $subject);
        $this->assertSame('key1', $subject->name);
        $this->assertSame($this->decodedArray, $subject->value);
        $this->assertSame($this->value, $subject->rawValue);
    }

    /**
     * Test that fromJson() returns InputGuard instance with decoded json as subject.
     *
     * @covers Lunr\Flare\InputGuard::fromJson
     */
    public function testFromJsonReturnsInputGuardWithJsonDecodedToObjectAsSubject(): void
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

        $object = $this->class->fromJson(object: TRUE);

        $this->assertInstanceOf(InputGuard::class, $object);

        $property = $this->getReflectionProperty('subject');

        $subject = $property->getValue($object);

        $this->assertInstanceOf(InputValue::class, $subject);
        $this->assertSame('key1', $subject->name);
        $this->assertEquals($this->decodedObject, $subject->value);
        $this->assertSame($this->value, $subject->rawValue);
    }

}

?>
