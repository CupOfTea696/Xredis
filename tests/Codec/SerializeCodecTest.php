<?php

use Xredis\Codec\SerializeCodec;

require_once __DIR__.'/stubs/Codec.php';

class SerializeCodecTest extends PHPUnit_Framework_TestCase
{
    use CodecRetriever;
    
    public function test_encode()
    {
        $data = ['foo' => 'bar'];
        $codec = $this->getCodec(SerializeCodec::class);
        
        $this->assertEquals(serialize($data), $codec->encode($data));
    }
    
    public function test_decode()
    {
        $data = serialize(['foo' => 'bar']);
        $codec = $this->getCodec(SerializeCodec::class);
        
        $this->assertEquals(unserialize($data), $codec->decode($data));
    }
}
