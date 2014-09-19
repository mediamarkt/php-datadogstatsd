<?php

use \DataDog\StatsD\Messenger\Http\Socket;

class NotifyHttpSocketTest extends \PHPUnit_Framework_TestCase
{
    private $appKey = '';
    private $apiKey = '';

    /** @var DataDog\StatsD\Notifier */
    private $notifier;

    public function __construct()
    {
        parent::__construct();

        $this->notifier = new \DataDog\StatsD\Notifier(new Socket(), $this->apiKey, $this->appKey);
    }

    public function testMetrics()
    {
//        $this->notifier->metric('multi_client.test.metric', [time(), 155], ['multi_client:test']);
    }

} 