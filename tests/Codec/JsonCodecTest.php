<?php

use Xredis\Codec\JsonCodec;

require_once __DIR__.'/stubs/Codec.php';

class JsonCodecTest extends PHPUnit_Framework_TestCase
{
    use CodecRetriever;
    
    public function test_encode()
    {
        $data = ['foo' => 'bar'];
        $codec = $this->getCodec(JsonCodec::class);
        
        $this->assertEquals(json_encode($data), $codec->encode($data));
    }
    
    public function test_decode()
    {
        $data = json_encode(['foo' => 'bar']);
        $codec = $this->getCodec(JsonCodec::class);
        
        $this->assertEquals(json_decode($data), $codec->decode($data));
    }
}
