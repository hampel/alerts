<?php namespace Hampel\Alerts\Facades;
/**
 * 
 */

use Hampel\Alerts\AlertManager;
use Illuminate\Support\Facades\Facade;

class Alert extends Facade {

    protected static function getFacadeAccessor() { return AlertManager::class; }

}
