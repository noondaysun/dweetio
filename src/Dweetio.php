<?php
namespace Noondaysun\Dweetio;

/**
 * PHP object to interact with https://dweet.io
 */

// : Includes
include_once dirname(realpath(__FILE__)) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
// : End

/**
 *
 * @author Feighen Oosterbroek <feighen@noondaysun.org>
 * @license GNU GPL v2
 * @package Dweetio
 */
class Dweetio_Client
{
    // : Variables
    /**
     *
     * @var string
     */
    protected $_baseUri = "https://dweet.io:443";

    /**
     *
     * @var \GuzzleHttp\Client _client
     */
    protected $_client;

    /**
     *
     * @var \Zend\Log\Logger
     */
    protected $_logger;
    // : End
    // : Public functions
    // : Magic
    /**
     * Class constructor
     * Do some setup for: HTTP Client, and Logging
     */
    public function __construct()
    {
        // : Set the HTTP Client Library
        $this->_client = new \GuzzleHttp\Client();
        // : Set our logger -> syslog
        $writer = new \Zend\Log\Writer\Syslog();
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $this->_logger = $logger;
    }
    // : End
    // : Accessors
    /**
     *
     * @return string
     */
    public function getBaseUri(): string
    {
        return $this->_baseUri;
    }

    /**
     *
     * @return \GuzzleHttp\Client
     */
    public function getClient(): \GuzzleHttp\Client
    {
        return $this->_client;
    }

    /**
     *
     * @param string $uri
     */
    public function setBaseUri(string $uri)
    {
        $this->_baseUri = $uri;
    }

    /**
     *
     * @param \GuzzleHttp\Client $client
     */
    public function setClient(\GuzzleHttp\Client $client)
    {
        $this->_client = $client;
    }
    // : End
    // : Locks
    public function lock(string $thing): mixed
    {
        $uri = (string) $this->_baseUri . '';
    }

    public function unlock(string $thing): mixed
    {}

    public function removeLock(string $lock): mixed
    {}
    // : End
    // : Dweets
    /**
     *
     * @param string $thing
     * @param array $content
     * @param string $key
     * @param bool $quietly
     * @return \stdClass
     */
    public function dweetFor(string $thing, array $content, string $key = '', bool $quietly): \stdClass
    {
        $uri = (string) $this->_baseUri . '/dweet/for/' . urlencode($thing);
        if ($quietly === true) {
            $uri = (string) $this->_baseUri . '/dweet/quietly/for/' . urlencode($thing);
        }
        if ($key) {
            $uri .= '?key=' . $key;
        }
        try {
            $res = $this->_client->request('POST', $uri, [
                'json' => $content
            ]);
            if ($res->getStatusCode() === 200) {
                $body = json_decode($res->getBody());
                if ($body->this === "failed") {
                    $this->_logger->err("Error: " . $body->because);
                    return new \stdClass();
                } elseif ($body->code === "InvalidContent") {
                    $this->_logger->err("Error: " . $body->message);
                    return new \stdClass();
                }
                return $body;
            } else {
                $this->_logger->err("Invalid response code returned. HTTP Response code returned: " . $res->getStatusCode());
                return new \stdClass();
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $this->_logger->err($e->getMessage());
            return new \stdClass();
        }
    }

    /**
     *
     * @param string $thing
     * @param string $key
     * @return \stdClass
     */
    public function getLatestDweetFor(string $thing, string $key = ''): \stdClass
    {
        $uri = (string) $this->_baseUri . '/get/latest/dweet/for/' . urlencode($thing);
        try {
            $res = $this->_client->request('GET', $uri, []);
            if ($res->getStatusCode() === 200) {
                $body = json_decode($res->getBody());
                if ($body->this === "failed") {
                    $this->_logger->err("Error: " . $body->because);
                    return new \stdClass();
                }
                return $body;
            } else {
                $this->_logger->err("Invalid response code returned. HTTP Response code returned: " . $res->getStatusCode());
                return new \stdClass();
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $this->_logger->err($e->getMessage());
            return new \stdClass();
        }
    }

    /**
     *
     * @param string $thing
     * @return \stdClass
     */
    public function getDweetsFor(string $thing): \stdClass
    {
        $uri = (string) $this->_baseUri . '/get/dweets/for/' . urlencode($thing);
        try {
            $res = $this->_client->request('GET', $uri, []);
            if ($res->getStatusCode() === 200) {
                $body = json_decode($res->getBody());
                if ($body->this === "failed") {
                    $this->_logger->err("Error: " . $body->because);
                    return new \stdClass();
                }
                return $body;
            } else {
                $this->_logger->err("Invalid response code returned. HTTP Response code returned: " . $res->getStatusCode());
                return new \stdClass();
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $this->_logger->err($e->getMessage());
            return new \stdClass();
        }
    }

    public function listenForDweetsFrom(string $thing): \stdClass
    {}
    // : End
    // : Storage
    public function getStoredDweetsFor(string $thing): mixed
    {}

    public function getStoredAlertsFor(string $thing): mixed
    {}
    // : End
    // : Alerts
    public function alert(string $who, string $when, string $condition, string $key = ""): mixed
    {}

    public function getAlertFor(string $thing): mixed
    {}

    public function removeAlertFor(string $thing): mixed
    {}
    // : End
    // : End
}
