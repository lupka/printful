<?php

namespace Lupka\Printful;

use GuzzleHttp\Client;

class PrintfulClient
{
    /*
     *  Printful API Key
     *
     * @var string
     */
    private $key;

    public function __construct()
    {
        $key = Config::get('printful.api_key');
        if(strlen($key) < 32){
            throw new PrintfulException('Missing or invalid Printful store key!');
        }
        $this->key = $key;
    }
}
