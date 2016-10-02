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

See the full [installation documentation][3].

Usage
-----

### Code ###

After setting up the correct location(s) to be scanned by Go! AOP, logging can be added to any public or protected 
function in the scanned location(s) simply by tagging the method with an annotation in the function DocBlock.

This example records all logins by attaching the 'Log' annotation to the controller action responsible for
logging in.

```php
<?php
// src/AppBundle/Controller/LoginController.php

// ...
use Jall\AopMonologBundle\Annotation\Log;

class LoginController extends Controller
{

    /**
     * @Log(
     *      message = "User {{ user.name }} has logged in",
     *      level = "info",
     *      channel = "user",
     *      context = {
     *          "user.name": "input['user'].getUserName()",
     *      },
     * )
     */
    public function loginAction(User $user)
    {
        // ... 
    }
    
}
```

Sample log output:
> [2016-10-01 12:00:00] user.INFO: User {{ user.name }} has logged in {"user.name": "admin"} []

This example records payment errors by attaching the 'LogException' annotation to the service method 
responsible for payments.

```php
<?php
// src/AppBundle/Service/PaymentProcessor.php

// ...
use Jall\AopMonologBundle\Annotation\LogException;

class PaymentProcessor
{

    /**
     * @LogException(
     *      message = "Invoice {{ invoice.id }} payment failed as payment method {{ paymentMethod.id }} has expired.",
     *      level = "error",
     *      channel = "payment",
     *      context = {
     *          "invoice.id": "input['invoice'].getId()",
     *          "paymentMethod.id": "input['paymentMethod'].getId()",
     *          "paymentMethod.expiryDate": "input['paymentMethod'].getExpiryDate().format(DATE_ISO8601)",
     *      },
     * )
     */
    public function pay(Invoice $invoice, PaymentMethod $paymentMethod)
    {
        if ($paymentMethod->isExpired()) {
            throw new \InvalidArgumentException("Invoice payment failure: payment method '{$paymentMethod->getId()}' expired on {$paymentMethod->getExpiryDate()->format(DATE_ISO8601)}.");
        }

        // ... 
    }

}
```

Sample log output:
> [2016-10-01 12:00:00] payment.ERROR: Invoice {{ invoice.id }} payment failed as payment method {{ paymentMethod.id }} has expired. {"invoice.id": "243","paymentMethod.id": "87","paymentMethod.expiryDate": "2015-06-21T13:42:00","exception":"[object] (InvalidArgumentException(code: 0): Invoice payment failure: payment method '87' expired on 2015-06-21T13:42:00. at /var/www/my-site/src/AppBundle/Service/PaymentProcessor.php:126"} []

The 'Log' annotation will always trigger when the function it is attached to is called, unless an exception is thrown.
The 'LogException' annotation will only trigger when the function it is attached to throws an exception.

### Annotations ###

Each annotation can be configured with four variables: _message_, _level_, _channel_, and _context_.

#### Message ####

The _message_ variable is the primary log message and is a string.

In the examples above there are placeholder variables inside this string (e.g. "{{ user.name }}" and "{{ invoice.id }}"); 
these are NOT replaced by the context variables by this bundle. 
If this behaviour is desired, a Monolog processor can be added to perform this replacement.

#### Level ####

The _level_ variable is one of the [PSR-3 log levels][5] and is a string.

It defaults to 'info'. 

#### Channel ####

The _channel_ variable is the channel the log will be written to and is a string.

It can be any configured channel in Monolog, but the channel must be configured manually; 
this bundle will not create Monolog channels on the fly for any given string.

It defaults to 'php'.

#### Context ####

The _context_ variable is an object containing an string representation of the data that should be parsed & stored 
alongside the message. It defaults to '{}'.

It uses the Symfony [expression language][5] on the annotation strings to convert them into useful data to be stored.

There are two special provided variables that can be used in context strings: 'input' and 'output'.

'input' is an associative array of the function parameters; keys are the parameter names and values are what was 
provided to that parameter in the current function call.

'output' is the return value of the function being called (if it had one).

For the 'LogException' annotation an extra variable 'exception' is automatically included in the context, which holds 
the __toString value of the exception that was thrown.

Further reading
---------------

More information on Go AOP! and Aspect Oriented Programming (AOP) in general can be found on [Go! AOP's website][6].

The Symfony cookbook has many useful articles on [configuring Monolog][7].

License
-------

This bundle is under the [MIT license][8].

[1]: https://github.com/goaop/goaop-symfony-bundle
[2]: https://github.com/symfony/monolog-bundle
[3]: https://github.com/jall/AopMonologBundle/blob/master/Resources/doc/install.md
[4]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md#5-psrlogloglevel
[5]: http://symfony.com/doc/current/components/expression_language.html
[6]: http://go.aopphp.com/docs/
[7]: http://symfony.com/doc/current/logging.html
[8]: https://github.com/jall/AopMonologBundle/blob/master/LICENSE
