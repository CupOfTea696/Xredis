<?php namespace Xredis\Codec;

trait Codec
{
    private $storeEncoded = [
        'getset' => ['from' => 1],
        'linsert' => ['from' => 3],
        'lpush' => ['from' => 1],
        'lpushx' => ['from' => 1],
        'lrem' => ['from' => 2],
        'lset' => ['from' => 2],
        'psetex' => ['from' => 2],
        'rpush' => ['from' => 1],
        'rpushx' => ['from' => 1],
        'sadd' => ['from' => 1],
        'set' => ['from' => 1, 'to' => 1],
        'setex' => ['from' => 2],
        'setrange' => ['from' => 1],
        'setex' => ['from' => 2],
        'smove' => ['from' => 2],
        'srem' => ['from' => 1],
        'zrank' => ['from' => 1],
        'zrem' => ['from' => 1],
        'zrevrank' => ['from' => 1],
        'zscore' => ['from' => 1],
    ];
    
    private $returnDecoded = [
        'get',
        'getset',
        'lindex',
        'lpop',
        'lrange',
        'mget',
        'rpop',
        'sdiff',
        'sinter',
        'smembers',
        'spop',
        'srandmember',
        'sunion',
    ];
    
    /**
     * Run a command against the Redis database.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    private function process($method, array $parameters = [])
    {
        $parameters = $this->encodeParametersForCommand($parameters);
        $result = $this->callClient($method, $parameters);
        
        return $this->decodeParametersForCommand($method, $result);
    }
    
    final protected function encodeParametersForCommand($commandID, $parameters)
    {
        if (in_array(strtolower($commandID), array_keys($this->storeEncoded))) {
            $values = $this->storeEncoded[strtolower($commandID)];
            
            $from = array_get($values, 'from', 0);
            $to = array_get($values, 'to', max(0, count($parameters) -1));
            
            for ($i = $from; $i <= $to; $i++) {
                $parameters[$i] = $this->encode($parameters[$i]);
            }
        }
        
        if ($commandID == 'mset' || $commandID == 'msetnx') {
            if (count($parameters) === 1 && is_array($parameters[0])) {
                foreach ($parameters[0] as $k => $v) {
                    $parameters[0][$k] = $this->encode($v);
                }
            } else {
                foreach ($parameters as $k => $v) {
                    if ($k % 2 != 0) {
                        $parameters[$k] = $this->encode($v);
                    }
                }
            }
        }
        
        if ($commandID == 'zadd') {
            if (is_array(end($parameters))) {
                foreach (array_pop($parameters) as $k => $v) {
                    $parameters[][$k] = $this->encode($v);
                }
            } else {
                foreach ($parameters as $k => $v) {
                    if ($k !== 0 && $k % 2 == 0) {
                        $parameters[$k] = $this->encode($v);
                    }
                }
            }
        }
        
        return $parameters;
    }
    
    final protected function decodeParametersForCommand($commandID, $result)
    {
        if (in_array(strtolower($method), $this->returnDecoded)) {
            $result = $this->callClient($method, $parameters);
            
            if (is_array($result)) {
                return array_map(function($value) {
                    return $this->decode($value);
                }, $result);
            }
            
            return $this->decode($result);
        }
        
        return $result;
    }
    
    /**
     * Call the client.
     * 
     * @param  string  $method
     * @param  array  $parameters
     */
    abstract protected function callClient($method, $parameters);
    
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
