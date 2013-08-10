1.0.4 / 2013-08-10
==================

  * Better randomness for generated class names (thanks @sstok)
  * Add support for interfaces into TypeToken and Argument::type() (thanks @sstok)
  * Add support for old-style (method name === class name) constructors (thanks @l310 for report)

1.0.3 / 2013-07-04
==================

  * Support callable typehints (thanks @stof)
  * Do not attempt to autoload arrays when generating code (thanks @MarcoDeBortoli)
  * New ArrayEntryToken (thanks @kagux)

1.0.2 / 2013-05-19
==================

  * Logical `AND` token added (thanks @kagux)
  * Logical `NOT` token added (thanks @kagux)
  * Add support for setting custom constructor arguments
  * Properly stringify hashes
  * Record calls that throw exceptions
  * Migrate spec suite to PhpSpec 2.0

1.0.1 / 2013-04-30
==================

  * Fix broken UnexpectedCallException message
  * Trim AggregateException message

1.0.0 / 2013-04-29
==================

  * Improve exception messages

1.0.0-BETA2 / 2013-04-03
========================

  * Add more debug information to CallTimes and Call prediction exception messages
  * Fix MethodNotFoundException wrong namespace (thanks @gunnarlium)
  * Fix some typos in the exception messages (thanks @pborreli)

1.0.0-BETA1 / 2013-03-25
========================

  * Initial release
