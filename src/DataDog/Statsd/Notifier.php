<?php

namespace DataDog\StatsD;

use DataDog\StatsD\Messenger\MessengerInterface;

/**
 * DataDog implementation of StatsD
 *
 * @author https://github.com/jbarciauskas
 * @author Alex Corley <anthroprose@gmail.com>
 * @author Aleksei Novitskiy <novitskiy.aleksei@gmail.com>
 */
class Notifier
{
    public $dataDogHost;
    private $eventUrl = '/api/v1/events';
    private $apiKey;
    private $applicationKey;
    /** @var MessengerInterface */
    private $messenger;

    public function __construct(MessengerInterface $messenger, $apiKey, $applicationKey, $dataDogHost = 'app.datadoghq.com')
    {
        $this->apiKey = $apiKey;
        $this->applicationKey = $applicationKey;
        $this->dataDogHost = $dataDogHost;
        $this->messenger = $messenger;
    }

    public function metric($name, array $points, array $tags = [], $host = null, $type = 'gauge')
    {
        // in case if received only one time point
        if (! is_array(reset($points)) ) {
            $points = array($points);
        }

        $series = array(
            'series' =>
                array(
                    array(
                        'metric' => $name,
                        'points' => $points,
                        'type'   => $type,
                        'host'   => $host,
                        'tags'   => $tags
                    )
                )
        );

        $uri = "/api/v1/series" . '?' . http_build_query(array('api_key' => $this->apiKey));

        $this->messenger->send($this->dataDogHost, $uri, json_encode($series));
    }
} 