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
		// Register the AlertsMessageBag class.
		$this->app->bindShared('alerts', function($app)
		{
			return new AlertManager($app['config'], $app['session.store'], new MessageBag, $app['translator']);
		});

		$this->app->bind('Hampel\Alerts\AlertManager', function($app)
		{
			return $app['alerts'];
		});

		// Register the AlertComposer class.
		$this->app->bind('Hampel\Alerts\Composers\AlertComposer', function($app)
		{
			return new AlertComposer($app['config'], $app['session.store'], $app['view']);
		});
	}

	public function boot()
	{
		$this->defineConfiguration();
		$this->defineViews();

		$view_name = $this->app['config']->get('alerts.base_view');

		$this->app['view']->composer($view_name, 'Hampel\Alerts\Composers\AlertComposer');
	}

	protected function defineConfiguration()
	{
		$this->publishes([
			__DIR__ . '/config/alerts.php' => config_path('alerts.php'),
		], 'config');

		$this->mergeConfigFrom(
			__DIR__ . '/config/alerts.php', 'alerts'
		);
	}

	protected function defineViews()
	{
		$this->publishes([
			__DIR__ . '/views' => base_path('resources/views/vendor/alerts'),
		], 'views');

		$this->loadViewsFrom(__DIR__ . '/views', 'alerts');
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