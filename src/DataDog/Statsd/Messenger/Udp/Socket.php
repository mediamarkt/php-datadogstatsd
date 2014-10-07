<?php

namespace DataDog\StatsD\Messenger\Udp;

use DataDog\StatsD\Messenger\MessengerInterface;

class Socket implements MessengerInterface
{
    private $server = '127.0.0.1';
    private $port = '8125';

    public function send($host, $uri, $body, $port = 8125)
    {
        $this->server = $host;
        $this->port = $port;

        $data = json_decode($body);
        $data = head($data);

        foreach ($data as $series) {
            foreach ($series->points as $point) {
                $this->flush(
                    $this->getConvertedValue($series->type, $series->metric, $point),
                    1,
                    (array) $series->tags
                );
            }
        }

    }

    public function getConvertedValue($type, $name, $point)
    {
        $result = null;

        switch ($type) {
            case 'timing':
                $p = $point[1] * 100; // milliseconds
                $result = array($name => "$p|ms");
                break;

            // not sure if written below works, because not used
            case 'gauge':
                $result = array($name => "$point[1]|g");
                break;
            case 'histogram':
                $result = array($name => "$point[1]|h");
                break;
            default:
                throw new \Exception("Wrong metric type: ". $type);
        }

        return $result;
    }

    /**
     * Squirt the metrics over UDP
     *
     * @param array $data Incoming Data
     * @param int $sampleRate the rate (0-1) for sampling.
     * @param array|string $tags Key Value array of Tag => Value, or single tag as string
     *
     * @return null
     */
    private function flush($data, $sampleRate = 1, array $tags = null)
    {

        // sampling
        $sampledData = array();

        if ($sampleRate < 1) {

            foreach ($data as $stat => $value) {
                if ((mt_rand() / mt_getrandmax()) <= $sampleRate) {
                    $sampledData[$stat] = "$value|@$sampleRate";
                }
            }

        } else {
            $sampledData = $data;
        }

        if (empty($sampledData)) {
            return;
        }

        foreach ($sampledData as $stat => $value) {

            if ($tags !== NULL && is_array($tags) && count($tags) > 0) {
                $value .= '|#';
                foreach ($tags as $tag_key => $tag_val) {
                    $value .= $tag_key . ':' . $tag_val . ',';

                }
                $value = substr($value, 0, -1);
            } elseif (isset($tags) && !empty($tags)) {
                $value .= '|#' . $tags;
            }

            $message = "$stat:$value";

            // Non - Blocking UDP I/O - Use IP Addresses!
            $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
            socket_set_nonblock($socket);
            socket_sendto($socket, $message, strlen($message), 0, $this->server, $this->port);
            socket_close($socket);
        }
    }
} 