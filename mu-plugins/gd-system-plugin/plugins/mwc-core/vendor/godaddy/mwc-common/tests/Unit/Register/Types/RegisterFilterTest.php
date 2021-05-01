<?php

namespace GoDaddy\WordPress\MWC\Common\Tests\Unit\Register\Types;

use Closure;
use Exception;
use GoDaddy\WordPress\MWC\Common\Register\Types\RegisterFilter;
use GoDaddy\WordPress\MWC\Common\Tests\WPTestCase;
use WP_Mock;

/**
 * @covers \GoDaddy\WordPress\MWC\Common\Register\Types\RegisterFilter
 */
final class RegisterFilterTest extends WPTestCase
{
    /** @var Closure used for testing handlers */
    private $handler;

    /**
     * Gets a closure.
     *
     * @return Closure
     */
    private function getHandler() : Closure
    {
        if (null === $this->handler) {
            $this->handler = static function () {
                return true;
            };
        }

        return $this->handler;
    }

    /**
     * Tests that filters can be deregistered.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Register\Types\RegisterFilter::deregister()
     *
     * @throws Exception
     */
    public function testCanDeregister()
    {
        // TODO: Use WP_Mock::expectFilterNotAdded when WP_Mock supports the remove_filter method {AC 2020-12-28}
        WP_Mock::userFunction('remove_filter')
            ->withArgs(['test', $this->getHandler(), 11])
            ->once();

        // all clear
        WP_Mock::expectFilterAdded('test', $this->getHandler(), 11, 2);
        $register = (new RegisterFilter())
            ->setGroup('test')
            ->setHandler($this->getHandler())
            ->setArgumentsCount(2)
            ->setPriority(11);

        $register->execute();
        $register->deregister();

        $this->assertConditionsMet();
    }

    /**
     * Tests that it can execute an action callback.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Register\Types\RegisterFilter::execute()
     */
    public function testCanExecute()
    {
        // there is no handler attached
        $this->expectException(Exception::class);
        (new RegisterFilter())->execute();

        // has a handler, but is invalid
        $this->expectException(Exception::class);
        (new RegisterFilter())->setHandler('')->execute();

        // only has a handler, no associated group name
        $this->expectException(Exception::class);
        (new RegisterFilter())->setHandler($this->getHandler())->execute();

        // has a handler and a group name, but has a condition to not apply
        WP_Mock::expectActionNotAdded('test', $this->getHandler());
        (new RegisterFilter())
            ->setGroup('test')
            ->setHandler($this->getHandler())
            ->setArgumentsCount(10)
            ->setPriority(1)
            ->setCondition(function () {
                return false;
            })
            ->execute();

        // all clear
        WP_Mock::expectActionAdded('test', $this->getHandler(), 11, 2);
        (new RegisterFilter())
            ->setGroup('test')
            ->setHandler($this->getHandler())
            ->setArgumentsCount(10)
            ->setPriority(1)
            ->execute();
    }

    /**
     * Tests that it can validate an action handler.
     *
     * @covers \GoDaddy\WordPress\MWC\Common\Register\Types\RegisterFilter::validate()
     */
    public function testCanValidate()
    {
        // no handler
        $this->expectException(Exception::class);
        (new RegisterFilter())->validate();

        // invalid handler
        $this->expectException(Exception::class);
        (new RegisterFilter())->setHandler('')->validate();

        // valid handler
        (new RegisterFilter())->setHandler($this->getHandler())->validate();
    }
}
