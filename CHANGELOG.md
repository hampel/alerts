CHANGELOG
=========

2.1.0 (2015-02-13)
------------------

* now fully compatible with Laravel 5.0 release

2.0.2 (2014-10-15)
------------------

* added binding for Hampel\Alerts\AlertManager to service provider to help with type hinting

2.0.1 (2014-09-29)
------------------

* handle situation where no alerts are actually raised

2.0.0 (2014-09-26)
------------------

* complete rewrite, now supports (only) Laravel v5.0
* psr-4 autoloading
* replaced AlertMessageBag class with an AlertManager class
* rewrote AlertComposer
* changed blade templates to new L5 raw echo syntax
* rewrote unit tests

1.2.2 (2014-07-03)
------------------

* removed redundant use code from AlertServiceProvider
* removed redundant use code

1.2.1 (2014-07-02)
------------------

* documentation error - need to getMessages() when using with()

1.2.0 (2014-06-04)
------------------

* make unit tests 4.2 compatible
* framework minimum version now 4.2

1.1.4 (2014-07-03)
------------------

* removed redundant use code
* changed framework version requirements to ">=4.0,<4.2" for 1.1 branch

1.1.3 (2014-07-02)
------------------

* documentation error - need to getMessages() when using with()

1.1.2 (2014-06-04)
------------------

* removed minimum-stability line from composer.json
* remove phpunit as dev dependency

1.1.1 (2013-12-14)
------------------

* added additional required illuminate packages to composer.json
* changed AlertMessageBag class to use Container rather than Facades, added setContainer method, ajusted Service
  Provider to set the container when binding class
* added View type hint to AlertComposer::compose() method
* refactored code for testability
* added unit tests

1.1.0 (2013-12-13)
------------------

* updated framework requirement to 4.1.*

1.0.1 (2013-11-19)
------------------

* changed levels array to a class map because different UI frameworks use different classnames to indicate different
  alert levels
* added Usage information to README

1.0.0 (2013-11-19)
------------------

* initial release
