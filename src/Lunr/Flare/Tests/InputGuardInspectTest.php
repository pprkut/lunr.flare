<?php

/**
 * This file contains the InputGuardInspectTest class.
 *
 * SPDX-FileCopyrightText: Copyright 2025 Framna Netherlands B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Flare\Tests;

use Lunr\Corona\RequestData;
use Lunr\Flare\InputGuard;
use Lunr\Flare\InputValue;

/**
 * This class contains tests for the InputGuard class.
 *
 * @covers Lunr\Flare\InputGuard
 */
class InputGuardInspectTest extends InputGuardTestCase
{

    /**
     * Test that inspect() returns an InputGuard with set subject for an available required input value.
     *
     * @covers Lunr\Flare\InputGuard::inspect
     */
    public function testInspectReturnsInputGuardWithSetSubjectForAvailableRequiredValue(): void
    {
        $object = $this->class->inspect('key', 'value1');

        $this->assertInstanceOf(InputGuard::class, $object);

        $property = $this->getReflectionProperty('subject');

        $subject = $property->getValue($object);

        $this->assertInstanceOf(InputValue::class, $subject);
        $this->assertSame('key', $subject->name);
        $this->assertSame('value1', $subject->value);
    }

    /**
     * Test that inspect() returns an InputGuard with set subject for an available required input value.
     *
     * @covers Lunr\Flare\InputGuard::inspect
     */
    public function testInspectReturnsInputGuardWithSetSubjectForAvailableRequiredValueAndCustomRawValue(): void
    {
        $object = $this->class->inspect('key', 'value1', 'key=value1');

        $this->assertInstanceOf(InputGuard::class, $object);

        $property = $this->getReflectionProperty('subject');

        $subject = $property->getValue($object);

        $this->assertInstanceOf(InputValue::class, $subject);
        $this->assertSame('key', $subject->name);
        $this->assertSame('value1', $subject->value);
        $this->assertSame('key=value1', $subject->rawValue);
    }

    /**
     * Test that inspect() returns an InputGuard with set subject for an unavailable required input value.
     *
     * @covers Lunr\Flare\InputGuard::inspect
     */
    public function testInspectReturnsInputGuardWithSetSubjectForUnavailableRequiredValue(): void
    {
        $object = $this->class->inspect('key', NULL);

        $this->assertInstanceOf(InputGuard::class, $object);

        $property = $this->getReflectionProperty('subject');

        $subject = $property->getValue($object);

        $this->assertInstanceOf(InputValue::class, $subject);
        $this->assertSame('key', $subject->name);
        $this->assertNull($subject->value);
    }

    /**
     * Test that inspect() returns an InputGuard with set subject for an available optional input value.
     *
     * @covers Lunr\Flare\InputGuard::inspect
     */
    public function testInspectReturnsInputGuardWithSetSubjectForAvailableOptionalValue(): void
    {
        $object = $this->class->inspect('key', 'value1', optional: TRUE);

        $this->assertInstanceOf(InputGuard::class, $object);

        $property = $this->getReflectionProperty('subject');

        $subject = $property->getValue($object);

        $this->assertInstanceOf(InputValue::class, $subject);
        $this->assertSame('key', $subject->name);
        $this->assertSame('value1', $subject->value);
    }

    /**
     * Test that inspect() returns NULL for an unavailable optional input value.
     *
     * @covers Lunr\Flare\InputGuard::inspect
     */
    public function testInspectReturnsNullForUnavailableOptionalValue(): void
    {
        $object = $this->class->inspect('key', NULL, optional: TRUE);

        $this->assertNull($object);
    }

    /**
     * Test that getAndInspect() returns an InputGuard with set subject for an available required input value.
     *
     * @covers Lunr\Flare\InputGuard::getAndInspect
     */
    public function testGetAndInspectReturnsInputGuardWithSetSubjectForAvailableRequiredValue(): void
    {
        $this->request->expects($this->once())
                      ->method('getData')
                      ->with('key', RequestData::Raw)
                      ->willReturn('value1');

        $object = $this->class->getAndInspect('key', RequestData::Raw);

        $this->assertInstanceOf(InputGuard::class, $object);

        $property = $this->getReflectionProperty('subject');

        $subject = $property->getValue($object);

        $this->assertInstanceOf(InputValue::class, $subject);
        $this->assertSame('key', $subject->name);
        $this->assertSame('value1', $subject->value);
    }

    /**
     * Test that getAndInspect() returns an InputGuard with set subject for an available required input value.
     *
     * @covers Lunr\Flare\InputGuard::getAndInspect
     */
    public function testGetAndInspectReturnsInputGuardWithSetSubjectForAvailableRequiredArrayValueFromGet(): void
    {
        $this->request->expects($this->once())
                      ->method('getData')
                      ->with('key', RequestData::Get)
                      ->willReturn([ 'value1', 'value2' ]);

        $object = $this->class->getAndInspect('key', RequestData::Get);

        $this->assertInstanceOf(InputGuard::class, $object);

        $property = $this->getReflectionProperty('subject');

        $subject = $property->getValue($object);

        $this->assertInstanceOf(InputValue::class, $subject);
        $this->assertSame('key', $subject->name);
        $this->assertSame([ 'value1', 'value2' ], $subject->value);
        $this->assertSame('["value1","value2"]', $subject->rawValue);
    }

    /**
     * Test that getAndInspect() returns an InputGuard with set subject for an available required input value.
     *
     * @covers Lunr\Flare\InputGuard::getAndInspect
     */
    public function testGetAndInspectReturnsInputGuardWithSetSubjectForAvailableRequiredArrayValueFromPost(): void
    {
        $this->request->expects($this->once())
                      ->method('getData')
                      ->with('key', RequestData::Post)
                      ->willReturn([ 'value1', 'value2' ]);

        $object = $this->class->getAndInspect('key', RequestData::Post);

        $this->assertInstanceOf(InputGuard::class, $object);

        $property = $this->getReflectionProperty('subject');

        $subject = $property->getValue($object);

        $this->assertInstanceOf(InputValue::class, $subject);
        $this->assertSame('key', $subject->name);
        $this->assertSame([ 'value1', 'value2' ], $subject->value);
        $this->assertSame('["value1","value2"]', $subject->rawValue);
    }

    /**
     * Test that getAndInspect() returns an InputGuard with set subject for an available required input value.
     *
     * @covers Lunr\Flare\InputGuard::getAndInspect
     */
    public function testGetAndInspectReturnsInputGuardWithSetSubjectForAvailableRequiredArrayValueFromCli(): void
    {
        $this->request->expects($this->once())
                      ->method('getData')
                      ->with('key', RequestData::CliArgument)
                      ->willReturn([ 'value1', 'value2' ]);

        $object = $this->class->getAndInspect('key', RequestData::CliArgument);

        $this->assertInstanceOf(InputGuard::class, $object);

        $property = $this->getReflectionProperty('subject');

        $subject = $property->getValue($object);

        $this->assertInstanceOf(InputValue::class, $subject);
        $this->assertSame('key', $subject->name);
        $this->assertSame([ 'value1', 'value2' ], $subject->value);
        $this->assertSame('["value1","value2"]', $subject->rawValue);
    }

    /**
     * Test that getAndInspect() returns an InputGuard with set subject for an available required input value.
     *
     * @covers Lunr\Flare\InputGuard::getAndInspect
     */
    public function testGetAndInspectReturnsInputGuardWithSetSubjectForAvailableRequiredArrayValueFromUpload(): void
    {
        $this->request->expects($this->once())
                      ->method('getData')
                      ->with('userfile', RequestData::Upload)
                      ->willReturn([ 'name' => 'file.log' ]);

        $object = $this->class->getAndInspect('userfile', RequestData::Upload);

        $this->assertInstanceOf(InputGuard::class, $object);

        $property = $this->getReflectionProperty('subject');

        $subject = $property->getValue($object);

        $this->assertInstanceOf(InputValue::class, $subject);
        $this->assertSame('userfile', $subject->name);
        $this->assertSame([ 'name' => 'file.log' ], $subject->value);
        $this->assertSame('file.log', $subject->rawValue);
    }

    /**
     * Test that getAndInspect() returns an InputGuard with set subject for an available required input value.
     *
     * @covers Lunr\Flare\InputGuard::getAndInspect
     */
    public function testGetAndInspectReturnsInputGuardWithSetSubjectForAvailableRequiredArrayValueFromMultiUpload(): void
    {
        $this->request->expects($this->once())
                      ->method('getData')
                      ->with('userfile', RequestData::Upload)
                      ->willReturn([ 'name' => [ 'one' => 'file.log', 'two' => 'file.txt' ] ]);

        $object = $this->class->getAndInspect('userfile', RequestData::Upload);

        $this->assertInstanceOf(InputGuard::class, $object);

        $property = $this->getReflectionProperty('subject');

        $subject = $property->getValue($object);

        $this->assertInstanceOf(InputValue::class, $subject);
        $this->assertSame('userfile', $subject->name);
        $this->assertSame([ 'name' => [ 'one' => 'file.log', 'two' => 'file.txt' ] ], $subject->value);
        $this->assertSame('file.log', $subject->rawValue);
    }

    /**
     * Test that getAndInspect() returns an InputGuard with set subject for an available required input value.
     *
     * @covers Lunr\Flare\InputGuard::getAndInspect
     */
    public function testGetAndInspectReturnsInputGuardWithSetSubjectForUnavailableRequiredArrayValueFromUpload(): void
    {
        $this->request->expects($this->once())
                      ->method('getData')
                      ->with('userfile', RequestData::Upload)
                      ->willReturn(NULL);

        $object = $this->class->getAndInspect('userfile', RequestData::Upload);

        $this->assertInstanceOf(InputGuard::class, $object);

        $property = $this->getReflectionProperty('subject');

        $subject = $property->getValue($object);

        $this->assertInstanceOf(InputValue::class, $subject);
        $this->assertSame('userfile', $subject->name);
        $this->assertNull($subject->value);
        $this->assertNull($subject->rawValue);
    }

    /**
     * Test that getAndInspect() returns an InputGuard with set subject for an unavailable required input value.
     *
     * @covers Lunr\Flare\InputGuard::getAndInspect
     */
    public function testGetAndInspectReturnsInputGuardWithSetSubjectForUnavailableRequiredValue(): void
    {
        $this->request->expects($this->once())
                      ->method('getData')
                      ->with('key', RequestData::Post)
                      ->willReturn(NULL);

        $object = $this->class->getAndInspect('key', RequestData::Post);

        $this->assertInstanceOf(InputGuard::class, $object);

        $property = $this->getReflectionProperty('subject');

        $subject = $property->getValue($object);

        $this->assertInstanceOf(InputValue::class, $subject);
        $this->assertSame('key', $subject->name);
        $this->assertNull($subject->value);
    }

    /**
     * Test that getAndInspect() returns an InputGuard with set subject for an available optional input value.
     *
     * @covers Lunr\Flare\InputGuard::getAndInspect
     */
    public function testGetAndInspectReturnsInputGuardWithSetSubjectForAvailableOptionalValue(): void
    {
        $this->request->expects($this->once())
                      ->method('getData')
                      ->with('key', RequestData::Post)
                      ->willReturn('value1');

        $object = $this->class->getAndInspect('key', RequestData::Post, optional: TRUE);

        $this->assertInstanceOf(InputGuard::class, $object);

        $property = $this->getReflectionProperty('subject');

        $subject = $property->getValue($object);

        $this->assertInstanceOf(InputValue::class, $subject);
        $this->assertSame('key', $subject->name);
        $this->assertSame('value1', $subject->value);
        $this->assertSame('value1', $subject->rawValue);
    }

    /**
     * Test that getAndInspect() returns NULL for an unavailable optional input value.
     *
     * @covers Lunr\Flare\InputGuard::getAndInspect
     */
    public function testGetAndInspectReturnsNullForUnavailableOptionalValue(): void
    {
        $this->request->expects($this->once())
                      ->method('getData')
                      ->with('key', RequestData::Post)
                      ->willReturn(NULL);

        $object = $this->class->getAndInspect('key', RequestData::Post, optional: TRUE);

        $this->assertNull($object);
    }

}

?>
