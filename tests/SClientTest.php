<?php

use Xredis\SClient;

class SClientTest extends PHPUnit_Framework_TestCase
{
    public function test_encodes_set_value_to_json()
    {
        $data = ['bar' => 'baz'];
        
        $client = $this->getClient('SET');
        $client->expects($this->once())
            ->method('SET')
            ->with(
            $this->equalTo('foo'),
            $this->equalTo(serialize($data))
        );
        
        $s = new SClient($client);
        $s->set('foo', $data);
    }
    
    public function test_decodes_get_response()
    {
        $response = serialize(['bar' => 'baz']);
        
        $client = $this->getClient('GET');
        $client->expects($this->once())
            ->method('GET')
            ->with($this->equalTo('foo'))
            ->willReturn($response);
        
        $s = new SClient($client);
        $data = $s->get('foo');
        
        $this->assertEquals($data, unserialize($response));
    }
    
    public function test_decodes_array_reponses()
    {
        $response = [
            serialize(['bar' => 'baz']),
            serialize(['hello' => 'world']),
        ];
        
        $client = $this->getClient('SMEMBERS');
        $client->expects($this->once())
            ->method('SMEMBERS')
            ->with($this->equalTo('mySet'))
            ->willReturn($response);
        
        $s = new SClient($client);
        $data = $s->smembers('mySet');
        
        $this->assertEquals($data, array_map('unserialize', $response));
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