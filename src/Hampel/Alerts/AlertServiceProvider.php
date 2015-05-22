<?php namespace Hampel\Alerts;

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

	}

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('hampel/alerts');

		$this->app['view']->addNamespace('alerts', __DIR__ . '/../../views');

		$view_name = $this->app['config']->get('alerts::base_view');

		$this->app['view']->composer($view_name, 'Hampel\Alerts\Composers\AlertComposer');

		// Register the AlertsMessageBag class.
		$this->app->bind('alerts', function($app)
		{
			$messagebag = new AlertMessageBag;
			$messagebag->setContainer($app);
			return $messagebag;
		});

		// Register the AlertComposer class.
		$this->app->bind('Hampel\Alerts\Composers\AlertComposer', function($app)
		{
			return new AlertComposer($app);
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
