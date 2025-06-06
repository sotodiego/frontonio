<?php
namespace WP_Rocket\Engine\Preload\Links;

use WP_Rocket\Dependencies\League\Container\ServiceProvider\AbstractServiceProvider;

/**
 * Service provider for WP Rocket preload links.
 */
class ServiceProvider extends AbstractServiceProvider {
	/**
	 * The provides array is a way to let the container
	 * know that a service is provided by this service
	 * provider. Every service that is registered via
	 * this service provider must have an alias added
	 * to this array or it will be ignored.
	 *
	 * @var array
	 */
	protected $provides = [
		'preload_links_admin_subscriber',
		'preload_links_subscriber',
	];

	/**
	 * Registers the subscribers in the container
	 *
	 * @return void
	 */
	public function register() {
		$options = $this->getContainer()->get( 'options' );

		$this->getContainer()->share( 'preload_links_admin_subscriber', AdminSubscriber::class )
			->addArgument( $options )
			->addTag( 'common_subscriber' );
		$this->getContainer()->share( 'preload_links_subscriber', Subscriber::class )
			->addArgument( $options )
			->addArgument( rocket_direct_filesystem() )
			->addTag( 'common_subscriber' );
	}
}
