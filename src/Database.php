<?php namespace Xredis;

use Xredis\Codec\Codec;
use Illuminate\Redis\Database as IlluminateDatabase;

abstract class Database extends IlluminateDatabase
{
    use Codec;
    
    /**
     * Call the client.
     * 
     * @param  string  $method
     * @param  array  $parameters
     */
    protected function callClient($method, $parameters)
    {
        return $this->command($method, $parameters);
    }
}
