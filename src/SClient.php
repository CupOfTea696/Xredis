<?php namespace CupOfTea\Xredis;

use CupOfTea\Xredis\Codec\SerializeCodec;

class SClient extends Client
{
    use SerializeCodec;
}
