<?php namespace Hampel\Alerts;

use Illuminate\Support\MessageBag;
use Illuminate\Support\ServiceProvider;
use Hampel\Alerts\Composers\AlertComposer;

class AlertServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->package('hampel/alerts', 'alerts', __DIR__);

		$view_name = $this->app['config']->get('alerts::base_view');

		$this->app['view']->composer($view_name, 'Hampel\Alerts\Composers\AlertComposer');

		// Register the AlertsMessageBag class.
		$this->app->bindShared('alerts', function($app)
		{
			return new AlertManager($app['config'], $app['session.store'], new MessageBag, $app['translator']);
		});

		// Register the AlertComposer class.
		$this->app->bind('Hampel\Alerts\Composers\AlertComposer', function($app)
		{
			return new AlertComposer($app['config'], $app['session.store'], $app['view']);
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('alerts');
	}

}