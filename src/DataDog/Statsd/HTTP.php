<?php

namespace BunnyApi\Multiplexer;

class HTTP
{

    public function post($url, $body)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: application/json"));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);


        $logRaw = class_exists('Config') && \Config::get('multiplexer2.log.api.raw_enabled') && class_exists('Log');

        if ($logRaw) {
            \Log::error(
                "Raw Multiplexer request:\n" .
                json_encode(json_decode($body), JSON_PRETTY_PRINT)
            );
        }

        $responseBody = curl_exec($ch);

        if ($responseBody === false) {
            throw new \Exception('Cannot connect to multiplexer: '.curl_error($ch));
        }

        if ($logRaw) {
            \Log::error(
                "Raw Multiplexer response:\n" .
                $responseBody
            );
        }

        curl_close($ch);

        $response = new \stdClass();
        $response->body = $responseBody;

        return $response;
    }

}
