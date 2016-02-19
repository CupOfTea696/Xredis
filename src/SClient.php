<?php namespace Xredis;

use Xredis\Codec\SerializeCodec;

class SClient extends Client
{
    use SerializeCodec;
}
