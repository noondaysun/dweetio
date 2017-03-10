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

    /**
     *
     * @var string
     */
    protected $_thing;

    /**
     *
     * @var string
     */
    protected $_lock;

    /**
     *
     * @var string
     */
    protected $_key;
    // : End
    // : Public functions
    // : Magic
    /**
     * Class constructor
     * Do some setup for: HTTP Client, and Logging
     *
     * @param array $params
     *            [
     *            'thing' => 'billy-bobs-battery-operated-billy-goat'
     *            ]
     */
    public function __construct(array $params)
    {
        // : Setup
        // : Set the HTTP Client Library
        $this->_client = new \GuzzleHttp\Client();
        // : Set our logger -> syslog
        $writer = new \Zend\Log\Writer\Syslog();
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $this->_logger = $logger;
        // : End
        // : Accessors
        
        // : End
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
     * @return string
     */
    public function getThing(): string
    {
        return $this->_thing;
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

    public function setThing(string $thing)
    {
        $this->_thing = $thing;
    }
    // : End
    // : Locks
    /**
     *
     * @param string $thing
     * @param string $lock
     * @param string $key
     * @return \stdClass
     */
    public function lock(): \stdClass
    {
        if (! $this->_key || ! $this->_lock || ! $this->_thing) {
            $this->_logger->err('One of thing, lock, or key missing in call to Dweet_Client::lock().');
            $response = new \stdClass();
            $response->this = "failed";
            $response->because = "There were missing parameters.";
            return $response;
        }
        $uri = (string) $this->_baseUri . '/lock/' . $this->_thing . '?lock=' . $$this->_lock . '&key=' . $this->_key;
        return $this->doRequest($uri);
    }

    /**
     *
     * @param string $thing
     * @param string $key
     * @return \stdClass
     */
    public function unlock(string $thing, string $key): \stdClass
    {
        $uri = (string) $this->_baseUri . '/unlock/' . $thing . '?key=' . $key;
        return $this->doRequest($uri);
    }

    /**
     *
     * @param string $lock
     * @param string $key
     * @return \stdClass
     */
    public function removeLock(string $lock, string $key): \stdClass
    {
        $uri = (string) $this->_baseUri . '/remove/lock/' . $lock . '?key=' . $key;
        if ($key) {}
        return $this->doRequest($uri);
    }
    // : End
    // : Dweets
    /**
     *
     * @param string $thing
     * @param array $content
     * @param string $key
     * @param bool $quietly
     *            IF TRUE Create a dweet for a thing. This method differs from /dweet/for/{thing} only in that successful dweets result in an HTTP 204 response rather than the typical verbose response.
     * @return \stdClass
     */
    public function dweetFor(string $thing, array $content, string $key = '', bool $quietly = false): \stdClass
    {
        $uri = (string) $this->_baseUri . '/dweet/for/' . urlencode($thing);
        if ($quietly === true) {
            $uri = (string) $this->_baseUri . '/dweet/quietly/for/' . urlencode($thing);
        }
        if ($key) {
            $uri .= '?key=' . $key;
        }
        return $this->doRequest($uri, [
            'json' => $content
        ]);
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
        return $this->doRequest($uri);
    }

    /**
     *
     * @param string $thing
     * @return \stdClass
     */
    public function getDweetsFor(string $thing): \stdClass
    {
        $uri = (string) $this->_baseUri . '/get/dweets/for/' . urlencode($thing);
        return $this->doRequest($uri);
    }

    public function listenForDweetsFrom(string $thing): \stdClass
    {
        $uri = (string) $this->_baseUri . '/listen/for/dweets/from/' . $thing;
        return $this->doRequest($uri);
    }
    // : End
    // : Storage
    /**
     * Read all the saved dweets for a thing from long term storage.
     * You can query a maximum of 1 day per request and a granularly of 1 hour.
     *
     * @param string $thing
     * @param string $key
     * @param string $date
     *            The calendar date (YYYY-MM-DD) from which you'd like to start your query. The response will be a maximum of one day.
     *            must be formatted YYYY-mm-dd which is why I've manually converted it to that format
     * @param string $hour
     *            The hour of the day represented in the date parameter in 24-hour (00-23) format. If this parameter is included, a maximum of 1 hour will be returned starting at this hour.
     * @return \stdClass
     */
    public function getStoredDweetsFor(string $thing, string $key, string $date, string $hour = ''): \stdClass
    {
        $date = (string) date('Y-m-d', strtotime($date));
        $uri = (string) $this->_baseUri . '/get/stored/dweets/for/' . $thing . '?key=' . $key . '&date=' . $date;
        if ($hour) {
            $uri .= '&hour=' . $hour;
        }
        return $this->doRequest($uri);
    }

    public function getStoredAlertsFor(string $thing): mixed
    {}
    // : End
    // : Alerts
    public function alert(string $who, string $when, string $condition, string $key = ""): mixed
    {
        $uri = (string) $this->_baseUri . '';
    }

    public function getAlertFor(string $thing): mixed
    {
        $uri = (string) $this->_baseUri . '';
    }

    public function removeAlertFor(string $thing): mixed
    {
        $uri = (string) $this->_baseUri . '';
    }
    // : End
    // : End
    // : Private functions
    /**
     * Actually do the work - Calls GuzzleHTTPClient->request to do the heavy lifting
     *
     * @param string $url
     * @param array $postData
     * @return \stdClass
     */
    private function doRequest(string $url, array $postData = []): \stdClass
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
                $this->_logger->err("Invalid response code returned. HTTP Response code returned: " . $res->getStatusCode());
                $response = new \stdClass();
                $response->this = "failed";
                $response->because = "Invalid response code returned. HTTP Response code returned: " . $res->getStatusCode();
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
    // : End
}
