# Buggregator client

Buggregator client library for xhprof as PSR-15 middleware.


## Install

```sh
composer require marekskopal/buggregator-client
```

## Usage

Use `XhprofMiddleware` class as PSR-15 middleware.

```php

use MarekSkopal\BuggregatorClient\Middleware;

$xhprofMiddleware = new XhprofMiddleware(
    appName: 'MyApp',
    url: (string) getenv('PROFILER_ENDPOINT'),
));

//e.g. phpleague/route

$router->middleware($xhprofMiddleware);

```
