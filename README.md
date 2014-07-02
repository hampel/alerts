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
            "hampel/alerts": "1.1.*"
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

The alerts functionality is based around a class which extends the MessageBag implementation provided by Laravel. This AlertMessageBag provides
additional services to add alert messages and render alert views.

We can easily add alerts to the MessageBag using the built-in functionality:

	:::php
	Alert::add('error', 'An error message');

There are four alert levels provided by default: `info`, `warning`, `error`, `success`.

You can add a message to the MessageBag using a shorthand function corresponding to the configured alert levels:

 	:::php
 	Alert::error('An error message');
 	Alert::success('Operation succeeded');

If you prefer to use the language translation files for your messages, you can simply specify a translation key and the MessageBag will
substitute the corresponding language entry and optionally apply any replacements too. Note that this only works if you use the shorthand
functions to add your messages.

 	:::php
 	Alert::success('auth.login.success');
 	Alert::error('validation.accepted', array('attribute' => 'terms'));

Alternatively, you could apply the translation yourself:

	:::php
	Alert::add('success', Lang::get('auth.login.success'));
 	Alert::error(Lang::get('validation.accepted', array('attribute' => 'terms')));

To pass the MessageBag to a view response, you will need to flash the data to the session.

You can do this in several ways - the AlertMessageBag class provides a `flash` function, which sets the session key for you automatically, or
you could do it as part of a redirect response. If not using the built-in `flash` function, make sure you specify the name of the session
key used in the config file `'alerts::session_key'`, or use the helper function `Alerts::getSessionKey()`:

	:::php
	// flash the messages to the session
	Alert::success('Operation succeeded')->flash();

	// return a redirect response and flash the messages
	return Redirect::to('myroute')->with(Alert::getSessionKey(), Alert::error('There was a problem')->getMessages());

	// you can always just do it all manually
	$messages = new AlertMessageBag;
	$messages->add('info', Lang::get('something.happened.info'));
	Session::flash(Config::get('alerts::session_key', $messagebag); // make sure you pass the whole AlertMessageBag to the session!

The package provides a view composer which automatically renders alerts whenever the view is called. There are several ways to do this.

By default, there is a Blade template `alerts.blade.php` which you can include in your base view. The view composer is configured by default
to watch this view and render the alerts whenever this view is rendered. To use, simply `@include` the template in your view:

	:::php
	@include('alerts::alerts')

If you prefer to do it yourself, you can change the configuration of the view composer to watch a different view, perhaps even your `layouts.base`
view. The composer will generate the alert data and bind it to the configured variable name, which you can then simply echo out in your view.

There are templates provided for both Bootstrap and Foundation, although Foundation is the default - you'll need to change the configuration
setting if you use Bootstrap. Alternatively, you can provide your own view template to use to render each alert message.  Refer to the
configuration file for more information on what the settings do.
