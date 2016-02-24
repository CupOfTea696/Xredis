<?php

class Codec
{
    public function __construct($codec, $encode, $decode)
    {
        $this->codec = $codec;
        $this->encode = $encode;
        $this->decode = $decode;
    }
    
    public function encode()
    {
        return $this->encode->invokeArgs($this->codec, func_get_args());
    }
    
    public function decode()
    {
        return $this->decode->invokeArgs($this->codec, func_get_args());
    }
}

trait CodecRetriever
{
    protected function getCodec($codec)
    {
        if (! isset($this->codec)) {
            $codec = $this->getMockForTrait($codec);
            
            $r = new ReflectionClass(get_class($codec));
            
            $encode = $r->getMethod('encode');
            $decode = $r->getMethod('decode');
            
            $encode->setAccessible(true);
            $decode->setAccessible(true);
            
            $this->codec = new Codec($codec, $encode, $decode);
        }
        
        return $this->codec;
    }
}
