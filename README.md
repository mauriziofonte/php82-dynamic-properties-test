# PHP 8.2 [AllowDynamicProperties] showcase

This utility is intended to showcase what's contained in the [PHP 8.2 AllowDynamicProperties RFC](https://php.watch/versions/8.2/dynamic-properties-deprecated#AllowDynamicProperties).

This is to avoid Deprecation Warnings on PHP 8.2 when using _Dynamic Properties_: `PHP Deprecated: Creation of dynamic property Class::$attribute is deprecated`.

This repository is intended to show how to globally solve the `PHP Deprecated: Creation of dynamic property is deprecated` with minor code changes, thus **allowing** dynamic properties with no warnings.

## TL;DR

To make a _Class_ compatible with _Dynamic Attributes_, follow the below example, that is purposedly using a namespace.

> Pay attention: in a project using namespaced classes, you will HAVE TO add the `use \AllowDynamicProperties;` to make this work. Otherwise, PHP won't know what "AllowDynamicProperties" is. Under the hoods, in fact, the attribute set above the Class definition is using [The AllowDynamicProperties class](https://www.php.net/manual/en/class.allowdynamicproperties.php), that really is a _Final Class_ itself.

```php
<?php
namespace App\Classes;

/**
 * Use the fully-qualified AllowDynamicProperties, otherwise the #[AllowDynamicProperties] attribute on "MyClass" WILL NOT WORK.
 */
use \AllowDynamicProperties;

#[AllowDynamicProperties]
class MyClass
{
    /**
     * Dynamic attributes will work with no deprecation warnings
     */
    public function __construct()
    {
        $this->first_name = 'George';
        $this->last_name = 'Orwell';
    }
}

class MyExtendedClass extends MyClass 
{
    /**
     * Even if "MyExtendedClass" is not using #[AllowDynamicProperties], it extends "MyClass", that is using it.
     * Dynamic attributes will work with no deprecation warnings
     */
    public function __construct()
    {
        parent::__construct();
    }
}
```

## Why bother?

> In PHP 8.2 and later, setting a value to an undeclared class property is deprecated, and emits a deprecation notice the first time the property is set during the lifetime of the application execution. **There are legitimate use cases of dynamic properties, such as value objects derived from a dynamic JSON response, or configuration objects that allow arbitrary values.** (reference: [php.watch](https://php.watch/versions/8.2/dynamic-properties-deprecated#AllowDynamicProperties))

IMHO, _Deprecating dynamic properties sucks_. The [votation of _2021-11-26_](https://wiki.php.net/rfc/deprecate_dynamic_properties) finished with a favor of **52** votes, with only **25** that were not convinced (as I am.)

Anyway, the **#[AllowDynamicProperties]** attribute _is_ an effective workaround to let this feature work and not trigger any **E_DEPRECATED** warnings.

**The tests are intended to let the reader know how to implement the #[AllowDynamicProperties] in various scenarios.**

## How to run the test

You'll have to use **PHP 8.2 binary**. Here is the list of steps to run all the tests.

1. `composer install`
2. `/usr/bin/php8.2 test --verbose all` to run all the tests in verbose mode (dumps objects)
3. `/usr/bin/php8.2 test all` to run all the tests
4. `/usr/bin/php8.2 test [deprecated|allowDynamicPropertiesTest1|allowDynamicPropertiesTest1OnExtended|allowDynamicPropertiesTest2|allowDynamicPropertiesTest2OnExtended]` to specifically run a single test

If you only want a **readable, simple, single-file test** that showcases everything, use `showcase.php`:

1. `/usr/bin/php8.2 showcase.php`

## What to expect from the different tests

1. `deprecated`: you **will** get `E_DEPRECATED` warnings
2. `allowDynamicPropertiesTest1`: you **shold not** get `E_DEPRECATED` warnings. Dynamic Properties are set within the class constructor.
3. `allowDynamicPropertiesTest1OnExtended`: you **shold not** get `E_DEPRECATED` warnings. This test extends the `AllowDynamicPropertiesTest1` class.
4. `allowDynamicPropertiesTest2`: you **should not** get `E_DEPRECATED` warnings. Dynamic Properties are set via calling a specific `setProps()` method.
5. `allowDynamicPropertiesTest2OnExtended`: you **should not** get `E_DEPRECATED` warnings. This test extends the `AllowDynamicPropertiesTest2` class.
6. `stackOverflowUser706420Test`: you **will** get `E_DEPRECATED` warnings. This test is intended to demonstrate that [StackOverflow's user706420](https://stackoverflow.com/users/706420/user706420) is not correct in his [comment left here](https://stackoverflow.com/questions/74991682/php-8-2-dynamic-properties-deprecated-how-to-use-them-anyway-in-a-compatible-wa#comment134908738_75039639): in fact, **his statement is true only on non-namespaced contexts**. In a namespaced context, you'll **always** need the fully-qualified `use \AllowDynamicProperties;` statement.