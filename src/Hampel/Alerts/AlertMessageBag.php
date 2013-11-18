<?php namespace Hampel\Alerts;

use Config, Lang, Session, View;
use BadMethodCallException;
use BadFunctionCallException;
use Illuminate\Support\MessageBag;
use Illuminate\Session\Store;
use Illuminate\Config\Repository;

class AlertMessageBag extends MessageBag {

	/**
	 * Store the messages in the current session.
	 */
	public function flash()
	{
		Session::flash($this->getSessionKey(), $this);

		return $this;
	}

	/**
	 * Returns the alert levels from the config.
	 *
	 * @return array
	 */
	protected function getLevels()
	{
		return (array) Config::get('alerts::levels');
	}

	/**
	 * Returns the session key from the config.
	 *
	 * @return string
	 */
	public function getSessionKey()
	{
		return Config::get('alerts::session_key');
	}

	public function renderView()
	{
		$view = "";

		foreach ($this->getMessages() as $type => $messages)
		{
			if (in_array($type, $this->getLevels()))
			{
				foreach ($messages as $message)
				{
					$alert_type = $type;
					$alert_text = $message;

					$view .= View::make(Config::get('alerts::alert_template'), compact('alert_type', 'alert_text'))->render();
				}
			}
		}

		return $view;
	}

	protected function translateMessage($message, $replacements)
	{
		if (Lang::has($message))
		{
			// if there is a language entry which matches this message, use that instead

			if (isset($replacements) AND is_array($replacements))
			{
				// there are replacements specified
				$message = Lang::get($message, $replacements);
			}
			else
			{
				// no replacement, just a plain language entry
				$message = Lang::get($message);
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