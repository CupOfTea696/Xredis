<?php namespace Xredis;

use Xredis\Codec\Codec;
use Predis\Client as Pclient;

abstract class Client
{
    use Codec;
    
    protected $client;
    
    public function __construct(Pclient $client)
    {
        $this->client = $client;
    }
    
    /**
     * Call the client.
     * 
     * @param  string  $method
     * @param  array  $parameters
     */
    protected function callClient($method, $parameters)
    {
        $this->client->__call($method, $parameters);
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
        return $this->process($method, $parameters);
    }
}
