<?php

namespace AsyncInterop\Loop\Test;

use AsyncInterop\Loop;

class LoopTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException \RuntimeException
     * @expectedExceptionMessage new factory while running isn't allowed
     */
    public function setFactoryFailsIfRunning() {
        $driver = new DummyDriver;

        $factory = $this->getMockBuilder(Loop\DriverFactory::class)->getMock();
        $factory->method("create")->willReturn($driver);

        Loop::setFactory($factory);

        Loop::execute(function () use ($factory) {
            Loop::setFactory($factory);
        });
    }

    /** @test */
    public function executeStackReturnsScopedDriver() {
        $driver1 = new DummyDriver;
        $driver2 = new DummyDriver;

        Loop::execute(function () use ($driver1, $driver2) {
            $this->assertSame($driver1, Loop::get());

            Loop::execute(function () use ($driver2) {
                $this->assertSame($driver2, Loop::get());
            }, $driver2);

            $this->assertSame($driver1, Loop::get());
        }, $driver1);
    }
}
