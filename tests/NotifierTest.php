<?php

/**
 *
 * Used for measure and development
 *
 * Class NotifierTest
 */
class NotifierTest extends \PHPUnit_Framework_TestCase{

    private $appKey = '';
    private $apiKey = '';

    /** @var DataDog\StatsD\Notifier */
    private $notifier;

    public function __construct()
    {
        parent::__construct();

    }

    public function testTiming()
    {
        //
        return;

        $notifier = new \DataDog\StatsD\Notifier(new \DataDog\StatsD\Messenger\Http\Curl(), $this->apiKey, $this->appKey);

        $mtime = microtime();
        $mtime = explode(" ",$mtime);
        $mtime = $mtime[1] + $mtime[0];
        $starttime = $mtime;

        for ($i = 0; $i < 100; $i++) {
            $notifier->metric('multi_client.test.metric', [time(), 222], ['multi_client:test']);
        }

        $mtime = microtime();
        $mtime = explode(" ",$mtime);
        $mtime = $mtime[1] + $mtime[0];
        $connect = $mtime;
        $curlTime = ($connect - $starttime);
        echo "curl time is ".$curlTime." seconds \n";

        ////

        $notifier = new \DataDog\StatsD\Notifier(new \DataDog\StatsD\Messenger\Http\Socket(), $this->apiKey, $this->appKey);

        $mtime = microtime();
        $mtime = explode(" ",$mtime);
        $mtime = $mtime[1] + $mtime[0];
        $starttime = $mtime;

        for ($i = 0; $i < 100; $i++) {
            $notifier->metric('multi_client.test.metric', [time(), 111], ['multi_client:test']);
        }

        $mtime = microtime();
        $mtime = explode(" ",$mtime);
        $mtime = $mtime[1] + $mtime[0];
        $connect = $mtime;
        $curlTime = ($connect - $starttime);
        echo "socket time is ".$curlTime." seconds \n";
    }

    public function testEvent()
    {
//        $this->notifier->event('test.event', ['foo' => 'bar']);
    }

} 