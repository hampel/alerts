<?php namespace Hampel\Alerts;

use View;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ServiceProvider;
use Hampel\Alerts\Composers\AlertComposer;

class AlertServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		// Register the AlertsMessageBag class.
		$this->app->singleton(AlertManager::class, function($app)
		{
			return new AlertManager($app['config'], $app['session.store'], new MessageBag, $app['translator']);
		});

//		// Register the AlertComposer class.
//		$this->app->bind(AlertComposer::class, function($app)
//		{
//			return new AlertComposer($app['config'], $app['session.store'], $app['view']);
//		});
	}

	public function boot()
	{
		$this->defineConfiguration();
		$this->defineViews();
		$this->defineViewComposer();
	}

	protected function defineConfiguration()
	{
		$this->publishes([
            __DIR__ . '/../config/alerts.php' => config_path('alerts.php'),
		], 'config');

		$this->mergeConfigFrom(
			__DIR__ . '/../config/alerts.php', 'alerts'
		);
	}

	protected function defineViews()
	{
		$this->publishes([
			__DIR__ . '/../views' => resource_path('views/vendor/alerts'),
		], 'views');

		$this->loadViewsFrom(__DIR__ . '/../views', 'alerts');
	}

	protected function defineViewComposer()
	{
		View::composer(config('alerts.base_view'), AlertComposer::class);
	}
}
