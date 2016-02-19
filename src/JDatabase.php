<?php namespace Xredis;

use Xredis\Codec\JsonCodec;

class JDatabase extends Database
{
    use JsonCodec;
}
