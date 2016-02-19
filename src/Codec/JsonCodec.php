<?php namespace Xredis\Codec;

trait JsonCodec
{
    protected function encode($v)
    {
        return json_encode($v);
    }
    
    protected function decode($v)
    {
        return json_decode($v);
    }
}
