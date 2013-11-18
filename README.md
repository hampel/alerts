Alerts for Laravel
==================

This package provides session-driven alerts for Laravel and provides alert templates for both Bootstrap and Foundation based front-end frameworks.

By [Simon Hampel](http://hampelgroup.com/).

Installation
------------

The recommended way of installing the Alerts package is through [Composer](http://getcomposer.org):

Require the package via Composer in your `composer.json`

	:::json
    {
        "require": {
            "hampel/alerts": "1.0.*"
        }
    }

Run Composer to update the new requirement.

	:::bash
    $ composer update

The package is built to work with the Laravel 4 Framework.

Open your Laravel config file `config/app.php` and add the following service providers in the `$providers` array, if they don't already exist:

	:::php
    "providers" => array(

        ...

		'Hampel\Alerts\AlertServiceProvider',

    ),

You may also optionally add an alias entry to the `$aliases` array in the same file for the Alert facade:

	:::php
	"aliases" => array(

		...

		'Alert'			  => 'Hampel\Alerts\Facades\Alert',
	),

If you want to change the default Alert configuration, first publish it using the command:

	:::bash
	$ php artisan config:publish hampel/alerts

The config file can then be found in `app/config/packages/hampel/alerts/config.php`.

Usage
-----

TODO!

