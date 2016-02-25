<?php namespace CupOfTea\Xredis\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Illuminate\Redis\Database
 */
class Sredis extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Sredis';
    }
}