<?php

namespace DataDog\StatsD\Http;


class Socket {

    public function post()
    {
        $fp = pfsockopen("app.datadoghq.com", 80, $errno, $errstr, 30);

        if (!$fp) {
            echo "$errstr ($errno)<br />\n";
        } else {
            $i = 0;
            while($i < 2000) {
                $out = "GET / HTTP/1.1\r\n";
                $out .= "Host: www.example.com\r\n";
                $out .= "Connection: Close\r\n\r\n";
                fwrite($fp, $out);

                $i++;
            }
//            while (!feof($fp)) {
//                echo fgets($fp, 128);
//            }
//            fclose($fp);
        }
    }

} 