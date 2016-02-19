<?php namespace Xredis\Codec;

trait SerializeCodec
{
    protected function encode($v)
    {
        return serialize($v);
    }
    
    protected function decode($v)
    {
        return unserialize($v);
    }
}
