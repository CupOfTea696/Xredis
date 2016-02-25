<?php namespace CupOfTea\Xredis;

use Predis\Client as Pclient;
use Predis\Command\CommandInterface;
use CupOfTea\Xredis\Codec\Codec;

abstract class Client
{
    use Codec;
    
    /**
     * The redis Client.
     * 
     * @var \Predis\Client
     */
    protected $client;
    
    /**
     * Create a new Xredis Client instance.
     * 
     * @param  \Predis\Client  $client
     * @return void
     */
    public function __construct(Pclient $client)
    {
        $this->client = $client;
    }
    
    /**
     * @see \Predis\Client::createCommand
     */
    public function createCommand($commandID, $arguments = [])
    {
        $commandID = strtoupper($commandID);
        
        return $this->client->createCommand($commandID, $this->encodeArgsForCommand($commandID, $arguments));
    }
    
    /**
     * @see \Predis\Client::executeCommand
     */
    public function executeCommand(CommandInterface $command)
    {
        $commandID = $command->getId();
        
        return $this->decodeResponseForCommand($commandID, $this->client->executeCommand($command));
    }
    
    /**
     * Encode a value with the Codec.
     * 
     * @param  mixed  $v
     * @return string
     */
    abstract protected function encode($v);
    
    /**
     * Decode a value with the Codec.
     * 
     * @param  string  $v
     * @return mixed
     */
    abstract protected function decode($v);
    
    /**
     * Call the client.
     * 
     * @param  string  $method
     * @param  array  $arguments
     */
    protected function callClient($method, $arguments)
    {
        return call_user_func_array([$this->client, $method], $arguments);
    }
    
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
