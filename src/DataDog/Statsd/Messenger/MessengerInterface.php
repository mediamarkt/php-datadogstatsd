<?php

namespace DataDog\StatsD\Messenger;

interface MessengerInterface {

    /**
     * @param string $host Usually "app.datadoghq.com"
     * @param string $uri  API path, like "/api/v1/series"
     * @param string $body Encoded parameters to send
     * @return mixed
     */
    public function send($host, $uri, $body);

} 