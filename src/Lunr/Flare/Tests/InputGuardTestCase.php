<?php

/**
 * This file contains the InputGuardTestCase class.
 *
 * SPDX-FileCopyrightText: Copyright 2025 Framna Netherlands B.V., Zwolle, The Netherlands
 * SPDX-License-Identifier: MIT
 */

namespace Lunr\Flare\Tests;

use Aura\Filter\FilterFactory;
use Aura\Filter\ValueFilter;
use Lunr\Corona\Request;
use Lunr\Flare\InputGuard;
use Lunr\Flare\InputValue;
use Lunr\Halo\LunrBaseTestCase;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery\MockInterface;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * This class contains common setup routines, providers
 * and shared attributes for testing the InputGuard class.
 *
 * @covers Lunr\Flare\InputGuard
 */
abstract class InputGuardTestCase extends LunrBaseTestCase
{

    use MockeryPHPUnitIntegration;

    /**
     * Mock instance of the Request class.
     * @var Request&MockObject
     */
    protected Request&MockObject $request;

    /**
     * Mock instance of the Filter factory class.
     * @var FilterFactory&MockInterface
     */
    protected FilterFactory&MockInterface $filterFactory;

    /**
     * Mock instance of the ValueFilter class.
     * @var ValueFilter&MockInterface
     */
    protected ValueFilter&MockInterface $valueFilter;

    /**
     * Instance of an InputValue class.
     * @var InputValue
     */
    protected InputValue $subject;

    /**
     * Instance of the tested class.
     * @var InputGuard
     */
    protected InputGuard $class;

    /**
     * Testcase Constructor.
     */
    public function setUp(): void
    {
        $this->setUpWithoutClass();

        $this->class = new InputGuard($this->request, $this->filterFactory);

        parent::baseSetUp($this->class);
    }

    /**
     * Testcase Constructor.
     *
     * @return void
     */
    public function setUpWithoutClass(): void
    {
        $this->request = $this->getMockBuilder(Request::class)
                              ->disableOriginalConstructor()
                              ->getMock();

        $this->filterFactory = Mockery::mock(FilterFactory::class);
        $this->valueFilter   = Mockery::mock(ValueFilter::class);

        $this->filterFactory->shouldReceive('newValueFilter')
                            ->atLeast()
                            ->once()
                            ->andReturn($this->valueFilter);
    }

    /**
     * Testcase Destructor.
     */
    public function tearDown(): void
    {
        parent::tearDown();

        unset($this->request);
        unset($this->filterFactory);
        unset($this->valueFilter);
        unset($this->subject);
        unset($this->class);
    }

}

?>
