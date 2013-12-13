<?php namespace Hampel\Alerts;

use Mockery;

class AlertMessageBagTest extends \PHPUnit_Framework_TestCase
{
	public function testFlash()
	{
		$config = Mockery::mock('Illuminate\Config\Repository');
		$config->shouldReceive('get')->once()->with('alerts::session_key')->andReturn('alert_messages');

		$session = Mockery::mock('Illuminate\Session\Store');
		$session->shouldReceive('flash')->once()->with('alert_messages', array('foo' => array('bar')));

		$container = Mockery::mock('Illuminate\Container\Container');
		$container->shouldReceive('offsetGet')->once()->with('session.store')->andReturn($session);
		$container->shouldReceive('offsetGet')->once()->with('config')->andReturn($config);

		$messagebag = new AlertMessageBag();
		$messagebag->setContainer($container);
		$messagebag->add('foo', 'bar');
		$messagebag->flash();
	}

	public function testGetLevels()
	{
		$config = Mockery::mock('Illuminate\Config\Repository');
		$config->shouldReceive('get')->once()->with('alerts::session_key')->andReturn('alert_messages');

		$container = Mockery::mock('Illuminate\Container\Container');
		$container->shouldReceive('offsetGet')->once()->with('config')->andReturn($config);

		$messagebag = new AlertMessageBag();
		$messagebag->setContainer($container);

		$this->assertEquals('alert_messages', $messagebag->getSessionKey());
	}

	public function testRenderView()
	{
		$config = Mockery::mock('Illuminate\Config\Repository');
		$config->shouldReceive('get')->once()->with('alerts::level_map')->andReturn(array(
			'info' => 'secondary',
			'warning' => '',
			'error' => 'alert',
			'success' => 'success',
		));
		$config->shouldReceive('get')->twice()->with('alerts::alert_template')->andReturn('alerts::templates.foundation');

		$view = Mockery::mock('Illuminate\View\View');
		$view->shouldReceive('render')->twice()->with()->andReturn('view1', 'view2');

		$env = Mockery::mock('Illuminate\View\Environment');
		$env->shouldReceive('make')->once()->with(
			'alerts::templates.foundation',
			array('alert_type' => 'secondary', 'alert_text' => 'alert.info')
		)->andReturn($view);

		$env->shouldReceive('make')->once()->with(
			'alerts::templates.foundation',
			array('alert_type' => 'alert', 'alert_text' => 'alert.error')
		)->andReturn($view);

		$container = Mockery::mock('Illuminate\Container\Container');
		$container->shouldReceive('offsetGet')->times(3)->with('config')->andReturn($config);
		$container->shouldReceive('offsetGet')->twice()->with('view')->andReturn($env);

		$messagebag = new AlertMessageBag();
		$messagebag->setContainer($container);

		$messagebag->add('info', 'alert.info');
		$messagebag->add('error', 'alert.error');

		$this->assertEquals('view1view2', $messagebag->renderView());
	}

	public function testCall()
	{
		$config = Mockery::mock('Illuminate\Config\Repository');
		$config->shouldReceive('get')->once()->with('alerts::level_map')->andReturn(array(
			'info' => 'secondary',
			'warning' => '',
			'error' => 'alert',
			'success' => 'success',
		));

		$lang = Mockery::mock('Illuminate\Translation\Translator');
		$lang->shouldReceive('has')->once()->with('alert.info')->andReturn(true);
		$lang->shouldReceive('get')->once()->with('alert.info')->andReturn('info alert');

		$container = Mockery::mock('Illuminate\Container\Container');
		$container->shouldReceive('offsetGet')->once()->with('config')->andReturn($config);
		$container->shouldReceive('offsetGet')->twice()->with('translator')->andReturn($lang);

		$messagebag = new AlertMessageBag();
		$messagebag->setContainer($container);

		$messagebag->info('alert.info');
		$this->assertTrue($messagebag->has('info'));
		$this->assertTrue(is_array($messagebag->get('info')));
		$this->assertArrayHasKey(0, $messagebag->get('info'));
		$this->assertEquals('info alert', array_pop($messagebag->get('info')));
	}

	public function testCallWithReplacements()
	{
		$config = Mockery::mock('Illuminate\Config\Repository');
		$config->shouldReceive('get')->once()->with('alerts::level_map')->andReturn(array(
			'info' => 'secondary',
			'warning' => '',
			'error' => 'alert',
			'success' => 'success',
		));

		$lang = Mockery::mock('Illuminate\Translation\Translator');
		$lang->shouldReceive('has')->once()->with('alert.info')->andReturn(true);
		$lang->shouldReceive('get')->once()->with('alert.info', array('foo' => 'bar'))->andReturn('info alert');

		$container = Mockery::mock('Illuminate\Container\Container');
		$container->shouldReceive('offsetGet')->once()->with('config')->andReturn($config);
		$container->shouldReceive('offsetGet')->twice()->with('translator')->andReturn($lang);

		$messagebag = new AlertMessageBag();
		$messagebag->setContainer($container);

		$messagebag->info('alert.info', array('foo' => 'bar'));
		$this->assertTrue($messagebag->has('info'));
		$this->assertTrue(is_array($messagebag->get('info')));
		$this->assertArrayHasKey(0, $messagebag->get('info'));
		$this->assertEquals('info alert', array_pop($messagebag->get('info')));
	}

	public function tearDown() {
		Mockery::close();
	}
}

?>
