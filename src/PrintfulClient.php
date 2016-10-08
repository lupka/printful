<?php

namespace Lupka\Printful;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Validator;

use Lupka\Printful\Exceptions\PrintfulException;
use Lupka\Printful\Exceptions\PrintfulApiException;
use Lupka\Printful\Exceptions\PrintfulValidationException;

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
     * @var Client
     */
    private $client;

    /*
     * Validator
     *
     * @var Validator
     */
    private $validator = null;

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
     * @param array     $data
     *
     * @return mixed
     */
    public function request($method, $action, $data = [])
    {
        if($this->validator && $this->validator->fails()){
            throw new PrintfulValidationException($this->validator->errors(), 400);
        }

        $response = $this->client->request($method, $action, ['json' => $data]);

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

    /**
     * Returns information about a specific product and a list of variants for this product.
     *
     * @param integer     $productId
     *
     * @return array
     */
    public function getVariants($productId)
    {
        return $this->request('GET', 'products/'.$productId);
    }

    /**
     * Creates a new order and optionally submits it for fulfillment
     *
     * @param array     $orderData
     *
     * @return array
     */
    public function createOrder($orderData)
    {
        return $this->request('POST', 'orders', $orderData);
    }

    /**
     * Calculate tax rate
     *
     * @param array     $recipient
     *
     * @return array
     */
    public function calculateTaxRate($recipient)
    {
        $this->validator = Validator::make($recipient, [
            'country_code' => 'required',
            'state_code' => 'required',
            'city' => 'required',
            'zip' => 'required',
        ]);
        return $this->request('POST', 'tax/rates', ['recipient' => $recipient]);
    }

}
