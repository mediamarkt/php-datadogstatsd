# PHP DataDog StatsD Client

This is an PHP [datadogstatsd](http://www.datadoghq.com/) client


## Installation

Clone repository at [github.com/mediamarkt/php-datadogstatsd](https://github.com/mediamarkt/php-datadogstatsd)

or via composer:

`
"repositories": [
        {
            "type": "git",
            "url":  "git@github.com:mediamarkt/php-datadogstatsd.git"
        }
    ],
`
## Usage

To send metric:

``` php

// send via curl:
$messenger = new \DataDog\StatsD\Messenger\Http\Curl();
$notifier = new \DataDog\StatsD\Notifier($messenger, "apiKey", "appKey");

// or via php socket:
$messenger = new \DataDog\StatsD\Messenger\Http\Socket();
$notifier = new \DataDog\StatsD\Notifier($messenger, "apiKey", "appKey");


// send metric
$notifier->metric('multi_client.test.metric', [time(), 155], ['multi_client:test']);
```

With multiple requests better to use socket connection. You can see tests to get time measurements.

## Author

Aleksei Novitskiy - novitskiy.aleksei@gmail.com
Alex Corley - anthroprose@gmail.com