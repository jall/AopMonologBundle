Installation
============

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require jall/aop-monolog-bundle "~1.0.0"
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Step 2: Enable the Bundle
-------------------------

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project.

The [MonologBundle][1] and [GoAopBundle][2] are REQUIRED dependencies of this bundle. They must also be enabled (per 
their own installation instructions). In particular, note that the GoAopBundle MUST be the very first bundle loaded.

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // This must go before any other bundles.
            new Go\Symfony\GoAopBundle\GoAopBundle(),
            
            // ...
            
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new \Jall\AopMonologBundle\JallAopMonologBundle(),
        );

        // ...
    }

    // ...
}
```

Step 2: Configure the Bundle
----------------------------

Finally, ensure that any locations you wish to use this bundle's logging annotations in are
part of GoAop's include_paths. The simplest use case is to include the entire src directory of your app.

```yaml
# app/config/config.yml 

go_aop:
    options:
        # ...
        include_paths:
            - "%kernel.root_dir%/../src/"
```

[1]: https://github.com/goaop/goaop-symfony-bundle
[2]: https://github.com/symfony/monolog-bundle
