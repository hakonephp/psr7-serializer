# PSR-7 Serializer 🏃‍♀️

This package provides functionality to persist [PSR-7] HTTP Responses based on and [PSR-17].

This package is designed to serialize within the PSR-7 standard. The data does not have the pre-serialized class name, so the restored class depends only on the PSR-17 Factory object passed to `unserializePsr7()`.

## Install

```
# minimum requirements when your project already has any HTTP library installed
composer require hakone/psr7-serializer

# for auto-detecting HTTP libraries installed in your project
composer require hakone/psr7-serializer php-http/discovery
```

[HTTPlug Discovery (`php-http/discovery`)][php-http/discovery] is a package useful for auto-discovering classes provided by installed HTTP libraries.

If you haven't installed any HTTP factory implementations yet: consider installing one of [`nyholm/psr7`][nyholm/psr7], [`guzzlehttp/psr7`][guzzlehttp/psr7], or [`laminas/laminas-diactoros`][laminas/laminas-diactoros].

## Usage

```php
<?php

use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use function Hakone\Psr7Serializer\serializePsr7;
use function Hakone\Psr7Serializer\unserializeResponse;

// 1. Send HTTP Request
$http_client = Psr18ClientDiscovery::find();
$response = $client->sendRequest(
    Psr17FactoryDiscovery::findRequestFactory()
        ->createRequest('GET', 'http://httpbin.org/get?foo=bar')
);

// 2. Serialize and cache HTTP response
$serialized_data = serializePsr7($serializer->serializeResponse($response));
file_put_contents(__DIR__ . '/cache.txt', $serialized_data);

// 3. Load and unserialize HTTP response
$loaded_serialized_data = file_get_contents(__DIR__ . '/cache.txt');
$unserialized_response = unserializePsr7($loaded_serialized_data);
```

If you don't have HTTPlug Discovery installed, you must explicitly pass an instance of HTTP Factory to unserialize HTTP messages:

```php
use Nyholm\Psr7\Factory\Psr17Factory;

$psr17Factory = new Psr17Factory();
$unserialized_response = unserializePsr7($loaded_serialized_data, $psr17Factory, $psr17Factory);
```

```php
use Laminas\Diactoros\{ResponseFactory, StreamFactory};

$unserialized_response = unserializePsr7($loaded_serialized_data, new ResponseFactory(), new StreamFactory());
```

## Copyright

```
Copyright 2023 USAMI Kenta <tadsan@zonu.me>

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
```

[PSR-17]: https://www.php-fig.org/psr/psr-17/
[PSR-7]: https://www.php-fig.org/psr/psr-7/
[Relay]: https://relayphp.com/
[guzzlehttp/psr7]: https://github.com/guzzle/psr7
[laminas/laminas-diactoros]: https://docs.laminas.dev/laminas-diactoros/
[nyholm/psr7]: https://github.com/Nyholm/psr7
[php-http/discovery]: https://github.com/php-http/discovery
