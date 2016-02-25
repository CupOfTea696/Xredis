<?php

use CupOfTea\Xredis\JClient;
use Predis\Command\StringGet;

class JClientTest extends PHPUnit_Framework_TestCase
{
    public function test_decodes_get_response_associatively()
    {
        $response = json_encode(['bar' => 'baz']);
        
        $client = $this->getClient('GET');
        $client->expects($this->once())
            ->method('GET')
            ->with($this->equalTo('foo'))
            ->willReturn($response);
        
        $j = new JClient($client);
        $j->decode_assoc(true);
        $data = $j->get('foo');
        
        $this->assertEquals($data, json_decode($response, true));
    }
    
    protected function getClient($methods = [])
    {
        if (is_string($methods)) {
            $methods = [$methods];
        }
        
        $methods = array_map('strtoupper', $methods);
        
        return $this->getMockBuilder('Predis\Client')
            ->setMethods($methods)
            ->getMock();
    }
}
