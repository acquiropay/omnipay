<?php

namespace Omnipay\AcquiroPay\Tests;

use Omnipay\AcquiroPay\Message\AuthorizeRequest;
use Omnipay\Common\CreditCard;
use Omnipay\Tests\TestCase;

class AuthorizeRequestTest extends TestCase
{
    /** @var AuthorizeRequest */
    private $request;

    public function setUp()
    {
        parent::setUp();

        $this->request = new AuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testGetData()
    {
        // Firstly, we test data without optional parameters
        $this->request
            ->setAmount('10.00')
            ->setCard($card = new CreditCard($this->getValidCard()))
            ->setTransactionId('foo')
            ->setClientIp('127.0.0.1');

        $data = $this->request->getData();

        $this->assertSame(0, $data['opcode']);
        $this->assertSame(10.00, $data['amount']);
        $this->assertSame('foo', $data['cf']);
        $this->assertSame('127.0.0.1', $data['ip_address']);
        $this->assertSame($card->getNumber(), $data['pan']);
        $this->assertSame($card->getName(), $data['cardholder']);
        $this->assertSame($card->getExpiryMonth(), $data['exp_month']);
        $this->assertSame($card->getExpiryYear(), $data['exp_year']);
        $this->assertSame($card->getCvv(), $data['cvv']);
        $this->assertSame('card', $data['pp_identity']);

        // Ensure that optional parameters not in data array
        $this->assertArrayNotHasKey('cf2', $data);
        $this->assertArrayNotHasKey('cf3', $data);
        $this->assertArrayNotHasKey('cb_url', $data);

        // Now we set optional parameters and check them
        $this->request
            ->setCf2('cf2_foo')
            ->setCf3('cf3_foo')
            ->setCallbackUrl('https://example.com/callback');

        $data = $this->request->getData();

        $this->assertSame('cf2_foo', $data['cf2']);
        $this->assertSame('cf3_foo', $data['cf3']);
        $this->assertSame('https://example.com/callback', $data['cb_url']);
    }

    public function testSendPreauthorizationSuccess()
    {
        $this->setMockHttpResponse('AuthorizePreauthorizationSuccess.txt');

        $card = new CreditCard(array(
            'firstName' => 'CARD',
            'lastName' => 'HOLDER',
            'number' => '4000000000000002',
            'expiryMonth' => 12,
            'expiryYear' => '2999',
            'cvv' => 123,
        ));

        $this->request
            ->setTestMode(true)
            ->setAmount('10.00')
            ->setCard($card)
            ->setTransactionId($transactionId = uniqid())
            ->setClientIp('127.0.0.1');

        $response = $this->request->send();

        $data = $response->getData();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNotNull($response->getTransactionReference());
        $this->assertEquals('PREAUTHORIZATION', $data['extended_status']);
        $this->assertEquals('PREAUTHORIZATION', $data['transaction_status']);
    }

    public function testSend3DSecureSuccess()
    {
        $this->setMockHttpResponse('Authorize3DSecureSuccess.txt');

        $card = new CreditCard(array(
            'firstName' => 'CARD',
            'lastName' => 'HOLDER',
            'number' => '4000000000000002',
            'expiryMonth' => 12,
            'expiryYear' => '2999',
            'cvv' => 123,
        ));

        $this->request
            ->setTestMode(true)
            ->setAmount('10.00')
            ->setCard($card)
            ->setTransactionId($transactionId = uniqid())
            ->setClientIp('127.0.0.1');

        $response = $this->request->send();

        $data = $response->getData();

        $this->assertTrue($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertSame('https://3dstest.mdmbank.ru/way4acs/pa?id=WTVX31EpSSdYKeOL_iKxVQ',
            $response->getRedirectUrl());
        $this->assertSame('POST', $response->getRedirectMethod());
        $this->assertSame(array('PaReq' => 'foo', 'MD' => 'bar'), $response->getRedirectData());

        $this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response->getRedirectResponse());
        $this->assertNotNull($response->getTransactionReference());
        $this->assertEquals('3DSECURE', $data['extended_status']);
        $this->assertEquals('3DSECURE', $data['transaction_status']);
    }

    public function testSendError()
    {
        $this->setMockHttpResponse('AuthorizeFailure.txt');

        $card = new CreditCard(array(
            'firstName' => 'CARD',
            'lastName' => 'HOLDER',
            'number' => '4000000000000002',
            'expiryMonth' => 12,
            'expiryYear' => '2999',
            'cvv' => 123,
        ));

        $this->request
            ->setTestMode(true)
            ->setAmount('10.00')
            ->setCard($card)
            ->setTransactionId($transactionId = uniqid())
            ->setClientIp('127.0.0.1');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
    }
}
