<?php

namespace Bernard\Tests\Connection;

use Bernard\Connection\PredisConnection;

class PredisConnectionTest extends PhpRedisConnectionTest
{
    public function setUp()
    {
        // Because predis uses __call all methods that needs mocking must be
        // explicitly defined.
        $this->redis = $this->getMock('Predis\Client', array(
            'lLen',
            'sMembers',
            'lRange',
            'blPop',
            'sRemove',
            'del',
            'sAdd',
            'sContains',
            'rPush',
            'sRem',
        ));

        $this->connection = new PredisConnection($this->redis);
    }

    public function testItPopMessages()
    {
        $this->redis->expects($this->at(0))->method('blPop')->with($this->equalTo('queue:send-newsletter'))
            ->will($this->returnValue(array('my-queue', 'message1')));

        $this->redis->expects($this->at(1))->method('blPop')->with($this->equalTo('queue:ask-forgiveness'), $this->equalTo(30))
            ->will($this->returnValue(array('my-queue2', 'message2')));

        $this->assertEquals('message1', $this->connection->popMessage('send-newsletter'));
        $this->assertEquals('message2', $this->connection->popMessage('ask-forgiveness', 30));
    }
}
