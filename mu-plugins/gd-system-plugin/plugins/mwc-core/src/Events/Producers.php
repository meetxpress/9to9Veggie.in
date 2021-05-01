<?php


namespace GoDaddy\WordPress\MWC\Core\Events;

use GoDaddy\WordPress\MWC\Common\Configuration\Configuration;
use GoDaddy\WordPress\MWC\Common\Events\Contracts\ProducerContract;

/**
 * Producers.
 *
 * @since x.y.z
 */
class Producers
{
	/**
	 * Class constructor.
	 */
	public function __construct()
	{
		foreach(Configuration::get('events.producers', []) as $className) {
			if (is_a($className, ProducerContract::class, true)) {
				(new $className())->setup();
			}
		}

	}
}
