<?php namespace CupOfTea\Xredis;

use CupOfTea\Package\Package;
use Predis\Client as Pclient;

class Xredis extends Pclient
{
    use Package;
    
    /**
     * The available codecs to store and retrieve data from the Redis Client.
     * 
     * @var array
     */
    private $available_codecs = [
        'json',
        'serialize',
    ];
    
    /**
     * The codec instances.
     * 
     * @var array
     */
    private $codecs = [];
    
    /**
     * Get the Json Codec instance.
     * 
     * @return \CupOfTea\Xredis\JClient
     */
    public function json()
    {
        if (! isset($this->codecs['json'])) {
            $this->codecs['json'] = new JClient($this);
        }
        
        return $this->codecs['json'];
    }
    
    /**
     * Get the Serialize Codec instance.
     * 
     * @return \CupOfTea\Xredis\SClient
     */
    public function serialize()
    {
        if (! isset($this->codecs['serialize'])) {
            $this->codecs['serialize'] = new SClient($this);
        }
        
        return $this->codecs['serialize'];
    }
    
    /**
     * Dynamically make a Redis command.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        foreach ($this->available_codecs as $name) {
            if (preg_match('/^' . $name . '_?(.*)/i', $method, $matches)) {
                $codec = $this->$name();
                $method = $matches[1];
                
                return call_user_func_array([$codec, $method], $parameters);
            }
        }
        
        return parent::__call($method, $parameters);
    }
}
