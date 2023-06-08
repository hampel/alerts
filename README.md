Alerts for Laravel
==================

[![Latest Version on Packagist](https://img.shields.io/packagist/v/hampel/alerts.svg?style=flat-square)](https://packagist.org/packages/hampel/alerts)
[![Total Downloads](https://img.shields.io/packagist/dt/hampel/alerts.svg?style=flat-square)](https://packagist.org/packages/hampel/alerts)
[![Open Issues](https://img.shields.io/github/issues-raw/hampel/alerts?style=flat-square)](https://github.com/hampel/alerts/issues)
[![License](https://img.shields.io/packagist/l/hampel/alerts.svg?style=flat-square)](https://packagist.org/packages/hampel/alerts)

This package provides session-driven alerts for Laravel and provides alert templates for both Bootstrap and Foundation
based front-end frameworks.

By [Simon Hampel](mailto:simon@hampelgroup.com)

Installation
------------

The recommended way of installing the Alerts package is through [Composer](http://getcomposer.org):

Require the package via Composer in your `composer.json`

    :::json
    {
        "require": {
            "hampel/alerts": "^2.1"
        }
    }

Run Composer to update the new requirement.

    :::bash
    $ composer update

Open your Laravel config file `config/app.php` and add the following service providers in the `$providers` array, if
they don't already exist:

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

If you want to change the default Alert configuration or views, first publish them using the command:

    :::bash
    $ php artisan vendor:publish --provider="Hampel\Alerts\AlertServiceProvider"

The config file can then be found in `config/alerts.php`, and the views in `resources/views/vendor/alerts`

Usage
-----

The alerts functionality is based around two classes - the first is an AlertManager class, which you can access in your code
via the Alert facade (or via the container $app['alerts']). The AlertManager class uses a Laravel MessageBag to store
alert a collection of alerts, grouped by alert key (warning, error, success, etc).

Once you have finished adding alerts, you can then "flash" them to the session so that the alert messages are then
available after a page redirect.

At this point, the second part of the functionality comes into play. The package provides a view composer, which waits
for a particular view to be rendered and then executes, injecting the stored alert information which is retrieved from
the session. The alerts are essentially rendered as a series of sub-views which are inserted into the base view using
a configurable variable.

We can easily add alerts to the AlertManager using the built-in functionality:

    :::php
    Alert::add('error', 'An error message');

There are four alert levels provided by default: `info`, `warning`, `error`, `success`. Note that any alert level passed
to the add function which is not in the configured list of alert levels, will be ignored when rendering the view.

You can add a message to the AlertManager using a shorthand function corresponding to the configured alert levels:

    :::php
    Alert::error('An error message');
    Alert::success('Operation succeeded');

If you prefer to use the language translation files for your messages, you can simply specify a translation key and the
AlertManager will substitute the corresponding language entry and optionally apply any replacements too. Note that this
only works if you use the shorthand functions to add your messages.

    :::php
    Alert::success('auth.login.success');
    Alert::error('validation.accepted', array('attribute' => 'terms'));

Alternatively, you could apply the translation yourself:

    :::php
    Alert::add('success', Lang::get('auth.login.success'));
    Alert::error(Lang::get('validation.accepted', array('attribute' => 'terms')));

You can retrieve the underlying MessageBag implementation and merge an existing MessageBag.

    :::php
    $mybag = new MesageBag();
    // add messages to your messagebag

    // now get the alert message bag
    $alerts = Alert::getMessageBag();

    // merge in our messages
    $alerts->merge($mybag);

    // flash the messages to the session
    Alert::flash();

To pass the alert messages to a view response, you will need to flash the data to the session.

You can do this in several ways - the AlertManager class provides a `flash` function, which sets the session key for
you automatically, or you could do it as part of a redirect response. If not using the built-in `flash` function, make
sure you specify the name of the session key used in the config file `'alerts::session_key'`, or use the helper function
`Alerts::getSessionKey()`:

    :::php
    // flash the messages to the session - this is the easiest way to do it
    Alert::success('Operation succeeded')->flash();

    // alternatively, return a redirect response and let Laravel flash the messages
    return Redirect::to('myroute')->with(Alert::getSessionKey(), Alert::error('There was a problem')->getMessageBag()->getMessages());

    // you can always just do it all manually and not use the AlertManager at all!
    $messages = new MessageBag;
    $messages->add('info', Lang::get('something.happened.info'));
    Session::flash(Config::get('alerts::session_key', $messages->getMessages());

The package provides a view composer which automatically renders alerts whenever the view is called. There are several
ways to do this.

By default, there is a Blade template `alerts.blade.php` which you can include in your base view. The view composer is
configured by default to watch this view and render the alerts whenever this view is rendered. To use, simply `@include`
the template in your view:

    :::php
    @include('alerts::alerts')

If you prefer to do it yourself, you can change the configuration of the view composer to watch a different view,
perhaps even your `layouts.base` view. The composer will generate the alert data and bind it to the configured variable
name, which you can then simply echo out in your view.

There are templates provided for both Bootstrap and Foundation, although Foundation is the default - you'll need to
change the configuration setting if you use Bootstrap. Alternatively, you can provide your own view template to use to
render each alert message.  Refer to the configuration file for more information on what the settings do.
