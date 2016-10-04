<?php

namespace Lupka\Printful;

use GuzzleHttp\Client;

use Lupka\Printful\Exceptions\PrintfulApiException;
use Lupka\Printful\Exceptions\PrintfulException;

class PrintfulClient
{
    /*
     * Printful API Key
     *
     * @var string
     */
    private $key;

    /*
     * API base URL
     *
     * @var string
     */
    private $base_url;

    /*
     * Guzzle client instance
     *
     * @var string
     */
    private $client;

    /**
     * Create a new Printful client instance
     */
    public function __construct()
    {
        $key = config('printful.api_key');
        if(strlen($key) < 32){
            throw new PrintfulException('Missing or invalid Printful store key!');
        }
        $this->key = $key;

        $this->base_url = config('printful.api_url');

        $this->client = new Client([
            'headers' => ['Authorization' => ['Basic '.base64_encode($this->key)]],
            'base_uri' => $this->base_url,
        ]);
    }

    /**
     * Perform request, throw errors if needed, return decoded data
     *
     * @param string    $method
     * @param string    $action
     *
     * @return mixed
     */
    public function request($method, $action)
    {
        $response = $this->client->request($method, $action);

        if($response->getStatusCode() != 200){
            throw new PrintfulApiException($response->getReasonPhrase(), $response->getStatusCode());
        }

        return json_decode($response->getBody())->result;
    }

    /**
     * Returns list of Products available in the Printful
     *
     * @return array
     */
    public function getProducts()
    {
        return $this->request('GET', 'products');
    }
}
