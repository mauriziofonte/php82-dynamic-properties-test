# PHP 8.2 [AllowDynamicProperties] showcase

This utility is intended to showcase what's contained in the [PHP 8.2 AllowDynamicProperties RFC](https://php.watch/versions/8.2/dynamic-properties-deprecated#AllowDynamicProperties).

## Why

> In PHP 8.2 and later, setting a value to an undeclared class property is deprecated, and emits a deprecation notice the first time the property is set during the lifetime of the application execution. **There are legitimate use cases of dynamic properties, such as value objects derived from a dynamic JSON response, or configuration objects that allow arbitrary values.**

Deprecating dynamic properties sucks. The **#[AllowDynamicProperties]** class attribute _should_ be a workaround to let this feature work and not trigger any **E_DEPRECATED** warnings.

## How to run the test

You'll have to use **PHP 8.2 binary**.

1. `composer install`
2. `/usr/bin/php8.2 test --verbose all` to run all the tests

## What to expect from the test

1. `deprecated`: you **will** get `E_DEPRECATED` warnings
2. `magicSetter`: you **will** get `E_DEPRECATED` warnings
3. `badImplementation`: you **will** get `E_DEPRECATED` warnings
4. `workingImplementation`: you **should not** get `E_DEPRECATED` warnings
5. `extendedClass`: you **should not** get `E_DEPRECATED` warnings
6. `usingTrait`: you **should not** get `E_DEPRECATED` warnings