<?php namespace CupOfTea\Xredis\Codec;

trait SerializeCodec
{
    /**
     * Encode an argument.
     *
     * @param  mixed  $v
     * @return string
     */
    protected function encode($v)
    {
        return serialize($v);
    }
    
    /**
     * Decode a response.
     *
     * @param  string  $v
     * @return mixed
     */
    protected function decode($v)
    {
        return unserialize($v);
    }
}
