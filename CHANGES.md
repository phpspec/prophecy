Unreleased
==========

1.22.0
======

**Removed:**

* Support for PHP 7.2 and 7.3 (@jean85)

1.21.0
======

**Added:**

* Add support for PHPUnit 12 (@jean85)

1.20.0
======

**Added:**

* Add support for PHP 8.4 (@andypost)

**Fixed:**

* Fix support for doubling methods using an enum case as default value of a parameter (@jdreesen)
* Fix deprecation when doubling a class with constructor parameters (@singinwhale, @W0rma)
* Fix deprecation warning when using phpdocumentor/reflection-docblock 5.4+ (@jrfnl)

1.19.0
======

**Added:**

* Allow sebastian/comparator and sebastian/recursion-context 6

1.18.0 / 2023-12-07
===================

* [added] Add support for PHP 8.3 [@rajeshreeputra]
* [changed] Improve the error when using return types that Prophecy does not support for mocking [@stof]
* [changed] Add more precise type for static analysis [@stof]
* [fixed] Error when comparing object arguments with integers [@lucassabreu]
* [changed] Add PHP 8.2 to test matrix [@Jean85]
* [Added] Allow sebastian/comparator and sebastian/recursion-context 5, and phpunit/phpunit 10 [@Jean85]
* [docs] Switch travis status badge to GHA one [@michalbundyra]

1.17.0 / 2023-02-02
===================

* [added] Add generic types for ProphecyInterface and ObjectProphecy [@stof]
* [added] Add the conditional return type for `ObjectProphecy::getMethodProphecies` [@stof]
* [added] Add support for doctrine/instantiator 2.0 [@stof]
* [added] Add the ability to customize the __toString representation of a CallbackToken [@ian-zunderdorp]
* [changed] Remove support for instantiating a MethodProphecy without its arguments [@stof]
* [deprecated] Deprecate `\Prophecy\Comparator\Factory` as `sebastian/comparator` v5 makes it parent class final [@stof]

1.16.0 / 2022/11/29
===================

* [added] Allow installing with PHP 8.2 [@gquemener]
* [added] Use shorter object IDs for object comparison [@TysonAndre]
* [added] Support standalone false,true and null types [@kschatzle]
* [added] Support doubling readonly classes [@gquemener]
* [fixed] Remove workarounds for unsupported HHVM [@TysonAndre]
* [fixed] Clear error message when doubling DNF types [@kschatzle]


1.15.0 / 2021/12/08
===================

* [added] Support for the `static` return type [@denis-rolling-scopes]
* [fixed] Add return types for Comparator implementations to avoid deprecation warnings from Symfony's DebugClassLoader [@stof]

1.14.0 / 2021/09/16
===================

* [added] Support for static closures in will and should [@ntzm]
* [added] Allow install on PHP 8.1 (with test suite fixes) [@javer]
* [added] Support for the 'never' return type [@ciaranmcnulty]
* [fixed] Better error message when doubling intersection return types [@ciaranmcnulty]

1.13.0 / 2021/03/17
===================

* [added] willYield can now specify a return value [@camilledejoye]
* [added] Prophecy exception interfaces are explicitly Throwable [@ciaranmcnulty]
* [fixed] Argument::in() and notIn() now marked as static [@tyteen4a03]
* [fixed] Can now double unions containing false [@ciaranmcnulty]
* [fixed] Virtual magic methods with arguments are now doublable in PHP 8 [@ciaranmcnulty]

1.12.2 / 2020/12/19
===================

* [fixed] MethodNotFoundException sometimes thrown with wrong class attached [@ciaranmcnulty]

1.12.1 / 2020/10/29
===================

* [fixed] Incorrect handling of inherited 'self' return types [@ciaranmcnulty]

1.12.0 / 2020/10/28
===================

* [added] PHP 8 support [@ciaranmcnulty]
* [added] Argument::in() and Argument::notIn() [@viniciusalonso]
* [added] Support for union and mixed types [@ciaranmcnulty]
* [fixed] Issues caused by introduction of named parameters [@ciaranmcnulty]
* [fixed] Issues caused by stricter rounding [@ciaranmcnulty]

1.11.1 / 2020/07/08
===================

* [fixed] can't double objects with `self` type hints (@greg0ire)
* [fixed] cloned doubes were not loosely comparable (@tkulka)

1.11.0 / 2020/07/07
===================

* [changed] dropped support for PHP versions earlier than 7.2 (@ciaranmcnulty)
* [fixed] removed use of Reflection APIs deprecated in PHP 8.0 (@Ayesh)

1.10.3 / 2020/03/05
===================

* [fixed] removed fatal error when phpdocumentor/reflection-docblock 5 parses an invalid `@method` tag (@stof)

1.10.2 / 2020/01/20
===================

* [added] support for new versions of `sebastian/comparator` and `sebastian/recursion-context` (@sebastianbergmann)

1.10.1 / 2019/12/22
===================

* [fixed] identical callables no longer match as arguments (@ciaranmcnulty)

1.10.0 / 2019/12/17
===================

* [added] shouldHaveBeenCalled evaluation happens later so un-stubbed calls don't throw (@elvetemedve)
* [added] methods can now be doubled case-insensitively to match PHP semantics (@michalbundyra)
* [fixed] reduced memory usage by optimising CachedDoubler (@DonCallisto)
* [fixed] removed fatal error nesting level when comparing large objects (@scroach)

1.9.0 / 2019/10/03
==================

* [added] Add willYield feature to Method Prophecy(@tkotosz)
* [fixed] Allow `MethodProphecy::willThrow()` to accept Throwable as string (@timoschinkel )
* [fixed] Allow new version of phpdocumentor/reflection-docblock (@ricpelo)

1.8.1 / 2019/06/13
==================

* [fixed] Don't try to patch final constructors (@NiR)

1.8.0 / 2018/08/05
==================

* Support for void return types without explicit will (@crellbar)
* Clearer error message for unexpected method calls (@meridius)
* Clearer error message for aggregate exceptions (@meridius)
* More verbose `shouldBeCalledOnce` expectation (@olvlvl)
* Ability to double Throwable, or methods that extend it (@ciaranmcnulty)
* [fixed] Doubling methods where class has additional arguments to interface (@webimpress)
* [fixed] Doubling methods where arguments are nullable but default is not null (@webimpress)
* [fixed] Doubling magic methods on parent class (@dsnopek)
* [fixed] Check method predictions only once (@dontub)
* [fixed] Argument::containingString throwing error when called with non-string (@dcabrejas)

1.7.6 / 2018/04/18
==================

* Allow sebastian/comparator ^3.0 (@sebastianbergmann)

1.7.5 / 2018/02/11
==================

* Support for object return type hints (thanks @greg0ire)

1.7.4 / 2018/02/11
==================

* Fix issues with PHP 7.2 (thanks @greg0ire)
* Support object type hints in PHP 7.2 (thanks @@jansvoboda11)

1.7.3 / 2017/11/24
==================

* Fix SplInfo ClassPatch to work with Symfony 4 (Thanks @gnugat)

1.7.2 / 2017-10-04
==================

* Reverted "check method predictions only once" due to it breaking Spies

1.7.1 / 2017-10-03
==================

* Allow PHP5 keywords methods generation on PHP7 (thanks @bycosta)
* Allow reflection-docblock v4 (thanks @GrahamCampbell)
* Check method predictions only once (thanks @dontub)
* Escape file path sent to \SplFileObjectConstructor when running on Windows (thanks @danmartin-epiphany)

1.7.0 / 2017-03-02
==================

* Add full PHP 7.1 Support (thanks @prolic)
* Allow `sebastian/comparator ^2.0` (thanks @sebastianbergmann)
* Allow `sebastian/recursion-context ^3.0` (thanks @sebastianbergmann)
* Allow `\Error` instances in `ThrowPromise` (thanks @jameshalsall)
* Support `phpspec/phpspect ^3.2` (thanks @Sam-Burns)
* Fix failing builds (thanks @Sam-Burns)

1.6.2 / 2016-11-21
==================

* Added support for detecting @method on interfaces that the class itself implements, or when the stubbed class is an interface itself (thanks @Seldaek)
* Added support for sebastian/recursion-context 2 (thanks @sebastianbergmann)
* Added testing on PHP 7.1 on Travis (thanks @danizord)
* Fixed the usage of the phpunit comparator (thanks @Anyqax)

1.6.1 / 2016-06-07
==================

  * Ignored empty method names in invalid `@method` phpdoc
  * Fixed the mocking of SplFileObject
  * Added compatibility with phpdocumentor/reflection-docblock 3

1.6.0 / 2016-02-15
==================

  * Add Variadics support (thanks @pamil)
  * Add ProphecyComparator for comparing objects that need revealing (thanks @jon-acker)
  * Add ApproximateValueToken (thanks @dantleech)
  * Add support for 'self' and 'parent' return type (thanks @bendavies)
  * Add __invoke to allowed reflectable methods list (thanks @ftrrtf)
  * Updated ExportUtil to reflect the latest changes by Sebastian (thanks @jakari)
  * Specify the required php version for composer (thanks @jakzal)
  * Exclude 'args' in the generated backtrace (thanks @oradwell)
  * Fix code generation for scalar parameters (thanks @trowski)
  * Fix missing sprintf in InvalidArgumentException __construct call (thanks @emmanuelballery)
  * Fix phpdoc for magic methods (thanks @Tobion)
  * Fix PhpDoc for interfaces usage (thanks @ImmRanneft)
  * Prevent final methods from being manually extended (thanks @kamioftea)
  * Enhance exception for invalid argument to ThrowPromise (thanks @Tobion)

1.5.0 / 2015-04-27
==================

  * Add support for PHP7 scalar type hints (thanks @trowski)
  * Add support for PHP7 return types (thanks @trowski)
  * Update internal test suite to support PHP7

1.4.1 / 2015-04-27
==================

  * Fixed bug in closure-based argument tokens (#181)

1.4.0 / 2015-03-27
==================

  * Fixed errors in return type phpdocs (thanks @sobit)
  * Fixed stringifying of hash containing one value (thanks @avant1)
  * Improved clarity of method call expectation exception (thanks @dantleech)
  * Add ability to specify which argument is returned in willReturnArgument (thanks @coderbyheart)
  * Add more information to MethodNotFound exceptions (thanks @ciaranmcnulty)
  * Support for mocking classes with methods that return references (thanks @edsonmedina)
  * Improved object comparison (thanks @whatthejeff)
  * Adopted '^' in composer dependencies (thanks @GrahamCampbell)
  * Fixed non-typehinted arguments being treated as optional (thanks @whatthejeff)
  * Magic methods are now filtered for keywords (thanks @seagoj)
  * More readable errors for failure when expecting single calls (thanks @dantleech)

1.3.1 / 2014-11-17
==================

  * Fix the edge case when failed predictions weren't recorded for `getCheckedPredictions()`

1.3.0 / 2014-11-14
==================

  * Add a way to get checked predictions with `MethodProphecy::getCheckedPredictions()`
  * Fix HHVM compatibility
  * Remove dead code (thanks @stof)
  * Add support for DirectoryIterators (thanks @shanethehat)

1.2.0 / 2014-07-18
==================

  * Added support for doubling magic methods documented in the class phpdoc (thanks @armetiz)
  * Fixed a segfault appearing in some cases (thanks @dmoreaulf)
  * Fixed the doubling of methods with typehints on non-existent classes (thanks @gquemener)
  * Added support for internal classes using keywords as method names (thanks @milan)
  * Added IdenticalValueToken and Argument::is (thanks @florianv)
  * Removed the usage of scalar typehints in HHVM as HHVM 3 does not support them anymore in PHP code (thanks @whatthejeff)

1.1.2 / 2014-01-24
==================

  * Spy automatically promotes spied method call to an expected one

1.1.1 / 2014-01-15
==================

  * Added support for HHVM

1.1.0 / 2014-01-01
==================

  * Changed the generated class names to use a static counter instead of a random number
  * Added a clss patch for ReflectionClass::newInstance to make its argument optional consistently (thanks @docteurklein)
  * Fixed mirroring of classes with typehints on non-existent classes (thanks @docteurklein)
  * Fixed the support of array callables in CallbackPromise and CallbackPrediction (thanks @ciaranmcnulty)
  * Added support for properties in ObjectStateToken (thanks @adrienbrault)
  * Added support for mocking classes with a final constructor (thanks @ciaranmcnulty)
  * Added ArrayEveryEntryToken and Argument::withEveryEntry() (thanks @adrienbrault)
  * Added an exception when trying to prophesize on a final method instead of ignoring silently (thanks @docteurklein)
  * Added StringContainToken and Argument::containingString() (thanks @peterjmit)
  * Added ``shouldNotHaveBeenCalled`` on the MethodProphecy (thanks @ciaranmcnulty)
  * Fixed the comparison of objects in ExactValuetoken (thanks @sstok)
  * Deprecated ``shouldNotBeenCalled`` in favor of ``shouldNotHaveBeenCalled``

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
