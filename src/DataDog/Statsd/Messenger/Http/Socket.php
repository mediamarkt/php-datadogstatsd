<?php

namespace DataDog\StatsD\Messenger\Http;

use DataDog\StatsD\Messenger\MessengerInterface;

class Socket implements MessengerInterface
{

    public function send($host, $uri, $body)
    {
        $resource = null;
        $errNum = $errStr = '';

        $openSocket = function() use ($host, &$resource, &$errNum, &$errStr) {
            $resource = pfsockopen("ssl://".$host, 443, $errNum, $errStr, 10);

            return $resource;
        };

        if (!$openSocket()) {
            error_log( "Could not send datadog stat. Socket error: $errStr ($errNum) \n" );

            return false;
        } else {

            $sendRequest = function() use ($uri, $host, $body, &$resource) {
                $out = "POST $uri HTTP/1.1\r\n";
                $out .= "Host: $host\r\n";
                $out .= "Accept: application/json\r\n";
                $out .= "Content-Type: application/json\r\n";
                $out .= "Content-Length: " . strlen($body) . "\r\n";
                $out .= "Connection: Close\r\n\r\n";
                $out .= $body . "\r\n\r\n";

                return fwrite($resource, $out);
            };

            if ($sendRequest() == false) {
                // reopen socket and try to send again
                fclose($resource);
                $resource = null;
                $openSocket();
                $sendRequest();
            }

            return true;
        }
    }

}