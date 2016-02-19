<?php namespace Xredis;

use Xredis\Codec\JsonCodec;

class JClient extends Client
{
    use JsonCodec;
}
