<?php

use Xredis\JClient;
use Xredis\SClient;
use Xredis\Xredis;

class XredisTest extends PHPUnit_Framework_TestCase
{
    public function test_get_json_client()
    {
        $x = new Xredis;
        
        $this->assertInstanceOf(JClient::class, $x->json());
    }
    
    public function test_get_serialize_client()
    {
        $x = new Xredis;
        
        $this->assertInstanceOf(SClient::class, $x->serialize());
    }
    
    /**
     * @depends ClientTest::test_decodes_get_response
     */
    public function test_uses_codec_for_magic_call()
    {
        $response = json_encode(['bar' => 'baz']);
        
        $x = $this->getMockBuilder(Xredis::class)
            ->setMethods(['GET'])
            ->getMock();
        
        $x->expects($this->exactly(2))
            ->method('GET')
            ->with($this->equalTo('foo'))
            ->willReturn($response);
        
        $data1 = $x->jsonGet('foo');
        $data2 = $x->json_GET('foo');
        
        $this->assertEquals($data1, json_decode($response));
        $this->assertEquals($data2, json_decode($response));
    }
}
