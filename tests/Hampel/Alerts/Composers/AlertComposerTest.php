<?php namespace Hampel\Alerts\Composers;

use Illuminate\View\View;
use Illuminate\View\Factory;
use Mockery;

class AlertComposerTest extends \PHPUnit_Framework_TestCase
{
	public function testViewComposer()
	{
		$messagebag = Mockery::mock('Hampel\Alerts\AlertMessageBag');
		$messagebag->shouldReceive('merge')->once()->with(array('foo' => array('bar')));
		$messagebag->shouldReceive('renderView')->once()->andReturn('rendered alert');

		$session = Mockery::mock('Illuminate\Session\Store');
		$session->shouldReceive('has')->once()->with('alert_messages')->andReturn(true);
		$session->shouldReceive('get')->once()->with('alert_messages')->andReturn(array('foo' => array('bar')));

		$config = Mockery::mock('Illuminate\Config\Repository');
		$config->shouldReceive('get')->once()->with('alerts::session_key')->andReturn('alert_messages');
		$config->shouldReceive('get')->once()->with('alerts::view_variable')->andReturn('alerts');

		$container = Mockery::mock('Illuminate\Container\Container');
		$container->shouldReceive('offsetGet')->twice()->with('session.store')->andReturn($session);
		$container->shouldReceive('offsetGet')->twice()->with('config')->andReturn($config);

		$env = $this->getFactory();
		$env->getDispatcher()->shouldReceive('listen')->once()->with('composing: foo', Mockery::type('Closure'));
		$env->setContainer($container);

		$container->shouldReceive('make')->once()->with('Hampel\Alerts\Composers\AlertComposer')->andReturn(
			new AlertComposer($container)
		);

		$container->shouldReceive('make')->once()->with('alerts')->andReturn($messagebag);

		$view = $this->getView();

		$callback = $env->composer('foo', 'Hampel\Alerts\Composers\AlertComposer');
		$callback = $callback[0];

		$callback($view);

		$viewdata = $view->getData();

		$this->assertArrayHasKey('alerts', $viewdata);
		$this->assertEquals('rendered alert', $viewdata['alerts']);
	}

	protected function getView()
	{
		return new View(
			Mockery::mock('Illuminate\View\Factory'),
			Mockery::mock('Illuminate\View\Engines\EngineInterface'),
			'view',
			'path',
			array('foo' => 'bar')
		);
	}


	protected function getFactory()
	{
		return new Factory(
			Mockery::mock('Illuminate\View\Engines\EngineResolver'),
			Mockery::mock('Illuminate\View\ViewFinderInterface'),
			Mockery::mock('Illuminate\Events\Dispatcher')
		);
	}

	public function tearDown() {
		Mockery::close();
	}
}
