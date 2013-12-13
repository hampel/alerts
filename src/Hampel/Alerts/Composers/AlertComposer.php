<?php namespace Hampel\Alerts\Composers;
/**
 * 
 */

use Illuminate\Container\Container;
use Illuminate\Session\Store;
use Illuminate\Config\Repository;
use Illuminate\View\View;

class AlertComposer
{
	/**
	 * The application container instance.
	 *
	 * @var \Illuminate\Container\Container
	 */
	protected $app;

	/**
	 * Initialize the AlertComposer class.
	 *
	 * @param  \Illuminate\Container\Container  $app
	 */
	public function __construct(Container $app)
	{
		$this->app = $app;
	}

	public function compose(View $view)
	{
		$alerts = "";
		$session_key = $this->app['config']->get('alerts::session_key');
		$variable_name = $this->app['config']->get('alerts::view_variable');

		if ($this->app['session.store']->has($session_key))
		{

			$messages = $this->app['session.store']->get($session_key);

			if (is_array($messages))
			{
				$bag = $this->app->make('alerts');
				$bag->merge($messages);
				$alerts = $bag->renderView();
			}
		}

		$view->with($variable_name, $alerts);
	}

}

?>
 