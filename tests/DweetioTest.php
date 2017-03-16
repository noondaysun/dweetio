<?php
namespace Noondaysun\Dweetio\Tests;

$base = (string) substr(dirname(realpath(__FILE__)), 0, strpos(dirname(realpath(__FILE__)), 'dweetio'));
$base .= DIRECTORY_SEPARATOR . 'dweetio' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR;
defined('BASE') || define('BASE', $base);

include_once BASE . 'Dweetio.php';

/**
 * Tesing that we can get a successful post/get to and from https://dweet.io using mocked objects
 *
 * @package Tests
 */
class DweetioTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     * @var \Noondaysun\Dweetio\Dweetio_Client
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
        $this->_dweet = new \Noondaysun\Dweetio\Dweetio_Client();
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

    public function testGettingLatestDweetsForFailure()
    {
        if (! $this->_dweet) {
            $this->setClient();
        }
        $this->_dweet->setThing($this->_thing);
        $live_response = $this->_dweet->getLatestDweetFor();
        
        $response = new \stdClass();
        $response->this = 'failed';
        $response->because = 'jhgfk';
        
        $this->assertEquals($response->this, $live_response->this);
        $this->assertObjectHasAttribute('because', $response);
        $this->assertObjectHasAttribute('because', $live_response);
    }
}