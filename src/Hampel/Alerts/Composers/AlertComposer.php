<?php namespace Hampel\Alerts\Composers;
/**
 * 
 */

use Illuminate\Session\Store;
use Illuminate\Config\Repository;
use Hampel\Alerts\AlertMessageBag;

class AlertComposer
{
	/**
	 * Illuminate's Session Store.
	 *
	 * @var \Illuminate\Session\Store
	 */
	protected $session;

	/**
	 * Illuminate's Config Repository.
	 *
	 * @var \Illuminate\Config\Repository
	 */
	protected $config;

	/**
	 * Initialize the AlertMessageBag class.
	 *
	 * @param  \Illuminate\Session\Store  $session
	 * @param  \Illuminate\Config\Repository $config
	 */
	public function __construct(Store $session, Repository $config)
	{
		$this->config = $config;
		$this->session = $session;
	}

	public function compose($view)
	{
		$alerts = "";
		$session_key = $this->config->get('alerts::session_key');
		$variable_name = $this->config->get('alerts::view_variable');

		if ($this->session->has($session_key))
		{
			$messages = $this->session->get($session_key);

			if ($messages instanceof AlertMessageBag)
			{
				$alerts = $messages->renderView();
			}
		}

		$view->with($variable_name, $alerts);
	}

}

?>
 