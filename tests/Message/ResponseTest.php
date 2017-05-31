<?php

namespace Omnipay\AcquiroPay\Tests;

use Omnipay\AcquiroPay\Message\Response;
use Omnipay\Tests\TestCase;

/**
 * Class ResponseTest
 *
 * @method mixed getMockRequest()
 *
 * @package Omnipay\AcquiroPay\Tests
 */
class ResponseTest extends TestCase
{
    public function testConstruct()
    {
        // response should decode URL format data
        $response = new Response($this->getMockRequest(), 'example=value&foo=bar');
        $this->assertEquals(array('example' => 'value', 'foo' => 'bar'), $response->getData());
    }

    public function testProPurchaseSuccess()
    {
        $httpResponse = $this->getMockHttpResponse('AuthorizeSuccess.txt');
        $response = new Response($this->getMockRequest(), $httpResponse->getBody());

        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('1234', $response->getTransactionReference());
        $this->assertNull($response->getMessage());
    }
}
