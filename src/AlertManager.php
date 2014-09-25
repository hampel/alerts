<?php namespace Hampel\Alerts;

use BadMethodCallException;
use BadFunctionCallException;
use Illuminate\Session\SessionInterface;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Support\MessageBag;
use Symfony\Component\Translation\TranslatorInterface;

class AlertManager 
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
	 * @var \Illuminate\Contracts\Support\MessageBag
	 */
	protected $alerts;

	/**
	 * @var \Symfony\Component\Translation\TranslatorInterface
	 */
	protected $translator;


	function __construct(Repository $config, SessionInterface $session, MessageBag $alerts, TranslatorInterface $translator)
	{
		$this->config = $config;
		$this->session = $session;
		$this->alerts = $alerts;
		$this->translator = $translator;
	}

	/**
	 * Store the messages in the current session.
	 */
	public function flash()
	{
		$this->session->flash($this->getSessionKey(), $this->alerts->getMessages());

		return $this->alerts;
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
			$messages = $this->arrayify($args[0]);
			if (empty($messages)) throw new BadFunctionCallException("Missing parameter to method {$method}");

			foreach ($messages as $message)
			{
				$this->alerts->add($method, $this->translateMessage($message, isset($args[1]) ? $this->arrayify($args[1]) : []));
			}

			return $this;
		}

		throw new BadMethodCallException("Method {$method} does not exist.");
	}

	protected function arrayify($var)
	{
		if (!isset($var)) return [];

		return is_array($var) ? $var : [$var];
	}

	protected function translateMessage($message, array $replacements = [])
	{
		// if a language entry for this message exists, translate it, otherwise just return the message
		return $this->translator->get($message, $replacements);
	}

	/**
	 * Returns the alert levels from the config.
	 *
	 * @return array
	 */
	public function getLevels()
	{
		return array_keys($this->config->get('alerts::level_map'));
	}

	/**
	 * Returns the session key from the config.
	 *
	 * @return string
	 */
	public function getSessionKey()
	{
		return $this->config->get('alerts::session_key');
	}
}

?>
 