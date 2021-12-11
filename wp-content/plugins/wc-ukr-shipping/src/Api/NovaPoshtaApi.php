<?php

namespace kirillbdev\WCUkrShipping\Api;

use kirillbdev\WCUkrShipping\Contracts\HttpClient;
use kirillbdev\WCUkrShipping\Exceptions\ApiServiceException;
use kirillbdev\WCUkrShipping\Http\WpHttpClient;

if ( ! defined('ABSPATH')) {
    exit;
}

class NovaPoshtaApi
{
    /**
     * @var string
     */
    private $apiUrl = 'https://api.novaposhta.ua/v2.0/json/';

    /**
     * @var HttpClient
     */
    private $client;

    /**
     * @var string
     */
    private $apiKey;

    public function __construct()
    {
        $this->client = wcus_container()->make(WpHttpClient::class);
        $this->apiKey = get_option('wc_ukr_shipping_np_api_key', '');
    }

    public function getAreas()
    {
        $data['modelName'] = 'Address';
        $data['calledMethod'] = 'getAreas';
        $data['apiKey'] = $this->apiKey;

        return $this->sendRequest($data);
    }

    public function getCities($page)
    {
        $data['modelName'] = 'Address';
        $data['calledMethod'] = 'getCities';
        $data['apiKey'] = $this->apiKey;
        $data['methodProperties'] = [
            'Page' => $page,
            'Limit' => apply_filters('wcus_api_city_limit', 500)
        ];

        return $this->sendRequest($data);
    }

    public function getWarehouses($page)
    {
        $data['modelName'] = 'AddressGeneral';
        $data['calledMethod'] = 'getWarehouses';
        $data['apiKey'] = $this->apiKey;
        $data['methodProperties'] = [
            'Page' => $page,
            'Limit' => apply_filters('wcus_api_warehouse_limit', 500)
        ];

        return $this->sendRequest($data);
    }

    /**
     * @param array $data
     * @return mixed
     *
     * @throws ApiServiceException
     */
    private function sendRequest($data)
    {
        $result = $this->client->post(
            $this->apiUrl,
            json_encode($data),
            [ 'Content-Type' => 'application/json' ]
        );

        return json_decode($result, true);
    }
}