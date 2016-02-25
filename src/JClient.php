<?php namespace CupOfTea\Xredis;

use CupOfTea\Xredis\Codec\JsonCodec;

class JClient extends Client
{
    use JsonCodec;
}
