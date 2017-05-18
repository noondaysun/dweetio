<?php
namespace Noondaysun\Dweetio;

/**
 * PHP object to interact with https://dweet.io
 */
// : Includes
$base = (string) substr(dirname(realpath(__FILE__)), 0, - 3);
include_once $base . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

/**
 * PHP object to interact with https://dweet.io
 *
 * @package Dweetio
 * @author Feighen Oosterbroek <feighen@noondaysun.org>
 * @license GNU GPL v2
 *
 */
class DweetioClient
{

    /**
     *
     * @var string
     */
    protected $baseuri = "https://dweet.io:443";

    /**
     *
     * @var \GuzzleHttp\Client _client
     */
    protected $client;

    /**
     *
     * @var \Zend\Log\Logger
     */
    protected $logger;

    // : Accessors
    /**
     *
     * @return string
     */
    public function getBaseUri(): string
    {
        return $this->baseuri;
    }

    /**
     *
     * @return \GuzzleHttp\Client
     */
    public function getClient(): \GuzzleHttp\Client
    {
        return $this->client;
    }

    /**
     *
     * @return \Zend\Log\Logger
     */
    public function getLogger(): \Zend\Log\Logger
    {
        return $this->logger;
    }

    /**
     *
     * @param string $uri
     */
    public function setBaseUri(string $uri)
    {
        $this->baseuri = $uri;
    }

    /**
     *
     * @param \GuzzleHttp\Client $client
     */
    public function setClient(\GuzzleHttp\Client $client)
    {
        $this->client = $client;
    }

    /**
     *
     * @param
     *            \Zend\Log\Logger
     */
    public function setLogger(\Zend\Log\Logger $logger)
    {
        $this->logger = $logger;
    }

    // : Functions
    /**
     * Actually do the work - Calls GuzzleHTTPClient->request to do the heavy lifting
     *
     * @param string $url
     * @param array $postData
     * @return \stdClass
     */
    public function doRequest(string $url, array $postData = []): \stdClass
    {
        try {
            $res = $this->_client->request('GET', $url, $postData);
            if (($res->getStatusCode() === 200) || ($res->getStatusCode() === 204)) {
                $body = json_decode($res->getBody());
                if ($body->this === "failed") {
                    $this->_logger->err("Error: " . $body->because);
                    $response = new \stdClass();
                    $response->this = "failed";
                    $response->because = $body->because;
                    return $response;
                } elseif (isset($body->code) && ($body->code === "InvalidContent")) {
                    $this->_logger->err("Error: " . $body->message);
                    $response = new \stdClass();
                    $response->this = "failed";
                    $response->because = $body->message;
                    return $response;
                }
                return $body;
            } else {
                $str = (string) "Invalid response code returned. HTTP Response code returned: " . $res->getStatusCode();
                $this->_logger->err($str);
                $response = new \stdClass();
                $response->this = "failed";
                $response->because = $str;
                return $response;
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $this->_logger->err($e->getMessage());
            $response = new \stdClass();
            $response->this = "failed";
            $response->because = $e->getMessage();
            return $response;
        }
    }
}
