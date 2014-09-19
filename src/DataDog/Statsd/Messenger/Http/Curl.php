<?php

namespace DataDog\StatsD\Messenger\Http;

use DataDog\StatsD\Messenger\MessengerInterface;

class Curl implements MessengerInterface
{

    public function send($host, $uri, $body)
    {
        $ch = curl_init();

        $url = "https://" . $host . $uri;

        curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: application/json"));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $responseBody = curl_exec($ch);

        if ($responseBody === false) {
            error_log('Cannot connect. '.curl_error($ch));
        }

        $info = curl_getinfo($ch);
        if (isset($info['http_code']) && $info['http_code'] != 202) {
            error_log($info['http_code'] . ' code.  Datadog server did not respond with success status code. Stat may not be sent');
        }

        curl_close($ch);

        return $responseBody;
    }

}
