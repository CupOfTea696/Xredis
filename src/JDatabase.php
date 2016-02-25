<?php namespace CupOfTea\Xredis;

use CupOfTea\Xredis\Codec\JsonCodec;

class JDatabase extends Database
{
    use JsonCodec;
}
