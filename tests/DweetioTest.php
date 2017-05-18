<?php
namespace Noondaysun\Dweetio;

class DweetioClientTest extends \PHPUnit\Framework\TestCase
{

    public function testDoRequest_WithBadURI_RetunsAnObject()
    {
        $dweetio_client = $this->getMockBuilder('DweetioClient', [
            'doRequest'
        ]);
        var_dump($dweetio_client);
    }
}
