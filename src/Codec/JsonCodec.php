<?php namespace CupOfTea\Xredis\Codec;

trait JsonCodec
{
    /**
     * Whether the content should be decoded to an associative array.
     * 
     * @var bool
     */
    private $_decode_assoc = false;
    
    /**
     * Set whether the content should be decoded to an associative array.
     * @param  bool  $assoc
     * @return void
     */
    public function decode_assoc($assoc)
    {
        $this->_decode_assoc = $assoc;
    }
    
    /**
     * Encode an argument.
     * 
     * @param  mixed  $v
     * @return string
     */
    protected function encode($v)
    {
        return json_encode($v);
    }
    
    /**
     * Decode a response.
     * 
     * @param  string  $v
     * @return mixed
     */
    protected function decode($v)
    {
        return json_decode($v, $this->_decode_assoc);
    }
}
