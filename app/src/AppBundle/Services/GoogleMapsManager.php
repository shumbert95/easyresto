<?php

namespace AppBundle\Services;

use GuzzleHttp\Client;



class GoogleMapsManager {

    protected $api_key;
    protected $api_host;
    protected $oauth_client;
    protected $oauth_secret;

    public function __construct($api_key, $api_host){
        $this->api_key = $api_key;
        $this->api_host = $api_host;
    }

    public function findBooks($keywords) {
        $opt = ['base_uri' => 'www.googleapis.com'];
        $client = new Client($opt);
        $request_url = 'https://www.googleapis.com/books/v1/volumes?q='.$keywords.'&maxResults=40';
        $response = $client->request('GET', $request_url);

        return json_decode($response->getBody()->getContents());
    }
}