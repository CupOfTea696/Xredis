<?php namespace Xredis;

use Xredis\Codec\Codec;
use Predis\Client as Pclient;
use Predis\Command\CommandInterface;

abstract class Client
{
    use Codec;
    
    protected $client;
    
    public function __construct(Pclient $client)
    {
        $this->client = $client;
    }
    
    public function createCommand($commandID, $parameters = [])
    {
        $commandID = strtoupper($commandID);
        
        return $this->client->createCommand($commandID, $this->encodeParametersForCommand($commandID, $parameters));
    }
    
    public function executeCommand(CommandInterface $command)
    {
        $commandID = $command->getId();
        
        return $this->decodeResponseForCommand($commandID, $this->client->executeCommand($command));
    }
    
    abstract protected function encode($v);
    
    abstract protected function decode($v);
    
    /**
     * Call the client.
     * 
     * @param  string  $method
     * @param  array  $parameters
     */
    protected function callClient($method, $parameters)
    {
        return call_user_func_array([$this->client, $method], $parameters);
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
