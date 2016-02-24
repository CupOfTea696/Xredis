<?php

use Xredis\Client;
use Predis\Command\StringGet;

class ClientTest extends PHPUnit_Framework_TestCase
{
    public function test_encodes_set_value()
    {
        $data = 'bar';
        
        $redis = $this->getRedis('SET');
        $redis->expects($this->once())
            ->method('SET')
            ->with(
            $this->equalTo('foo'),
            $this->equalTo('ENC:' . $data)
        );
        
        $client = $this->getClient($redis);
        $client->set('foo', $data);
    }
    
    public function test_decodes_get_response()
    {
        $response = 'ENC:bar';
        
        $redis = $this->getRedis('GET');
        $redis->expects($this->once())
            ->method('GET')
            ->with($this->equalTo('foo'))
            ->willReturn($response);
        
        $client = $this->getClient($redis);
        $data = $client->get('foo');
        
        $this->assertEquals('bar', $data);
    }
    
    public function test_decodes_array_reponses()
    {
        $response = [
            'ENC:bar',
            'ENC:baz',
        ];
        
        $redis = $this->getRedis('SMEMBERS');
        $redis->expects($this->once())
            ->method('SMEMBERS')
            ->with($this->equalTo('mySet'))
            ->willReturn($response);
        
        $client = $this->getClient($redis);
        $data = $client->smembers('mySet');
        
        $this->assertEquals(array_map(function($v) {
            return str_replace('ENC:', '', $v);
        }, $response), $data);
    }
    
    public function test_create_command_encodes_set_value()
    {
        $data = 'bar';
        
        $redis = $this->getRedis('SET');
        
        $client = $this->getClient($redis);
        $cmd = $client->createCommand('set', ['foo', $data]);
        
        $this->assertEquals(['foo', 'ENC:' . $data], $cmd->getArguments());
    }
    
    public function test_execute_command_decodes_get_value()
    {
        $response = 'ENC:bar';
        $cmd = new StringGet;
        
        $redis = $this->getRedis('executeCommand');
        $redis->expects($this->once())
            ->method('executeCommand')
            ->with($this->equalTo($cmd))
            ->willReturn($response);
        
        $client = $this->getClient($redis);
        $data = $client->executeCommand($cmd);
        
        $this->assertEquals('bar', $data);
    }
    
    protected function getRedis($methods = [])
    {
        if (is_string($methods)) {
            $methods = [$methods];
        }
        
        $methods = array_map('strtoupper', $methods);
        
        return $this->getMockBuilder('Predis\Client')
            ->setMethods($methods)
            ->getMock();
    }
    
    protected function getClient($redis)
    {
        $client = $this->getMockBuilder(Client::class)
            ->setConstructorArgs([$redis])
            ->setMethods(['encode', 'decode'])
            ->getMock();
        
        $client->method('encode')
            ->will($this->returnCallback(function($v) {
                return 'ENC:' . $v;
            }));
        
        $client->method('decode')
            ->will($this->returnCallback(function($v) {
                return str_replace('ENC:', '', $v);
            }));
        
        return $client;
    }
}
