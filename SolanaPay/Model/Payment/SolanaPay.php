<?php

/**
 * Copyright © @ ArmMage LLC All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace ArmMage\SolanaPay\Model\Payment;

use Magento\Payment\Model\Method\AbstractMethod;

class SolanaPay extends AbstractMethod
{

    /**
     * constant string
     */
    const METHOD_CODE = 'solanapay';

    /**
     * Payment code
     *
     * @var string
     */
    protected $_code = self::METHOD_CODE;

    /**
     * Payment Method feature
     *
     * @var bool
     */
    protected $_isOffline = false;

    /**
     * Payment Method feature
     *
     * @var bool
     */
    protected $_canCapture = true;

    /**
     * Payment Method feature
     *
     * @var bool
     */
    protected $_canRefund = false;
}
