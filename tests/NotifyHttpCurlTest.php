<?php

use \DataDog\StatsD\Messenger\Http\Curl;

/**
 *
 * Used for measure and development
 *
 * Class NotifyHttpCurlTest
 */
class NotifyHttpCurlTest extends \PHPUnit_Framework_TestCase{

    private $appKey = '';
    private $apiKey = '';

    /** @var DataDog\StatsD\Notifier */
    private $notifier;

    public function __construct()
    {
        parent::__construct();

        $this->notifier = new \DataDog\StatsD\Notifier(new Curl(), $this->apiKey, $this->appKey);
    }

    public function testMetrics()
    {
//        $this->notifier->metric('multi_client.test.metric', [time(), 155], ['multi_client:test']);
    }

} 