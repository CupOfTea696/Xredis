<?php namespace Xredis\Codec;

trait JsonCodec
{
    private $_decode_assoc = false;
    
    public function decode_assoc($assoc)
    {
        $this->_decode_assoc = $assoc;
    }
    
    protected function encode($v)
    {
        return json_encode($v);
    }
    
    protected function decode($v)
    {
        return json_decode($v, $this->_decode_assoc);
    }
}
