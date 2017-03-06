<?php
namespace Noondaysun\Dweetio\Tests;

use Noondaysun\Dweetio\Dweetio_Client;

/**
 * Tesing that we can get a successful post/get to and from https://dweet.io using mocked objects
 *
 * @package Tests
 */
class DweetioTest extends \PHPUnit_Framework_TestCase
{

    public $_dweet;

    /**
     *
     * @return Dweetio_Client
     */
    public function getClient(): Dweetio_Client
    {
        return $this->_dweet;
    }

    /**
     *
     * @param Dweetio_Client $client
     */
    public function setClient()
    {
        $this->_dweet = $this->getMockBuilder('Dweetio_Client')->getMock();
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
}