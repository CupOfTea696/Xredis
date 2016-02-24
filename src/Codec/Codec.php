<?php namespace Xredis\Codec;

trait Codec
{
    private $storeEncoded = [
        'GETSET' => ['from' => 1],
        'LINSERT' => ['from' => 3],
        'LPUSH' => ['from' => 1],
        'LPUSHX' => ['from' => 1],
        'LREM' => ['from' => 2],
        'LSET' => ['from' => 2],
        'PSETEX' => ['from' => 2],
        'RPUSH' => ['from' => 1],
        'RPUSHX' => ['from' => 1],
        'SADD' => ['from' => 1],
        'SET' => ['from' => 1, 'to' => 1],
        'SETEX' => ['from' => 2],
        'SETRANGE' => ['from' => 1],
        'SETEX' => ['from' => 2],
        'SMOVE' => ['from' => 2],
        'SREM' => ['from' => 1],
        'ZRANK' => ['from' => 1],
        'ZREM' => ['from' => 1],
        'ZREVRANK' => ['from' => 1],
        'ZSCORE' => ['from' => 1],
    ];
    
    private $returnDecoded = [
        'GET',
        'GETSET',
        'LINDEX',
        'LPOP',
        'LRANGE',
        'MGET',
        'RPOP',
        'SDIFF',
        'SINTER',
        'SMEMBERS',
        'SPOP',
        'SRANDMEMBER',
        'SUNION',
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
        $commandID = strtoupper($method);
        
        $parameters = $this->encodeParametersForCommand($commandID, $parameters);
        $response = $this->callClient($commandID, $parameters);
        
        return $this->decodeResponseForCommand($commandID, $response);
    }
    
    final protected function encodeParametersForCommand($commandID, $parameters)
    {
        if (in_array($commandID, array_keys($this->storeEncoded))) {
            $values = $this->storeEncoded[$commandID];
            
            $from = isset($values['from']) ? $values['from'] : 0;
            $to = isset($values['to']) ? $values['to'] : max(0, count($parameters) -1);
            
            for ($i = $from; $i <= $to; $i++) {
                $parameters[$i] = $this->encode($parameters[$i]);
            }
        }
        
        if ($commandID == 'MSET' || $commandID == 'MSETNX') {
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
        
        if ($commandID == 'ZADD') {
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
    
    final protected function decodeResponseForCommand($commandID, $response)
    {
        if (in_array($commandID, $this->returnDecoded)) {
            if (is_array($response)) {
                return array_map(function($value) {
                    return $this->decode($value);
                }, $response);
            }
            
            return $this->decode($response);
        }
        
        return $response;
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
