<?php

use Xredis\JClient;

class JClientTest extends PHPUnit_Framework_TestCase
{
    public function test_encodes_set_value_to_json()
    {
        $data = ['bar' => 'baz'];
        
        $client = $this->getClient('SET');
        $client->expects($this->once())
            ->method('SET')
            ->with(
                $this->equalTo('foo'),
                $this->equalTo(json_encode($data))
            );
        
        $j = new JClient($client);
        $j->set('foo', $data);
    }
    
    public function test_decodes_get_response()
    {
        $response = json_encode(['bar' => 'baz']);
        
        $client = $this->getClient('GET');
        $client->expects($this->once())
            ->method('GET')
            ->with($this->equalTo('foo'))
            ->willReturn($response);
        
        $j = new JClient($client);
        $data = $j->get('foo');
        
        $this->assertEquals($data, json_decode($response));
    }
    
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
    
    public function test_decodes_array_reponses()
    {
        $response = [
            json_encode(['bar' => 'baz']),
            json_encode(['hello' => 'world']),
        ];
        
        $client = $this->getClient('SMEMBERS');
        $client->expects($this->once())
            ->method('SMEMBERS')
            ->with($this->equalTo('mySet'))
            ->willReturn($response);
        
        $j = new JClient($client);
        $data = $j->smembers('mySet');
        
        $this->assertEquals($data, array_map('json_decode', $response));
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