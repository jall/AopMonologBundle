JallAopMonologBundle
====================

[![Latest Stable Version](https://poser.pugx.org/jall/aop-monolog-bundle/v/stable)](https://packagist.org/packages/jall/aop-monolog-bundle)
[![Total Downloads](https://poser.pugx.org/jall/aop-monolog-bundle/downloads)](https://packagist.org/packages/jall/aop-monolog-bundle)
[![License](https://poser.pugx.org/jall/aop-monolog-bundle/license)](https://packagist.org/packages/jall/aop-monolog-bundle)
[![Build Status](https://travis-ci.org/jall/AopMonologBundle.svg?branch=master)](https://travis-ci.org/jall/AopMonologBundle)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/4b8fba47-71ab-4d62-9759-8103b977ff2c/mini.png)](https://insight.sensiolabs.com/projects/4b8fba47-71ab-4d62-9759-8103b977ff2c)

A Symfony bundle to provide some very simple AOP logging functionality by building on top of the [GoAopBundle][1] and 
[MonologBundle][2].

It allows tagging public and protected methods in any class with annotations that write a log (via Monolog) every time 
the method is called.

Installation
------------

See the [installation documentation][3].

Usage
-----

See the [usage documentation][4].

Testing
-------

```
./vendor/bin/phpunit
```

Further reading
---------------

More information on Go AOP! and Aspect Oriented Programming (AOP) in general can be found on [Go! AOP's website][5].

The Symfony cookbook has many useful articles on [configuring Monolog][6].

License
-------

This bundle is under the [MIT license][7].

[1]: https://github.com/goaop/goaop-symfony-bundle
[2]: https://github.com/symfony/monolog-bundle
[3]: https://github.com/jall/AopMonologBundle/blob/master/Resources/doc/install.md
[4]: https://github.com/jall/AopMonologBundle/blob/master/Resources/doc/usage.md
[5]: http://go.aopphp.com/docs/
[6]: http://symfony.com/doc/current/logging.html
[7]: https://github.com/jall/AopMonologBundle/blob/master/LICENSE
