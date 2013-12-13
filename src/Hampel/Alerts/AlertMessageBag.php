<?php namespace Hampel\Alerts;

use BadMethodCallException;
use BadFunctionCallException;
use Illuminate\Support\MessageBag;
use Illuminate\Container\Container;

class AlertMessageBag extends MessageBag {

	/**
	 * The application container instance.
	 *
	 * @var \Illuminate\Container\Container
	 */
	protected $app;

	/**
	 * Set the IoC container instance.
	 *
	 * @param  \Illuminate\Container\Container $app
	 * @return void
	 */
	public function setContainer(Container $app)
	{
		$this->app = $app;

		return $this;
	}

	/**
	 * Store the messages in the current session.
	 */
	public function flash()
	{
		if (isset($this->app))
		{
			$this->app['session.store']->flash($this->getSessionKey(), $this->getMessages());
		}

		return $this;
	}

	/**
	 * Returns the alert levels from the config.
	 *
	 * @return array
	 */
	protected function getLevels()
	{
		if (isset($this->app))
		{
			return array_keys($this->app['config']->get('alerts::level_map'));
		}

		return array();
	}
	/**
	 * Returns the session key from the config.
	 *
	 * @return string
	 */
	public function getSessionKey()
	{
		if (isset($this->app))
		{
			return $this->app['config']->get('alerts::session_key');
		}

		return "";
	}

	public function renderView()
	{
		$view = "";

		if (isset($this->app))
		{
			$level_map = $this->app['config']->get('alerts::level_map');

			foreach ($this->getMessages() as $type => $messages)
			{
				if (in_array($type, array_keys($level_map)))
				{
					foreach ($messages as $message)
					{
						$alert_type = $level_map[$type];
						$alert_text = $message;

						$view .= $this->app['view']->make(
							$this->app['config']->get('alerts::alert_template'),
							compact('alert_type', 'alert_text')
						)->render();
					}
				}
			}

			return $view;
		}

		return null;
	}


	protected function translateMessage($message, $replacements)
	{
		if (isset($this->app))
		{

			if ($this->app['translator']->has($message))
			{
				// if there is a language entry which matches this message, use that instead

				if (isset($replacements) AND is_array($replacements))
				{
					// there are replacements specified
					$message = $this->app['translator']->get($message, $replacements);
				}
				else
				{
					// no replacement, just a plain language entry
					$message = $this->app['translator']->get($message);
				}
			}
		}

		return $message;
	}

	/**
	 * Dynamically handle alert additions.
	 *
	 * @param  string  $method
	 * @param  array   $args
	 * @return mixed
	 * @throws BadMethodCallException
	 * @throws BadFunctionCallException
	 */
	public function __call($method, $args)
	{
		// Check if the method is in the allowed alert levels array.
		if (in_array($method, $this->getLevels()))
		{
			if (isset($args[0]))
			{
				$messages = $args[0];
				if (!is_array($messages))
				{
					$messages = array($messages);
				}

				foreach ($messages as $message)
				{
					$message = $this->translateMessage($message, isset($args[1]) ? $args[1] : null);

					$this->add($method, $message);
				}

				return $this;
			}

			throw new BadFunctionCallException("Missing parameter to method {$method}");

		}

		throw new BadMethodCallException("Method {$method} does not exist.");
	}

}