# Container middleware resolver

This package provides a resolver producing Psr-15 middleware from class names using a Psr-11 container.

**Require** php >= 7.1

**Installation** `composer require ellipse/middleware-container`

**Run tests** `./vendor/bin/kahlan`

- [Using the container middleware resolver](#using-the-container-middleware-resolver)

## Using the container middleware resolver

Please note the middleware instances are resolved using [ellipse/container-reflection](https://github.com/ellipsephp/container-reflection) auto-wiring feature.

Also when instances of `Psr\Http\Message\ServerRequestInterface` or `Interop\Http\Server\RequestHandlerInterface` are needed to build a middleware instance, the one received by its `->process()` method are injected.

```php
<?php

namespace App;

use Some\Psr11Container;

use Ellipse\Middleware\ContainerResolver;

use App\Middleware\SomeMiddleware;

// Get a Psr-11 container.
$container = new Psr11Container;

// Create a resolver with the Psr-11 container and a delegate for non middleware class name elements.
$resolver = new ContainerResolver($container, function ($element) {

    // $element is not a middleware class name, just return it.

    return $element;

});

// Produce a Psr-15 middleware from a middleware class name.
$middleware = $resolver(SomeMiddleware::class);
```
