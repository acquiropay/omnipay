<?php

namespace Omnipay\AcquiroPay\Tests\Unit\Message;

use Omnipay\AcquiroPay\Message\AuthorizeRequest;
use Omnipay\Common\CreditCard;
use Omnipay\Tests\TestCase;

class AuthorizeRequestTest extends TestCase
{
    /** @var AuthorizeRequest */
    private $request;

    private $productId;

    public function setUp()
    {
        parent::setUp();

        $this->request = new AuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->productId = mt_rand(1, 1000);
    }

    public function testGetData()
    {
        // Firstly, we test data without optional parameters
        $this->request
            ->setMerchantId('merchant-1')
            ->setProductId($this->productId)
            ->setSecretWord('secret-3')
            ->setAmount('10.00')
            ->setCard($card = new CreditCard($this->getValidCard()))
            ->setTransactionId('foo')
            ->setClientIp('127.0.0.1')
            ->setReturnUrl('http://merchant-site.app');

        $data = $this->request->getData();

        $expected = [
            'opcode'      => 0,
            'product_id'  => $this->productId,
            'amount'      => '10.00',
            'cf'          => 'foo',
            'ip_address'  => '127.0.0.1',
            'pan'         => $card->getNumber(),
            'cardholder'  => $card->getName(),
            'exp_month'   => $card->getExpiryMonth(),
            'exp_year'    => $card->getExpiryYear(),
            'cvv'         => $card->getCvv(),
            'pp_identity' => 'card',
            'token'       => md5('merchant-1'.$this->productId.'10.00'.'foo'.'secret-3'),
        ];

        $this->assertSame($expected, $data);

        // Ensure that optional parameters not in data array
        $this->assertArrayNotHasKey('phone', $data);
        $this->assertArrayNotHasKey('cf2', $data);
        $this->assertArrayNotHasKey('cf3', $data);
        $this->assertArrayNotHasKey('cb_url', $data);

        // Now we set optional parameters and check them
        $this->request
            ->setPhone('+74951234567')
            ->setCf2('cf2_foo')
            ->setCf3('cf3_foo')
            ->setCallbackUrl('https://merchant-site.app/callback');

        $data = $this->request->getData();

        $expected['phone'] = '+74951234567';
        $expected['cf2'] = 'cf2_foo';
        $expected['cf3'] = 'cf3_foo';
        $expected['cb_url'] = 'https://merchant-site.app/callback';
        $expected['token'] = md5('merchant-1'.$this->productId.'10.00'.'foo'.'+74951234567'.'secret-3');

        $this->assertSame($expected, $data);
    }

    /**
     * @dataProvider amounts
     *
     * @param $amount
     */
    public function testGetRequestToken($amount)
    {
        $merchantId = mt_rand(1, 1000);
        $secretWord = uniqid();
        $transactionId = uniqid();

        $this->request
            ->setMerchantId($merchantId)
            ->setProductId($this->productId)
            ->setSecretWord($secretWord)
            ->setAmount($amount)
            ->setTransactionId($transactionId);

        $token = md5($merchantId.$this->productId.$amount.$transactionId.$secretWord);

        $this->assertSame($token, $this->request->getRequestToken());
    }

    public function testSendPreauthorizationSuccess()
    {
        $this->setMockHttpResponse('AuthorizePreauthorizationSuccess.txt');

        $card = new CreditCard([
            'firstName'   => 'CARD',
            'lastName'    => 'HOLDER',
            'number'      => '4000000000000002',
            'expiryMonth' => 12,
            'expiryYear'  => '2999',
            'cvv'         => 123,
        ]);

        $this->request
            ->setProductId($this->productId)
            ->setTestMode(true)
            ->setAmount('10.00')
            ->setCard($card)
            ->setTransactionId($transactionId = uniqid())
            ->setClientIp('127.0.0.1')
            ->setReturnUrl('http://merchant-site.app');

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNotNull($response->getTransactionReference());
        $this->assertSame('PREAUTHORIZATION', $response->getStatus());
    }

    public function testSend3DSecureSuccess()
    {
        $this->setMockHttpResponse('Authorize3DSecureSuccess.txt');

        $card = new CreditCard([
            'firstName'   => 'CARD',
            'lastName'    => 'HOLDER',
            'number'      => '4000000000000002',
            'expiryMonth' => 12,
            'expiryYear'  => '2999',
            'cvv'         => 123,
        ]);

        $this->request
            ->setProductId($this->productId)
            ->setTestMode(true)
            ->setAmount('10.00')
            ->setCard($card)
            ->setTransactionId($transactionId = uniqid())
            ->setClientIp('127.0.0.1')
            ->setReturnUrl('http://merchant-site.app');

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertSame(
            'https://3dstest.mdmbank.ru/way4acs/pa?id=WTVX31EpSSdYKeOL_iKxVQ',
            $response->getRedirectUrl()
        );
        $this->assertSame('POST', $response->getRedirectMethod());
        $this->assertSame(
            ['PaReq' => 'foo', 'MD' => 'bar', 'TermUrl' => 'http://merchant-site.app'],
            $response->getRedirectData()
        );

        $this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response->getRedirectResponse());
        $this->assertNotNull($response->getTransactionReference());
        $this->assertSame('3DSECURE', $response->getStatus());
    }

    public function testSendError()
    {
        $this->setMockHttpResponse('AuthorizeFailure.txt');

        $card = new CreditCard([
            'firstName'   => 'CARD',
            'lastName'    => 'HOLDER',
            'number'      => '4000000000000002',
            'expiryMonth' => 12,
            'expiryYear'  => '2999',
            'cvv'         => 123,
        ]);

        $this->request
            ->setProductId($this->productId)
            ->setTestMode(true)
            ->setAmount('10.00')
            ->setCard($card)
            ->setTransactionId($transactionId = uniqid())
            ->setClientIp('127.0.0.1')
            ->setReturnUrl('http://merchant-site.app');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
    }

    public function amounts()
    {
        return [
            ['10.00'],
            ['10.50'],
            ['14500.00'],
            ['11500.53'],
        ];
    }
}
