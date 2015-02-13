<?php namespace Hampel\Alerts;

use Illuminate\Support\MessageBag;
use Mockery;

class AlertManagerTest extends \PHPUnit_Framework_TestCase
{
	public function testManager()
	{
		$config = Mockery::mock('Illuminate\Contracts\Config\Repository');
		$session = Mockery::mock('Illuminate\Session\SessionInterface');
		$translator = Mockery::mock('Symfony\Component\Translation\TranslatorInterface');

		$config->shouldReceive('get')->once()->with('alerts.level_map')->andReturn(array(
			'info' => 'secondary',
			'warning' => '',
			'error' => 'alert',
			'success' => 'success',
		));
		$translator->shouldReceive('get')->once()->with('alert.info', [])->andReturn('info alert');
		$config->shouldReceive('get')->once()->with('alerts.session_key')->andReturn('alert_messages');
		$session->shouldReceive('flash')->once()->with('alert_messages', array('info' => ['info alert']));

		$manager = new AlertManager($config, $session, new MessageBag, $translator);
		$manager->info('alert.info');
		$messages = $manager->flash();

		$this->assertTrue($messages->has('info'));
		$this->assertTrue(is_array($messages->get('info')));
		$this->assertArrayHasKey(0, $messages->get('info'));
		$this->assertEquals('info alert', array_pop($messages->get('info')));
	}

	public function testManagerReplacements()
	{
		$config = Mockery::mock('Illuminate\Contracts\Config\Repository');
		$session = Mockery::mock('Illuminate\Session\SessionInterface');
		$translator = Mockery::mock('Symfony\Component\Translation\TranslatorInterface');

		$config->shouldReceive('get')->times(3)->with('alerts.level_map')->andReturn(array(
			'info' => 'secondary',
			'warning' => '',
			'error' => 'alert',
			'success' => 'success',
		));
		$translator->shouldReceive('get')->once()->with('alert.info', [])->andReturn('Info Alert');
		$translator->shouldReceive('get')->once()->with('Non-translated Error', [])->andReturn('Non-translated Error');
		$translator->shouldReceive('get')->once()->with('alert.error', ['foo' => 'bar'])->andReturn('Translated Error');
		$config->shouldReceive('get')->once()->with('alerts.session_key')->andReturn('alert_messages');
		$session->shouldReceive('flash')->once()->with('alert_messages', Mockery::on(function($data) {
			$this->assertArrayHasKey('info', $data);
			$this->assertArrayHasKey(0, $data['info']);
			$this->assertEquals('Info Alert', $data['info'][0]);
			$this->assertArrayHasKey('error', $data);
			$this->assertArrayHasKey(0, $data['error']);
			$this->assertEquals('Non-translated Error', $data['error'][0]);
			$this->assertArrayHasKey(1, $data['error']);
			$this->assertEquals('Translated Error', $data['error'][1]);
			return true;
		}));

		$manager = new AlertManager($config, $session, new MessageBag, $translator);
		$manager->info('alert.info');
		$manager->error('Non-translated Error');
		$manager->error('alert.error', ['foo' => 'bar']);
		$messages = $manager->flash();

		$this->assertTrue($messages->has('info'));
		$this->assertTrue(is_array($messages->get('info')));
		$this->assertArrayHasKey(0, $messages->get('info'));
		$this->assertEquals('Info Alert', array_pop($messages->get('info')));

		$this->assertTrue($messages->has('error'));
		$errors = $messages->get('error');
		$this->assertTrue(is_array($errors));
		$this->assertArrayHasKey(0, $errors);
		$this->assertEquals('Translated Error', array_pop($errors));
		$this->assertEquals('Non-translated Error', array_pop($errors));
	}

	public function tearDown() {
		Mockery::close();
	}
}

?>
