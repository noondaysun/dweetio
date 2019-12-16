<?php

declare(strict_types=1);

namespace Noondaysun\Dweetio;

use DateTimeImmutable;
use GuzzleHttp\Client;
use Monolog\Logger;

/**
 * @package Dweetio
 *
 * @author Feighen Oosterbroek <feighen@noondaysun.org>
 *
 * @license GNU GPL v2
 */
class Dweetio_Client
{
    /** @var string */
    protected $baseUri = 'https://dweet.io:443';

    /** @var Client client */
    protected $client;

    /** @var Logger */
    protected $logger;

    /** @var string */
    protected $thing;

    /** @var string */
    protected $lock;

    /** @var string */
    protected $key;

    /** @var array */
    protected $content;

    /** @var bool */
    protected $quietly;

    /**
     * @var DateTimeImmutable The calendar date (YYYY-MM-DD) from which you'd like to start your query.
     *      The response will be a maximum of one day.
     *      must be formatted YYYY-mm-dd which is why I've manually converted it to that format
     */
    protected $date;

    /**
     * @var string The hour of the day represented in the date parameter in 24-hour (00-23) format.
     *      If this parameter is included, a maximum of 1 hour will be returned starting at this hour.
     */
    protected $hour;

    /**
     * @param array $params
     *            [
     *            'thing' => 'billy-bobs-battery-operated-billy-goat'
     *            ]
     */
    public function __construct(array $params = [])
    {
        $this->client = new Client();
        $this->logger = new Logger('Dweetio');

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
    }

    /**
     * @return string
     */
    public function getBaseUri(): string
    {
        return $this->baseUri;
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @return array
     */
    public function getContent(): array
    {
        return $this->content;
    }

    /**
     * @return bool
     */
    public function getQuietly(): bool
    {
        return $this->quietly;
    }

    /**
     * @return string
     */
    public function getThing(): string
    {
        return $this->thing;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getHour(): string
    {
        return $this->hour;
    }

    /**
     * @param string $uri
     */
    public function setBaseUri(string $uri): void
    {
        $this->baseUri = $uri;
    }

    /**
     * @param Client $client
     */
    public function setClient(Client $client): void
    {
        $this->client = $client;
    }

    /**
     * @param array $content
     */
    public function setContent(array $content): void
    {
        $this->content = $content;
    }

    /**
     * @param DateTimeImmutable $date
     */
    public function setDate(DateTimeImmutable $date): void
    {
        $this->date = $date;
    }

    /**
     * @param string $hour
     */
    public function setHour(string $hour): void
    {
        $this->hour = $hour;
    }

    /**
     * @param bool $quietly
     *            IF TRUE Create a dweet for a thing. This method differs from /dweet/for/{thing} only in that
     *            successful dweets result in an HTTP 204 response rather than the typical verbose response.
     */
    public function setQuietly(bool $quietly): void
    {
        $this->quietly = $quietly;
    }

    /**
     * @param string $thing
     */
    public function setThing(string $thing): void
    {
        $this->thing = $thing;
    }

    /**
     * @return \stdClass
     */
    public function lock(): \stdClass
    {
        $test = $this->testFunctionRequirementsAreMet(
            [
                'key',
                'lock',
                'thing',
            ],
            'One of thing, lock, or key missing in call to Dweet_Client::lock().'
        );

        var_dump($test);
        $uri = (string) $this->baseUri . '/lock/' . $this->thing . '?lock=' . $$this->lock . '&key=' . $this->key;

        return $this->doRequest($uri);
    }

    /**
     * @return \stdClass
     */
    public function unlock(): \stdClass
    {
        $test = $this->testFunctionRequirementsAreMet(
            [
                'thing',
                'key',
            ],
            'Dweet_Client::unlock() requires a thing, and a key to work.'
        );

        if (get_object_vars($test)) {
            return $test;
        }

        $uri = (string) $this->baseUri . '/unlock/' . $this->thing . '?key=' . $this->key;

        return $this->doRequest($uri);
    }

    /**
     * @return \stdClass
     */
    public function removeLock(): \stdClass
    {
        $test = $this->testFunctionRequirementsAreMet(
            [
                'lock',
                'key',
            ],
            'Dweet_Client::removeLock() requires a lock, and a key to work.'
        );

        if (get_object_vars($test)) {
            return $test;
        }

        $uri = (string) $this->baseUri . '/remove/lock/' . $this->lock . '?key=' . $this->key;

        return $this->doRequest($uri);
    }

    /**
     * @return \stdClass
     */
    public function dweetFor(): \stdClass
    {
        $test = $this->testFunctionRequirementsAreMet(
            [
                'thing',
                'content',
            ],
            'Dweet_Client::dweetFor() requires a thing to write to, and content.'
        );

        if (get_object_vars($test)) {
            return $test;
        }

        $uri = (string) $this->baseUri . '/dweet/for/' . urlencode($this->thing);

        if ($this->quietly === true) {
            $uri = (string) $this->baseUri . '/dweet/quietly/for/' . urlencode($this->thing);
        }

        if ($this->key) {
            $uri .= '?key=' . $this->key;
        }

        return $this->doRequest(
            $uri,
            [
                'json' => $this->content,
            ]
        );
    }

    /**
     * @return \stdClass
     */
    public function getLatestDweetFor(): \stdClass
    {
        $test = $this->testFunctionRequirementsAreMet(
            [
                'thing',
            ],
            'Dweet_Client::getLatestDweetFor() requires a thing to search for.'
        );

        if (get_object_vars($test)) {
            return $test;
        }

        $uri = (string) $this->baseUri . '/get/latest/dweet/for/' . urlencode($this->thing);

        if ($this->key) {
            $uri .= '?key=' . $this->key;
        }

        return $this->doRequest($uri);
    }

    /**
     * @return \stdClass
     */
    public function getDweetsFor(): \stdClass
    {
        $test = $this->testFunctionRequirementsAreMet(
            [
                'thing',
            ],
            'Dweet_Client::getDweetFor() requires a thing to search for.'
        );

        if (get_object_vars($test)) {
            return $test;
        }

        $uri = (string) $this->baseUri . '/get/dweets/for/' . urlencode($this->thing);

        if ($this->key) {
            $uri .= '?key=' . $this->key;
        }

        return $this->doRequest($uri);
    }

    /**
     * @return \stdClass
     */
    public function listenForDweetsFrom(): \stdClass
    {
        $test = $this->testFunctionRequirementsAreMet(
            [
                'thing',
            ],
            'Dweet_Client::listenForDweetsFrom() requires a thing to search for.'
        );

        if (get_object_vars($test)) {
            return $test;
        }

        $uri = (string) $this->baseUri . '/listen/for/dweets/from/' . urlencode($this->thing);

        return $this->doRequest($uri);
    }

    /**
     * Read all the saved dweets for a thing from long term storage.
     * You can query a maximum of 1 day per request and a granularly of 1 hour.
     *
     * @return \stdClass
     */
    public function getStoredDweetsFor(): \stdClass
    {
        $test = $this->testFunctionRequirementsAreMet(
            [
                'thing',
                'key',
                'date',
            ],
            'Dweet_Client::getStoredDweetsFor() requires a thing to search for, and a date to search on.'
        );

        if (get_object_vars($test)) {
            return $test;
        }

        $uri = (string) $this->baseUri . '/get/stored/dweets/for/' . $this->thing . '?key=' . $this->key;
        $uri .= '&date=' . $this->date->format('Y-m-d');

        if ($this->hour) {
            $uri .= '&hour=' . $this->hour;
        }

        return $this->doRequest($uri);
    }

    /**
     * @return \stdClass
     */
    public function getStoredAlertsFor(): \stdClass
    {
        $test = $this->testFunctionRequirementsAreMet(
            [
                'thing',
                'key',
                'date',
            ],
            'Dweet_Client::getStoredAlertsFor() requires a thing to search for, and a date to search on.'
        );

        if (get_object_vars($test)) {
            return $test;
        }

        $uri = (string) $this->baseUri . '/get/stored/alerts/for/' . $this->thing . '?key=' . $this->key;
        $uri .= '&date=' . $this->date->format('Y-m-d');

        if ($this->hour) {
            $uri .= '&hour=' . $this->hour;
        }

        return $this->doRequest($uri);
    }

    public function alert(string $who, string $when, string $condition, string $key = ''): \stdClass
    {
        $uri = (string) $this->baseUri . '';
    }

    public function getAlertFor(): \stdClass
    {
        $uri = (string) $this->baseUri . '';
    }

    public function removeAlertFor(): \stdClass
    {
        $uri = (string) $this->baseUri . '';
    }

    /**
     * Actually do the work - Calls GuzzleHTTPClient->request to do the heavy lifting
     *
     * @param string $url
     * @param array $postData
     *
     * @return \stdClass
     */
    private function doRequest(string $url, array $postData = []): \stdClass
    {
        try {
            $res = $this->client->request('GET', $url, $postData);

            if (($res->getStatusCode() === 200) || ($res->getStatusCode() === 204)) {
                $body = json_decode($res->getBody());

                if ($body->this === 'failed') {
                    $this->logger->addError('Error: ' . $body->because);
                    $response = new \stdClass();
                    $response->this = 'failed';
                    $response->because = $body->because;
                    return $response;
                }

                if (isset($body->code) && ($body->code === 'InvalidContent')) {
                    $this->logger->addError('Error: ' . $body->message);
                    $response = new \stdClass();
                    $response->this = 'failed';
                    $response->because = $body->message;
                    return $response;
                }

                return $body;
            } else {
                $str = (string) 'Invalid response code returned. HTTP Response code returned: ' . $res->getStatusCode();
                $this->logger->addError($str);
                $response = new \stdClass();
                $response->this = 'failed';
                $response->because = $str;

                return $response;
            }
        } catch (\GuzzleHttp\Exception\ClientException $exception) {
            $this->logger->addError($exception->getMessage());
            $response = new \stdClass();
            $response->this = 'failed';
            $response->because = $exception->getMessage();

            return $response;
        }
    }

    /**
     * @param array $required
     * @param string $message
     *
     * @return \stdClass
     */
    private function testFunctionRequirementsAreMet(array $required, string $message): \stdClass
    {
        $return_value = (bool) false;
        $flipped = array_flip($required);

        if (array_key_exists('thing', $flipped) && ! $this->thing) {
            $return_value = true;
        }

        if (array_key_exists('lock', $flipped) && ! $this->lock) {
            $return_value = true;
        }

        if (array_key_exists('key', $flipped) && ! $this->key) {
            $return_value = true;
        }

        if (array_key_exists('content', $flipped) && ! $this->content) {
            $return_value = true;
        }

        if (array_key_exists('quietly', $flipped) && ! $this->quietly) {
            $return_value = true;
        }

        if ($return_value === true) {
            $this->logger->addError($message);
            $response = new \stdClass();
            $response->this = 'failed';
            $response->because = 'There were missing parameters.';

            return $response;
        }

        return new \stdClass();
    }
}
