<?php

namespace GoDaddy\WordPress\MWC\Core\Tests\Unit\WooCommerce;

use GoDaddy\WordPress\MWC\Core\Tests\WPTestCase;
use GoDaddy\WordPress\MWC\Core\WooCommerce\NewWooCommerceObjectFlag;
use ReflectionClass;

/**
 * Provides tests for the NewWooCommerceObjectFlag class.
 *
 * @covers \GoDaddy\WordPress\MWC\Core\WooCommerce\NewWooCommerceObjectFlag
 */
final class NewWooCommerceObjectFlagTest extends WPTestCase
{
	/**
	 * @covers \GoDaddy\WordPress\MWC\Core\WooCommerce\NewWooCommerceObjectFlag::__construct()
	 */
	public function testCanInstantiate()
	{
		$instance = new NewWooCommerceObjectFlag('test');

		$reflection = new ReflectionClass($instance);

		$metaKeyProperty = $reflection->getProperty('metaKey');
		$metaKeyProperty->setAccessible(true);

		$metaValueProperty = $reflection->getProperty('metaValue');
		$metaValueProperty->setAccessible(true);

		$this->assertNotNull($instance);
		$this->assertEquals('_gd_mwc_is_new_object', $metaKeyProperty->getValue($instance));
		$this->assertEquals('no', $metaValueProperty->getValue($instance));
	}

	/**
	 * @covers \GoDaddy\WordPress\MWC\Core\WooCommerce\NewWooCommerceObjectFlag::isOn()
	 */
	public function testCanCheckIsOn()
	{
		$instance = new NewWooCommerceObjectFlag('test');

		$this->assertFalse($instance->isOn());

		$instance->turnOn();

		$this->assertTrue($instance->isOn());
	}

	/**
	 * @covers \GoDaddy\WordPress\MWC\Core\WooCommerce\NewWooCommerceObjectFlag::isOff()
	 */
	public function testCanCheckIsOff()
	{
		$instance = new NewWooCommerceObjectFlag('test');

		$instance->turnOn();

		$this->assertFalse($instance->isOff());

		$instance->turnOff();

		$this->assertTrue($instance->isOff());
	}

	/**
	 * @covers \GoDaddy\WordPress\MWC\Core\WooCommerce\NewWooCommerceObjectFlag::turnOn()
	 */
	public function testCanTurnOn()
	{
		$instance = new NewWooCommerceObjectFlag('test');

		$returnedInstance = $instance->turnOn();

		$this->assertSame($instance, $returnedInstance);
		$this->assertTrue($returnedInstance->isOn());
	}

	/**
	 * @covers \GoDaddy\WordPress\MWC\Core\WooCommerce\NewWooCommerceObjectFlag::turnOff()
	 */
	public function testCanTurnOff()
	{
		$instance = new NewWooCommerceObjectFlag('test');

		$returnedInstance = $instance->turnOff();

		$this->assertSame($instance, $returnedInstance);
		$this->assertTrue($returnedInstance->isOff());
	}
}
