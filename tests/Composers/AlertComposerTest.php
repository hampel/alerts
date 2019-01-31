<?php namespace Hampel\Alerts\Composers;

use Mockery;
use Illuminate\View\View;
use Illuminate\View\Factory;
use PHPUnit\Framework\TestCase;
use Illuminate\Container\Container;
use Illuminate\Contracts\View\Engine;
use Illuminate\View\ViewFinderInterface;
use Illuminate\Contracts\Session\Session;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\View\Engines\EngineResolver;

class AlertComposerTest extends TestCase
{
	use \Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

	public function testViewComposer()
	{
		$resolver = Mockery::mock(EngineResolver::class);
		$finder = Mockery::mock(ViewFinderInterface::class);
		$events = Mockery::mock(Dispatcher::class);

		$view_factory = new Factory($resolver, $finder, $events);

		$container = Mockery::mock(Container::class);
		$view_factory->setContainer($container);

		$config = Mockery::mock(Repository::class);
		$session = Mockery::mock(Session::class);

		$factory = Mockery::mock(\Illuminate\Contracts\View\Factory::class);
		$subview = Mockery::mock(View::class);

		$composer = new AlertComposer($config, $session, $factory);

		$engine = Mockery::mock(Engine::class);

		$events->shouldReceive('listen')->once()->with('composing: foo', Mockery::type('Closure'));
		$container->shouldReceive('make')->once()->with(AlertComposer::class)->andReturn($composer);
		$config->shouldReceive('get')->once()->with('alerts.session_key')->andReturn('alert_messages');
		$config->shouldReceive('get')->once()->with('alerts.view_variable')->andReturn('alerts');
		$session->shouldReceive('has')->once()->with('alert_messages')->andReturn(true);
		$session->shouldReceive('get')->once()->with('alert_messages')->andReturn(array('success' => ['foo bar']));
		$config->shouldReceive('get')->once()->with('alerts.alert_template')->andReturn('alert_template');
		$config->shouldReceive('get')->once()->with('alerts.level_map')->andReturn(['error' => 'alert', 'success' => 'success']);
		$factory->shouldReceive('make')->once()->with('alert_template', Mockery::on(function($data) {
			$this->assertArrayHasKey('alert_type', $data);
			$this->assertEquals('success', $data['alert_type']);
			$this->assertArrayHasKey('alert_text', $data);
			$this->assertEquals('foo bar', $data['alert_text']);
			return true;
		}))->andReturn($subview);
		$subview->shouldReceive('render')->once()->with()->andReturn('rendered alert');

		$view = new View($view_factory, $engine, 'view', 'path', ['foo' => 'bar']);

		$callback = $view_factory->composer('foo', AlertComposer::class);
		$callback = $callback[0];

		$callback($view);

		$viewdata = $view->getData();

		$this->assertArrayHasKey('alerts', $viewdata);
		$this->assertEquals('rendered alert', $viewdata['alerts']);
	}

}
