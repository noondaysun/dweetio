<?php
namespace Noondaysun\Dweetio\Tests;

include_once '../src/Dweetio.php';

/**
 * Tesing that we can get a successful post/get to and from https://dweet.io using mocked objects
 *
 * @package Tests
 */
class DweetioTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     * @var Dweetio_Client
     */
    public $_dweet;

    /**
     *
     * @var string
     */
    public $_thing = 'dweetio-php-test';

    /**
     *
     * @return mixed
     */
    public function getClient()
    {
        return $this->_dweet;
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
     * @param Dweetio_Client $client
     */
    public function setClient()
    {
        $this->_dweet = $this->getMockBuilder('\Noondaysun\Dweetio\Dweetio_Client');
    }

    /**
     *
     * @param string $thing
     */
    public function setThing(string $thing)
    {
        $this->_thing = $thing;
    }

    public function testDweetingFor()
    {
        if (! $this->_dweet) {
            $this->setClient();
        }
    }

    public function testQuietlyDweetingFor()
    {
        if (! $this->_dweet) {
            $this->setClient();
        }
    }

    public function testGettingLatestDweetsForSuccess()
    {
        if (! $this->_dweet) {
            $this->setClient();
        }
        $client = $this->_dweet->setConstructorArgs(array(
            'thing' => $this->_thing
        ))->getMock();
        
        $response = new \stdClass();
        $response->this = 'succeeded';
        $response->by = 'getting';
        $response->the = 'dweets';
    }
}