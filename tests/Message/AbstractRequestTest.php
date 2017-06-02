<?php

namespace Omnipay\AcquiroPay\Tests;

use Mockery;
use Mockery\Mock;
use Omnipay\AcquiroPay\Message\AbstractRequest;
use Omnipay\Common\CreditCard;
use Omnipay\Tests\TestCase;

class AbstractRequestTest extends TestCase
{
    /** @var AbstractRequest|Mock */
    protected $request;

    public function setUp()
    {
        $this->request = Mockery::mock('\Omnipay\AcquiroPay\Message\AbstractRequest')->makePartial();
        $this->request->initialize();
    }

    public function testCardReference()
    {
        $this->assertSame($this->request, $this->request->setCardReference('abc123'));
        $this->assertSame('abc123', $this->request->getCardReference());
    }

    public function testCardToken()
    {
        $this->assertSame($this->request, $this->request->setToken('abc123'));
        $this->assertSame('abc123', $this->request->getToken());
    }

    public function testCardData()
    {
        $card = new CreditCard($this->getValidCard());
        $this->request->setCard($card);
        // if card data is not valid, the InvalidCreditCardException will be thrown
        $this->request->getCard()->validate();
        $this->assertSame($card, $this->request->getCard());
    }
}