<?php

namespace PHPSTORM_META {

    /** @noinspection PhpIllegalArrayKeyTypeInspection */
    /** @noinspection PhpUnusedLocalVariableInspection */
    $STATIC_METHOD_TYPES = [
        \Omnipay\Omnipay::create('') => [
            'AcquiroPay' instanceof \Omnipay\AcquiroPay\Gateway,
        ],
        \Omnipay\Common\GatewayFactory::create('') => [
            'AcquiroPay' instanceof \Omnipay\AcquiroPay\Gateway,
        ],
    ];
}
