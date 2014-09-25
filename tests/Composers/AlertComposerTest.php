<?php namespace Hampel\Alerts\Composers;

use Mockery;
use Illuminate\View\View;
use Illuminate\View\Factory;

class AlertComposerTest extends \PHPUnit_Framework_TestCase
{
	public function testViewComposer()
	{
		$resolver = Mockery::mock('Illuminate\View\Engines\EngineResolver');
		$finder = Mockery::mock('Illuminate\View\ViewFinderInterface');
		$events = Mockery::mock('Illuminate\Contracts\Events\Dispatcher');

		$view_factory = new Factory($resolver, $finder, $events);

		$container = Mockery::mock('Illuminate\Container\Container');
		$view_factory->setContainer($container);

		$config = Mockery::mock('Illuminate\Contracts\Config\Repository');
		$session = Mockery::mock('Illuminate\Session\SessionInterface');

		$factory = Mockery::mock('Illuminate\Contracts\View\Factory');
		$subview = Mockery::mock('Illuminate\View\View');

		$composer = new AlertComposer($config, $session, $factory);

		$engine = Mockery::mock('Illuminate\View\Engines\EngineInterface');

		$events->shouldReceive('listen')->once()->with('composing: foo', Mockery::type('Closure'));
		$container->shouldReceive('make')->once()->with('Hampel\Alerts\Composers\AlertComposer')->andReturn($composer);
		$config->shouldReceive('get')->once()->with('alerts::session_key')->andReturn('alert_messages');
		$config->shouldReceive('get')->once()->with('alerts::view_variable')->andReturn('alerts');
		$session->shouldReceive('has')->once()->with('alert_messages')->andReturn(true);
		$session->shouldReceive('get')->once()->with('alert_messages')->andReturn(array('success' => ['foo bar']));
		$config->shouldReceive('get')->once()->with('alerts::alert_template')->andReturn('alert_template');
		$config->shouldReceive('get')->once()->with('alerts::level_map')->andReturn(['error' => 'alert', 'success' => 'success']);
		$factory->shouldReceive('make')->once()->with('alert_template', Mockery::on(function($data) {
			$this->assertArrayHasKey('alert_type', $data);
			$this->assertEquals('success', $data['alert_type']);
			$this->assertArrayHasKey('alert_text', $data);
			$this->assertEquals('foo bar', $data['alert_text']);
			return true;
		}))->andReturn($subview);
		$subview->shouldReceive('render')->once()->with()->andReturn('rendered alert');

		$view = new View($view_factory, $engine, 'view', 'path', ['foo' => 'bar']);

		$callback = $view_factory->composer('foo', 'Hampel\Alerts\Composers\AlertComposer');
		$callback = $callback[0];

		$callback($view);

		$viewdata = $view->getData();

		$this->assertArrayHasKey('alerts', $viewdata);
		$this->assertEquals('rendered alert', $viewdata['alerts']);
	}

	public function tearDown() {
		Mockery::close();
	}
}

?>
