<?php
/**
 * Configuration for Alerts
 */

return array(

	/*
	|--------------------------------------------------------------------------
	| Alert Level Map
	|--------------------------------------------------------------------------
	|
	| The alert levels and corresponding class maps to use in view rendering.
	|
	| These alert levels can be used as a convenient shortcut for adding
	| messages of a particular type:
	|
	| For example:
	|
	|     Alerts::info($message);
	|
	| Any session messages using keys other than those listed below, will be
	| ignored when rendering the alert view.
	|
	| The array value in the map should correspond to the name of the class to
	| be used when rendering the view for an alert of that type.
	|
	| For example, to configure alerts for Bootstrap, change the config to match
	| the following:
	|
	| 'levels' => array(
	|     'info' => 'info',
	|     'warning' => 'warning',
	|     'error' => 'danger',
	|     'success' => 'success'
	| ),
	|
	| The default values provided are intended to work with Foundation, with
	| 	info = a "secondary" alert
	| 	warning = a "standard" alert
	| 	error = an "alert" alert
	| 	success = a "success" alert
	|
	*/

	'level_map' => array(
		'info' => 'secondary',
		'warning' => '',
		'error' => 'alert',
		'success' => 'success',
	),

	/*
	|--------------------------------------------------------------------------
	| Session Key
	|--------------------------------------------------------------------------
	|
	| The session key which is used to store flashed messages into the current
	| session. This can be changed if it conflicts with another key.
	|
	*/

	'session_key' => 'alert_messages',

	/*
	|--------------------------------------------------------------------------
	| Base View Name
	|--------------------------------------------------------------------------
	|
	| The name of the view in which to compose the alerts views. When this view
	| is rendered, the alert data will be bound to a variable which can be
	| echoed in this view.
	|
	| You can either specify your own view name here and make sure you echo out
	| the variable specified below, or else make sure you @include this
	| view in your own view.
	|
	| For example, to use the default value, you must specify the following in
	| your view where you want the alerts to appear:
	|
	| @include('alerts::alerts')
	|
	*/

	'base_view' => 'alerts::alerts',

	/*
	|--------------------------------------------------------------------------
	| Alerts Variable Name
	|--------------------------------------------------------------------------
	|
	| The name of the variable to which we will bind the rendered alerts views.
	| If you use a custom base_view above, you might want to specify your own
	| variable name here as well.
	|
	| For example, if you specify a variable name of 'my_alerts', you must echo
	| this variable in your view to have it display the alerts:
	|
	| <div>
	| 	{{ $my_alerts }}
	| </div>
	|
	| If using the default base_view above, leave the view_variable set to the
	| default value 'alerts'
	|
	*/

	'view_variable' => 'alerts',

	/*
	|--------------------------------------------------------------------------
	| Alert Template Name
	|--------------------------------------------------------------------------
	|
	| The name of the view template used to generate the alert code.
	|
	| There are two default templates provided - one for Bootstrap and one for
	| Foundation based projects. You will need to provide your own CSS to make
	| the code work.
	|
	| If you don't like the default templates, or if you want to use a different
	| front-end framework or your own code, simply change the name of the
	| view template here to use to render each alert.
	|
	| Note that multiple alerts will be displayed each with their own alert box.
	|
	| For example, to use the provided Bootstrap template, change this value to
	| 'alerts::templates.bootstrap'
	|
	| If implementing your own template or modifying the provided templates,
	| there are two variables which will be bound to the view:
	|
	| 'alert_type' contains the 'level' of the alert as per the levels setting above,
	| which is intended to be used in a class name
	|
	| 'alert_text' contains the message text of the alert
	|
	*/

	'alert_template' => 'alerts::templates.foundation',

);
