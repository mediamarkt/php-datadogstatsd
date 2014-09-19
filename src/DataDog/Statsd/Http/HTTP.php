<?php

namespace DataDog\StatsD;

class HTTP
{

    public static function post($url, $body)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: application/json"));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $responseBody = curl_exec($ch);

        if ($responseBody === false) {
            throw new \Exception('Cannot connect. '.curl_error($ch));
        }

        curl_close($ch);

        return $responseBody;
    }

    /*
     * { "series" :
         [{"metric":"test.metric",
          "points":[[1346340794, 20]],
          "type":"gauge",
          "host":"test.example.com",
          "tags":["environment:test"]}
        ]
    }
     */

}
