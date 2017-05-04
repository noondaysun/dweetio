<?php
namespace Noondaysun\Dweetio;

/**
 * PHP object to interact with https://dweet.io
 */

// : Includes
$base = (string) substr(dirname(realpath(__FILE__)), 0, - 3);
include_once $base . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

// : End

/**
 *
 * @package Dweetio
 * @author Feighen Oosterbroek <feighen@noondaysun.org>
 * @license GNU GPL v2
 *
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

    /**
     *
     * @var array
     */
    protected $_content;

    /**
     *
     * @var bool
     */
    protected $_quietly;

    /**
     *
     * @var string The calendar date (YYYY-MM-DD) from which you'd like to start your query.
     *      The response will be a maximum of one day.
     *      must be formatted YYYY-mm-dd which is why I've manually converted it to that format
     */
    protected $_date;

    /**
     *
     * @var string The hour of the day represented in the date parameter in 24-hour (00-23) format.
     *      If this parameter is included, a maximum of 1 hour will be returned starting at this hour.
     */
    protected $_hour;

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
    public function __construct(array $params = array())
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
        if (array_key_exists('thing', $params)) {
            $this->setThing($params['thing']);
        }
        if (array_key_exists('lock', $params)) {
            $this->setLock($params['lock']);
        }
        if (array_key_exists('key', $params)) {
            $this->setKey($params['key']);
        }
        if (array_key_exists('content', $params)) {
            $this->setContent($params['content']);
        }
        if (array_key_exists('quietly', $params)) {
            $this->setQuietly($params['quietly']);
        }
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
     * @return array
     */
    public function getContent(): array
    {
        return $this->_content;
    }

    /**
     *
     * @return bool
     */
    public function getQuietly(): bool
    {
        return $this->_quietly;
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
     * @return string
     */
    public function getDate(): string
    {
        return $this->_date;
    }

    /**
     *
     * @return string
     */
    public function getHour(): string
    {
        return $this->_hour;
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

    /**
     *
     * @param array $content
     */
    public function setContent(array $content)
    {
        $this->_content = $content;
    }

    /**
     *
     * @param string $date
     */
    public function setDate(string $date)
    {
        $this->_date = $date;
    }

    /**
     *
     * @param string $hour
     */
    public function setHour(string $hour)
    {
        $this->_hour = $hour;
    }

    /**
     *
     * @param bool $quietly
     *            IF TRUE Create a dweet for a thing. This method differs from /dweet/for/{thing} only in that
     *            successful dweets result in an HTTP 204 response rather than the typical verbose response.
     */
    public function setQuietly(bool $quietly)
    {
        $this->_quietly = $quietly;
    }

    /**
     *
     * @param string $thing
     */
    public function setThing(string $thing)
    {
        $this->_thing = $thing;
    }

    // : End
    // : Locks
    /**
     *
     * @return \stdClass
     */
    public function lock(): \stdClass
    {
        $test = $this->testFunctionRequirementsAreMet([
            'key',
            'lock',
            'thing'
        ], 'One of thing, lock, or key missing in call to Dweet_Client::lock().');
        var_dump($test);
        $uri = (string) $this->_baseUri . '/lock/' . $this->_thing . '?lock=' . $$this->_lock . '&key=' . $this->_key;
        return $this->doRequest($uri);
    }

    /**
     *
     * @return \stdClass
     */
    public function unlock(): \stdClass
    {
        $test = $this->testFunctionRequirementsAreMet([
            'thing',
            'key'
        ], 'Dweet_Client::unlock() requires a thing, and a key to work.');
        if (get_object_vars($test)) {
            return $test;
        }
        $uri = (string) $this->_baseUri . '/unlock/' . $this->_thing . '?key=' . $this->_key;
        return $this->doRequest($uri);
    }

    /**
     *
     * @return \stdClass
     */
    public function removeLock(): \stdClass
    {
        $test = $this->testFunctionRequirementsAreMet([
            'lock',
            'key'
        ], 'Dweet_Client::removeLock() requires a lock, and a key to work.');
        if (get_object_vars($test)) {
            return $test;
        }
        $uri = (string) $this->_baseUri . '/remove/lock/' . $this->_lock . '?key=' . $this->_key;
        return $this->doRequest($uri);
    }

    // : End
    // : Dweets
    /**
     *
     * @return \stdClass
     */
    public function dweetFor(): \stdClass
    {
        $test = $this->testFunctionRequirementsAreMet([
            'thing',
            'content'
        ], 'Dweet_Client::dweetFor() requires a thing to write to, and content.');
        if (get_object_vars($test)) {
            return $test;
        }
        $uri = (string) $this->_baseUri . '/dweet/for/' . urlencode($this->_thing);
        if ($this->_quietly === true) {
            $uri = (string) $this->_baseUri . '/dweet/quietly/for/' . urlencode($this->_thing);
        }
        if ($this->_key) {
            $uri .= '?key=' . $this->_key;
        }
        return $this->doRequest($uri, [
            'json' => $this->_content
        ]);
    }

    /**
     *
     * @return \stdClass
     */
    public function getLatestDweetFor(): \stdClass
    {
        $test = $this->testFunctionRequirementsAreMet([
            'thing'
        ], 'Dweet_Client::getLatestDweetFor() requires a thing to search for.');
        if (get_object_vars($test)) {
            return $test;
        }
        
        $uri = (string) $this->_baseUri . '/get/latest/dweet/for/' . urlencode($this->_thing);
        if ($this->_key) {
            $uri .= '?key=' . $this->_key;
        }
        return $this->doRequest($uri);
    }

    /**
     *
     * @return \stdClass
     */
    public function getDweetsFor(): \stdClass
    {
        $test = $this->testFunctionRequirementsAreMet([
            'thing'
        ], 'Dweet_Client::getDweetFor() requires a thing to search for.');
        if (get_object_vars($test)) {
            return $test;
        }
        $uri = (string) $this->_baseUri . '/get/dweets/for/' . urlencode($this->_thing);
        if ($this->_key) {
            $uri .= '?key=' . $this->_key;
        }
        return $this->doRequest($uri);
    }

    /**
     *
     * @return \stdClass
     */
    public function listenForDweetsFrom(): \stdClass
    {
        $test = $this->testFunctionRequirementsAreMet([
            'thing'
        ], 'Dweet_Client::listenForDweetsFrom() requires a thing to search for.');
        if (get_object_vars($test)) {
            return $test;
        }
        $uri = (string) $this->_baseUri . '/listen/for/dweets/from/' . urlencode($this->_thing);
        return $this->doRequest($uri);
    }

    // : End
    // : Storage
    /**
     * Read all the saved dweets for a thing from long term storage.
     * You can query a maximum of 1 day per request and a granularly of 1 hour.
     *
     * @return \stdClass
     */
    public function getStoredDweetsFor(): \stdClass
    {
        $test = $this->testFunctionRequirementsAreMet([
            'thing',
            'key',
            'date'
        ], 'Dweet_Client::getStoredDweetsFor() requires a thing to search for, and a date to search on.');
        if (get_object_vars($test)) {
            return $test;
        }
        $date = (string) date('Y-m-d', strtotime($this->_date));
        $uri = (string) $this->_baseUri . '/get/stored/dweets/for/' . $this->_thing . '?key=' . $this->_key;
        $uri .= '&date=' . $date;
        if ($this->_hour) {
            $uri .= '&hour=' . $this->_hour;
        }
        return $this->doRequest($uri);
    }

    /**
     *
     * @return \stdClass
     */
    public function getStoredAlertsFor(): \stdClass
    {
        $test = $this->testFunctionRequirementsAreMet([
            'thing',
            'key',
            'date'
        ], 'Dweet_Client::getStoredAlertsFor() requires a thing to search for, and a date to search on.');
        if (get_object_vars($test)) {
            return $test;
        }
        $date = (string) date('Y-m-d', strtotime($this->_date));
        $uri = (string) $this->_baseUri . '/get/stored/alerts/for/' . $this->_thing . '?key=' . $this->_key;
        $uri .= '&date=' . $date;
        if ($this->_hour) {
            $uri .= '&hour=' . $this->_hour;
        }
        return $this->doRequest($uri);
    }

    // : End
    // : Alerts
    public function alert(string $who, string $when, string $condition, string $key = ""): \stdClass
    {
        $uri = (string) $this->_baseUri . '';
    }

    public function getAlertFor(): \stdClass
    {
        $uri = (string) $this->_baseUri . '';
    }

    public function removeAlertFor(): \stdClass
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

    /**
     *
     * @param array $required
     * @param string $message
     * @return \stdClass
     */
    private function testFunctionRequirementsAreMet(array $required, string $message): \stdClass
    {
        $return_value = (bool) false;
        $flipped = array_flip($required);
        if (array_key_exists('thing', $flipped) && ! $this->_thing) {
            $return_value = true;
        }
        if (array_key_exists('lock', $flipped) && ! $this->_lock) {
            $return_value = true;
        }
        if (array_key_exists('key', $flipped) && ! $this->_key) {
            $return_value = true;
        }
        if (array_key_exists('content', $flipped) && ! $this->_content) {
            $return_value = true;
        }
        if (array_key_exists('quietly', $flipped) && ! $this->_quietly) {
            $return_value = true;
        }
        
        if ($return_value === true) {
            $this->_logger->err($message);
            $response = new \stdClass();
            $response->this = "failed";
            $response->because = "There were missing parameters.";
            return $response;
        }
        return new \stdClass();
    }
    // : End
}
