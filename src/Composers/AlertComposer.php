<?php namespace Hampel\Alerts\Composers;
/**
 * 
 */

use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use Illuminate\Session\SessionInterface;
use Illuminate\Contracts\Config\Repository;

class AlertComposer
{
	/**
	 * The application configuration repository
	 *
	 * @var \Illuminate\Contracts\Config\Repository
	 */
	protected $config;

	/**
	 * @var \Illuminate\Session\SessionInterface
	 */
	protected $session;

	/**
	 * @var \Illuminate\Contracts\View\Factory
	 */
	protected $factory;

	function __construct(Repository $config, SessionInterface $session, Factory $factory)
	{
		$this->config = $config;
		$this->session = $session;
		$this->factory = $factory;
	}

	public function compose(View $view)
	{
		$session_key = $this->getSessionKey();
		$variable_name = $this->getViewVariable();

		if ($this->session->has($session_key))
		{
			$messages = $this->session->get($session_key);

			if (is_array($messages))
			{
				$view->with($variable_name, $this->renderView($messages));
			}
		}
		else
		{
			$view->with($variable_name, '');
		}
	}

	protected function renderView($messages)
	{
		$template = $this->getAlertTemplate();
		$level_map = $this->getLevelMap();

		$view_output = "";

		foreach ($messages as $type => $message)
		{
			if (in_array($type, array_keys($level_map)))
			{
				$alert_type = $level_map[$type];

				foreach ($message as $alert_text)
				{
					$view_output .= $this->factory->make($template, compact('alert_type', 'alert_text'))->render();
				}
			}
		}

		return $view_output;
	}

	protected function getSessionKey()
	{
		return $this->config->get('alerts.session_key');
	}

	protected function getViewVariable()
	{
		return $this->config->get('alerts.view_variable');
	}

	protected function getLevelMap()
	{
		return $this->config->get('alerts.level_map');
	}

	protected function getAlertTemplate()
	{
		return $this->config->get('alerts.alert_template');
	}

}

?>
 