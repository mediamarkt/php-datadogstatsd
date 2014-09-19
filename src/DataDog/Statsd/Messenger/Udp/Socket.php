<?php

namespace DataDog\StatsD\Messenger\Udp;


class Socket {
    /**
     * Log timing information
     *
     * @param string $metric The metric to in log timing info for.
     * @param float $time The elapsed time (ms) to log
     * @param float $sampleRate the rate (0-1) for sampling.
     * @param array $tags
     */
    public function timing($metric, $time, $sampleRate = 1.0, array $tags = array())
    {
        $this->send(array($metric => "$time|ms"), $sampleRate, $tags);
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
    private function send($data, $sampleRate = 1, array $tags = null)
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
                $value .= '|';
                foreach ($tags as $tag_key => $tag_val) {
                    $value .= '#' . $tag_key . ':' . $tag_val . ',';

                }
                $value = substr($value, 0, -1);
            } elseif (isset($tags) && !empty($tags)) {
                $value .= '|#' . $tags;
            }

            $message = "$stat:$value";
            // Non - Blocking UDP I/O - Use IP Addresses!
            $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
            socket_set_nonblock($socket);
            socket_sendto($socket, $message, strlen($message), 0, $this->server, 8125);
            socket_close($socket);
        }
    }

    /**
     * Gauge
     *
     * @param string $stat The metric
     * @param float $value The value
     * @param int $sampleRate the rate (0-1) for sampling.
     * @param array $tags
     */
    public function gauge($stat, $value, $sampleRate = 1, array $tags = null)
    {
        $this->send(array($stat => "$value|g"), $sampleRate, $tags);
    }

    /**
     * Histogram
     *
     * @param string $stat The metric
     * @param float $value The value
     * @param int $sampleRate the rate (0-1) for sampling.
     * @param array $tags
     */
    public function histogram($stat, $value, $sampleRate = 1, array $tags = null)
    {
        $this->send(array($stat => "$value|h"), $sampleRate, $tags);
    }

    /**
     * Set
     *
     * @param string $stat The metric
     * @param float $value The value
     * @param int $sampleRate the rate (0-1) for sampling.
     * @param array $tags
     */
    public function set($stat, $value, $sampleRate = 1, array $tags = array())
    {
        $this->send(array($stat => "$value|s"), $sampleRate, $tags);
    }

    /**
     * Increments one or more stats counters
     *
     * @param string|array $stats The metric(s) to increment.
     * @param int $sampleRate the rate (0-1) for sampling.
     * @param array $tags
     * @return boolean
     */
    public function increment($stats, $sampleRate = 1, array $tags = array())
    {
        $this->updateStats($stats, 1, $sampleRate, $tags);
    }

    /**
     * Updates one or more stats counters by arbitrary amounts.
     *
     * @param string|array $stats The metric(s) to update. Should be either a string or array of metrics.
     * @param int $delta The amount to increment/decrement each metric by.
     * @param int $sampleRate the rate (0-1) for sampling.
     * @param array|string $tags Key Value array of Tag => Value, or single tag as string
     *
     * @return boolean
     */
    public function updateStats($stats, $delta = 1, $sampleRate = 1, array $tags = array())
    {

        if (!is_array($stats)) {
            $stats = array($stats);
        }

        $data = array();

        foreach ($stats as $stat) {
            $data[$stat] = "$delta|c";
        }

        $this->send($data, $sampleRate, $tags);
    }

    /**
     * Decrements one or more stats counters.
     *
     * @param string|array $stats The metric(s) to decrement.
     * @param int $sampleRate the rate (0-1) for sampling.
     * @param array $tags
     * @return boolean
     */
    public function decrement($stats, $sampleRate = 1, array $tags = array())
    {
        $this->updateStats($stats, -1, $sampleRate, $tags);
    }

    /**
     * Send an event to the Datadog HTTP api. Potentially slow, so avoid
     * making many call in a row if you don't want to stall your app.
     *
     * @param string $title Title of the event
     * @param array $values Optional values of the event. See
     *   http://api.datadoghq.com/events for the valid keys
     * @return null
     **/
    public function event($title, $values = array())
    {
        // Assemble the request
        $values['title'] = $title;
        // Convert a comma-separated string of tags into an array
        if (array_key_exists('tags', $values) && is_string($values['tags'])) {
            $tags = explode(',', $values['tags']);
            $values['tags'] = array();
            foreach ($tags as $tag) {
                $values['tags'][] = trim($tag);
            }
        }

        $body = json_encode($values);

        // Get the url to POST to
        $url = $this->dataDogHost . $this->eventUrl
            . '?api_key=' . $this->apiKey
            . '&application_key=' . $this->applicationKey;

        try {

            $response = HTTP::post($url, $body);

        } catch (HttpException $ex) {

            error_log($ex);
            if (!empty($response)) {
                error_log($response);
            }

        }
    }
} 