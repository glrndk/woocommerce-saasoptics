<?php
class SaasOpticsClient
{
    private $api_key;
    private $api_url;

    public function __construct($api_key = '', $api_url = '')
    {
        $this->api_key = $api_key;
        $this->api_url = $api_url;
    }

    public function post($endpoint, array $args = [])
    {
        $args['method'] = 'POST';
        $args['headers'] = ['Authorization' => 'Token ' . $this->api_key, 'Content-Type' => 'application/json'];
        return (new WP_Http())->request($this->api_url . $endpoint, $args);
    }

    public function get($endpoint, array $args = [])
    {
        $api_url = $this->api_url;
        $api_key = $this->api_key;
        $args['method'] = 'GET';
        $args['headers'] = ['Authorization' => 'Token ' . $this->api_key, 'Content-Type' => 'application/json'];
        return (new WP_Http())->request($this->api_url . $endpoint, $args);
    }
}