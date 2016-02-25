<?php namespace CupOfTea\Xredis\Codec;

trait Codec
{
    /**
     * Map Commands + Arguments that need to be encoded.
     */
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
    
    /**
     * Map Commands that need to be decoded.
     */
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
     * @param  array  $arguments
     * @return mixed
     */
    private function process($method, array $arguments = [])
    {
        $commandID = strtoupper($method);
        
        $arguments = $this->encodeArgsForCommand($commandID, $arguments);
        $response = $this->callClient($commandID, $arguments);
        
        return $this->decodeResponseForCommand($commandID, $response);
    }
    
    /**
     * Encode the Arguments for a given Command.
     * 
     * @param  string  $commandID
     * @param  array  $arguments
     * @return array
     */
    final protected function encodeArgsForCommand($commandID, $arguments)
    {
        if (in_array($commandID, array_keys($this->storeEncoded))) {
            $values = $this->storeEncoded[$commandID];
            
            $from = isset($values['from']) ? $values['from'] : 0;
            $to = isset($values['to']) ? $values['to'] : max(0, count($arguments) -1);
            
            for ($i = $from; $i <= $to; $i++) {
                $arguments[$i] = $this->encode($arguments[$i]);
            }
        }
        
        if ($commandID == 'MSET' || $commandID == 'MSETNX') {
            if (count($arguments) === 1 && is_array($arguments[0])) {
                foreach ($arguments[0] as $k => $v) {
                    $arguments[0][$k] = $this->encode($v);
                }
            } else {
                foreach ($arguments as $k => $v) {
                    if ($k % 2 != 0) {
                        $arguments[$k] = $this->encode($v);
                    }
                }
            }
        }
        
        if ($commandID == 'ZADD') {
            if (is_array(end($arguments))) {
                foreach (array_pop($arguments) as $k => $v) {
                    $arguments[][$k] = $this->encode($v);
                }
            } else {
                foreach ($arguments as $k => $v) {
                    if ($k !== 0 && $k % 2 == 0) {
                        $arguments[$k] = $this->encode($v);
                    }
                }
            }
        }
        
        return $arguments;
    }
    
    /**
     * Decode the Response for a given Command.
     * 
     * @param  string  $commandID
     * @param  string  $response
     * @return mixed
     */
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
     * @param  array  $arguments
     */
    abstract protected function callClient($method, $arguments);
    
    /**
     * Dynamically make a Redis command.
     *
     * @param  string  $method
     * @param  array  $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        return $this->process($method, $arguments);
    }
}
